<?php

namespace App\EditoraFront\PageCacher;

use Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageCacher
{
    public function getPage()
    {// return page or null
        Log::debug("PageCacher getPage: ".Request::path().' '.Request::fullUrl());
        if ($this->isCacheable()) {
            $res=Cache::get(Request::path());
            if ($res) {
                Log::debug("PageCacher getPage: ".Request::path().' '." HIT!");
                return $res;
            }
            Log::debug("PageCacher getPage: ".Request::path().' '." not in cache MISS!");
            return null;
        }
        Log::debug("PageCacher getPage: ".Request::path().' '." url not cacheable MISS!");
        return null;
    }

    public function setPage($page)
    {// return page or null
        Log::debug("PageCacher setPage: ".Request::path().' '.Request::fullUrl());
        if ($this->isCacheable()) {
            Cache::put(Request::path(), $page, config('omatech.page_cacher_seconds'));
            Log::debug("PageCacher setPage: ".Request::path().' '." STORED!");
        } else {
            Log::debug("PageCacher setPage: ".Request::path().' '." url not cacheable NOT STORED!");
        }
        return null;
    }

    private function isCacheable()
    {
        $fullURL=Request::fullUrl();
        if (Str::startsWith(config('app.url'), 'http://'))
        {
            $fullURL=str_replace('https://', 'http://', $fullURL);
        }
        Log::debug('Comparing '.config('app.url').'/'.Request::path().' vs. '.$fullURL);
        return (config('omatech.page_cacher_seconds')>0
        && config('app.url').'/'.Request::path()==$fullURL);
    }
}
