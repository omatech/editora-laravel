<?php

namespace App\EditoraFront\StaticTexts;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Omatech\Editora\Connector\Models\StaticTexts;

class CachedStaticTexts
{
    private static $instances = [];
    private $messages;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): CachedStaticTexts
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
            self::$instances[$cls]->getCachedMessages();
        }

        return self::$instances[$cls];
    }

    public function get(string $key, array $replacements=[]): string
    {
        $messages=$this->messages;
        if (array_key_exists($key, $messages) && !empty($messages[$key])) {
            return $this->applyReplacements($messages[$key], $replacements);
        } else {
            return _statictext($key);
        }
    }

    private function applyReplacements(string $text, array $replacements=[]): string
    {
        if (empty($replacements)||!is_array($replacements)) {
            return $text;
        }
        foreach ($replacements as $key=>$value) {
            $text = str_replace('#'.$key.'#', $value, $text);
        }
        return $text;
    }

    private function getCachedMessages(): void
    {
        $editoraStaticTexts=new StaticTexts();
        $lang = App::getLocale();
        $cacheKey="staticTexts:$lang";
        if (config('omatech.statictexts_cacher_seconds')==0) {
            Log::debug(__CLASS__.'->'.__METHOD__."() $cacheKey - CACHE DISABLED!");
            $messages=$editoraStaticTexts->todos();
        } else {
            if (Cache::has($cacheKey)) {
                Log::debug(__CLASS__.'->'.__METHOD__."() $cacheKey - HIT!");
                $messages=Cache::get($cacheKey);
            } else {
                Log::debug(__CLASS__.'->'.__METHOD__."() $cacheKey - MISS!");
                $messages=$editoraStaticTexts->todos();
                Cache::put($cacheKey, $messages, config('omatech.statictexts_cacher_seconds'));
            }
        }
        $this->messages=$messages;
    }
}
