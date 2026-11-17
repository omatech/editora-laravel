<?php
use Omatech\Editora\Admin\Util\StoreMetadata;

class Appscrapper
{
    public function loaditunesData(?string $url = null): string|false
    {
        $data = StoreMetadata::apple($url ?? '');
        return $data === false ? false : json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function loadgoogleplayData(?string $url = null): string
    {
        return json_encode(StoreMetadata::google($url ?? ''), JSON_THROW_ON_ERROR);
    }
}
