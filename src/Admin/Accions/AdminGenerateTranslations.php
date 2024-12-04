<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;
use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\statictext;
use Illuminate\Support\Facades\Session;
use DeepL\Translator;
use Omatech\Editora\Admin\Events\EditInstance2UpdatedEvent;

class AdminGenerateTranslations extends AuthController
{
    public function __construct()
    {
        parent::__construct();

        $this->instances = new Instances;
        $this->attributes = new attributes();
        $this->statictext = new statictext();
    }

    public function render()
    {
        $params = get_params_info();
        $params['p_mode'] = $p_mode = 'V';
        $page = 1;
        $langs = $this->instances->busca_idiomes();
        $last_translated = [];

        $staticTexts = $this->statictext->get_static_text_keys();
        $staticTextsData = $this->statictext->get_static_texts_by_keys_and_langs($staticTexts, $langs);
        $this->translateStaticTexts($staticTextsData);

        $instances = $this->instances->getAllInstances();
        $translatedInstances = $this->translateInstances($instances, $langs);

        if(!empty($translatedInstances)) {
            $this->dispatchEvent($translatedInstances);

            $last_translated = array_map(function ($instance) {
                return $this->instances->getInstInfo($instance);
            }, $translatedInstances);
        }

        if ($params['param3'] != "") {
            $page = $params['param3'];
        }
        if ($params['param1'] != "") {
            $params['param1'] = "";
        }

        $count = count($last_translated);
        $menu = $this->loadMenu($this->instances, $params);
        $title = EDITORA_NAME;

        $viewData = array_merge($menu, [
            'title' => $title,
            'p_mode' => $p_mode,
            'last_translated' => $last_translated,
            'count' => $count,
            'page' => $page,
        ]);

        return response()->view('editora::pages.translated_instances', $viewData);
    }

    private function translateInstances($instances, $langs)
    {
        $excludedClassIds = explode(',', env('EXCLUDED_CLASS_IDS'));
        $translatedInstances = [];

        foreach ($instances as $instance) {
            if (!in_array($instance['class_id'], $excludedClassIds) && $instance['status'] == 'O') {
                $params['param1'] = $instance['class_id'];
                $params['param2'] = $instance['id'];
                $attributes = $this->attributes->getInstanceAttributes('U', $params);
                $textAttributes = $this->filterTextAttributes($attributes['instance_info']['instance_tabs']);
                $missingAttrLangs = $this->getMissingAttrLangs($textAttributes);
                $translated = $this->processMissingTranslations($textAttributes, $missingAttrLangs, $langs);
                if ($translated) {
                    $translatedInstances[] = $this->attributes->insertAttributeValues($translated, $instance['id']);
                }
            }
        }
        return $translatedInstances;
    }

    private function translateStaticTexts($staticTextsData)
    {
        $translatedTexts = [];
        foreach ($staticTextsData as $key => $values) {
            $text = '';
            $targetLangs = [];
            $sourceLang = [];
            foreach ($values as $value) {
                if(!empty($value['text_value'])){
                    if(empty($text) && $value['language'] != 'ca') {
                        $sourceLang[] = $value['language'];
                        $text = $value['text_value'];
                    }
                } else {
                    $targetLangs[] = $value['language'];
                }
            }

            if($text && $targetLangs && $sourceLang) {
                foreach($targetLangs as $targetLang) {
                    $translatedTexts[$key][$targetLang] = $this->translateText($text, $targetLang);
                }
            }

        }
        $this->statictext->insert_static_texts($translatedTexts);
    }

    private function processMissingTranslations($textAttributes, $missingAttrLangs, $langs)
    {
        $translated = [];

        foreach ($missingAttrLangs as $attrName => $attrInfo) {
            if (count($attrInfo) !== 7) {
                $existingLangs = array_diff($langs, array_column($attrInfo, 'lang'));
                $langToTranslateFrom = in_array('en', $existingLangs) ? 'en' :
                    (in_array('us', $existingLangs) ? 'us' :
                        (in_array('es', $existingLangs) ? 'es' :
                            (in_array('fr', $existingLangs) ? 'fr' :
                                (in_array('de', $existingLangs) ? 'de' :
                                    (in_array('jp', $existingLangs) ? 'jp' : null)))));

                if ($langToTranslateFrom) {
                    $textToTranslateFrom = $this->getTextValForLang($textAttributes, $langToTranslateFrom, $attrName);
                    foreach ($attrInfo as $info) {
                        $translated[$info['id']] = [
                            'lang' => $info['lang'],
                            'text' => $this->translateText($textToTranslateFrom['text_val'], $info['lang'])
                        ];
                    }
                }
            }
        }

        return $translated;
    }

    private function translateText($text, $targetLang)
    {
        try {
            if ($targetLang === 'ca') {
                return null;
            }
            if ($targetLang === 'us') {
                $targetLang = 'EN-US';
            }
            if ($targetLang === 'en') {
                $targetLang = 'EN-GB';
            }
            if ($targetLang === 'jp') {
                $targetLang = 'JA';
            }

            $translator = new Translator(config('editora-admin.deepl_key'));
            $result = $translator->translateText($text, null, $targetLang);
            return html_entity_decode($result->text);

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            return null;
        }
    }

    private function getTextValForLang($textAttributes, $langToTranslateFrom, $attrName)
    {
        $attrVal = [];

        foreach ($textAttributes as $textAttr) {
            if ($textAttr['name'] === $langToTranslateFrom) {
                $attrVal = array_map(function ($a) use ($attrName) {
                    if ($a['tag'] === $attrName) {
                        return $a['atrib_values'][0]['text_val'];
                    }
                }, $textAttr['elsatribs']);

                $attrVal = array_filter($attrVal, function ($value) {
                    return !is_null($value);
                });
                break;
            }
        }

        return ['lang' => $langToTranslateFrom, 'text_val' => array_values($attrVal)[0]];
    }

    private function filterTextAttributes($attributes)
    {
        $tabs_lang = array_filter($attributes, function ($value) {
            return $value['name'] !== 'data';
        });

        foreach ($tabs_lang as &$data) {
            $data['elsatribs'] = $this->filterEachLangAttributes($data['elsatribs']);
        }

        return $tabs_lang;
    }

    private function filterEachLangAttributes($elsatribs)
    {
        return array_filter($elsatribs, function ($value) {
            return in_array($value['type'], ['S', 'K']);
        });
    }

    private function getMissingAttrLangs(array $tabs_lang)
    {
        $missingLangs = [];

        foreach ($tabs_lang as &$data) {
            $data['elsatribs'] = $this->filterEachLangAttributes($data['elsatribs']);

            foreach ($data['elsatribs'] as $elsatrib) {
                if (empty($elsatrib['atrib_values']) ||
                    (!empty($elsatrib['atrib_values']) && $elsatrib['atrib_values'][0]['text_val'] === '<p>&nbsp;</p>')) {
                    $missingLangs[$elsatrib['tag']][] = [
                        'id' => $elsatrib['id'],
                        'lang' => $elsatrib['language']
                    ];
                }
            }
        }
        return $missingLangs;
    }

    private function dispatchEvent(array $instanceIds): void
    {
        try {
            foreach ($instanceIds as $instanceId) {
                if ($instanceId !== 0) {
                    Event::dispatch(new EditInstance2UpdatedEvent($instanceId));
                }
            }
        } catch (\Exception $exception) {
        }
    }
}

