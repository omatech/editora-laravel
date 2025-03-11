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
        $this->translator = new Translator(config('editora-admin.deepl_key'));
        $this->custom_target_langs = config('editora-admin.languages_for_translation') ?? [];
    }

    public function render()
    {
        $title = EDITORA_NAME;
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
        $batchTexts = [];

        foreach ($instances as $instance) {
            if (!in_array($instance['class_id'], $excludedClassIds) && $instance['status'] == 'O') {
                $params['param1'] = $instance['class_id'];
                $params['param2'] = $instance['id'];
                $attributes = $this->attributes->getInstanceAttributes('U', $params);
                $textAttributes = $this->filterTextAttributes($attributes['instance_info']['instance_tabs']);
                $missingAttrLangs = $this->getMissingAttrLangs($textAttributes);

                foreach ($missingAttrLangs as $attrName => $attrInfo) {
                    if(count($attrInfo) !== 7) {
                        $sourceLang = $this->determineSourceLang($langs, $attrInfo);
                        $textToTranslate = $this->getTextValForLang($textAttributes, $sourceLang, $attrName);
                        foreach($attrInfo as $info) {
                            if (empty($this->custom_target_langs) || in_array($info['lang'], $this->custom_target_langs)) {
                                $batchTexts[$info['lang']][] = [
                                    'instance_id' => $instance['id'],
                                    'attr_id' => $info['id'],
                                    'attr_name' => $attrName,
                                    'text' => $textToTranslate['text_val'],
                                    'source_lang' => $sourceLang,
                                ];
                            }
                        }
                    }
                }
            }
        }

        unset($batchTexts['ca']);
        $translatedAttributes = $this->processBatchTranslations($batchTexts);

        foreach($translatedAttributes as $instanceId => $translations) {
            $this->attributes->insertAttributeValues($translations, $instanceId);
            $translatedInstances[] = $instanceId;
        }
        return $translatedInstances;
    }

    private function translateStaticTexts($staticTextsData)
    {
        $batchTexts = [];
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
                    if (empty($this->custom_target_langs) || in_array($value['language'], $this->custom_target_langs)) {
                        $batchTexts[$value['language']][] = [
                            'key' => $key,
                            'text' => $text
                        ];
                    }
                }
            }
        }

        unset($batchTexts['ca']);
        foreach ($batchTexts as $targetLang => $texts) {
            $textsForBatch = array_column($texts, 'text');
            $translations = $this->translateText($textsForBatch, $targetLang);

            foreach ($translations as $index => $translation) {
                $key = $texts[$index]['key'];
                $translatedTexts[$key][$targetLang] = $translation;
            }
        }

        $this->statictext->insert_static_texts($translatedTexts);
    }

    private function determineSourceLang($langs, $langInfo)
    {
        $existingLangs = array_diff($langs, array_column($langInfo, 'lang'));
        return in_array('es', $existingLangs) ? 'es' :
            (in_array('en', $existingLangs) ? 'en' :
                (in_array('fr', $existingLangs) ? 'fr' :
                    (in_array('de', $existingLangs) ? 'de' :
                        (in_array('us', $existingLangs) ? 'us' :
                            (in_array('jp', $existingLangs) ? 'jp' : null)))));
    }

    private function processBatchTranslations($batchTexts)
    {
        $translatedAttributes = [];

        foreach($batchTexts as $targetLang => $texts) {
            $textsForBatch = array_column($texts, 'text');
            $translations = $this->translateText($textsForBatch, $targetLang);

            foreach($translations as $index => $translation) {
                $textInfo = $texts[$index];
                $translatedAttributes[$textInfo['instance_id']][] = [
                    'id' => $textInfo['attr_id'],
                    'lang' => $targetLang,
                    'text' => $translation,
                ];
            }
        }
        return $translatedAttributes;
    }


    private function translateText($texts, $targetLang)
    {
        try {
            if ($targetLang == 'ca') {
                return null;
            }
            $targetLangMap = [
                'us' => 'EN-US',
                'en' => 'EN-GB',
                'jp' => 'JA'
            ];
            $targetLang = $targetLangMap[$targetLang] ?? $targetLang;

            if(is_array($texts)) {
                $results = $this->translator->translateText(array_values($texts), null, $targetLang, ['timeout' => 100]);
                return array_map(function ($result) {
                    return html_entity_decode($result->text);
                }, $results);
            }

            $result = $this->translator->translateText($texts, null, $targetLang, ['timeout' => 100]);
            return html_entity_decode($result->text);


        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            return array_fill(0, count($texts), null);
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
