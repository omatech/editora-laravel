<?php
namespace Omatech\Editora\Admin\Util;

final class StoreMetadata
{
    public static function apple(string $url): array|false
    {
        $parts = self::storeUrl($url, ['itunes.apple.com', 'apps.apple.com']);
        if (!preg_match('~/id(\d+)(?:/|$)~', $parts['path'] ?? '', $match)) throw new \InvalidArgumentException('Invalid Apple app identifier.');
        $body = self::request('https://itunes.apple.com/lookup?'.http_build_query(['id'=>$match[1], 'country'=>'es']));
        $data = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        return $data['results'][0] ?? false;
    }

    public static function google(string $url): array
    {
        $parts = self::storeUrl($url, ['play.google.com']);
        if (($parts['path'] ?? '') !== '/store/apps/details') throw new \InvalidArgumentException('Invalid Google Play app path.');
        parse_str($parts['query'] ?? '', $query);
        $id = $query['id'] ?? null;
        if (!is_string($id) || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*(?:\.[a-zA-Z0-9_]+)+$/D', $id)) throw new \InvalidArgumentException('Invalid Google app identifier.');
        $url = 'https://play.google.com/store/apps/details?'.http_build_query(['id'=>$id, 'hl'=>'es']);
        $result = self::parseGoogle(self::request($url));
        $result['General'][0]['app_store_url'] = $url;
        return $result;
    }

    private static function storeUrl(string $url, array $hosts): array
    {
        $parts = parse_url($url);
        if (!is_array($parts) || !in_array($parts['scheme'] ?? '', ['http', 'https'], true)) throw new \InvalidArgumentException('Invalid store URL.');
        if (!in_array($parts['host'] ?? '', $hosts, true)) throw new \InvalidArgumentException('Invalid store host.');
        if (isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) throw new \InvalidArgumentException('Invalid store authority.');
        return $parts;
    }

    private static function request(string $url): string
    {
        $body = '';
        $client = curl_init($url);
        if ($client === false) throw new \RuntimeException('Cannot initialize store request.');
        $options = [CURLOPT_FOLLOWLOCATION=>false, CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>10,
            CURLOPT_PROTOCOLS_STR=>'HTTPS', CURLOPT_SSL_VERIFYPEER=>true, CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_USERAGENT=>'EditoraMetadata/1.0', CURLOPT_WRITEFUNCTION=>static function (\CurlHandle $client, string $chunk) use (&$body): int {
                if (strlen($body) + strlen($chunk) > 8388608) throw new \RuntimeException('Store response exceeds the size limit.');
                $body .= $chunk;
                return strlen($chunk);
            }];
        if (!curl_setopt_array($client, $options)) throw new \RuntimeException('Cannot configure store request.');
        if (curl_exec($client) === false || curl_getinfo($client, CURLINFO_RESPONSE_CODE) !== 200) throw new \RuntimeException('Store metadata request failed.');
        return $body;
    }

    public static function parseGoogle(string $html): array
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        try { $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NONET); }
        finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
        $schema = self::structuredData($document);
        $values = [];
        foreach (['app_title'=>['name', 'og:title'], 'text_plain_app_description'=>['description', 'og:description'], 'banner_icon'=>['image', 'og:image']] as $field=>$sources) {
            $value = $schema[$sources[0]] ?? self::meta($document, $sources[1]) ?? '';
            $values[$field] = is_string($value) ? $value : '';
        }
        if ($values['app_title'] === '') throw new \RuntimeException('Store response has no application title.');
        $values['html_app_description'] = $values['text_plain_app_description'];
        return ['General'=>[$values]];
    }

    private static function meta(\DOMDocument $document, string $property): string
    {
        foreach ($document->getElementsByTagName('meta') as $meta) {
            if ($meta->getAttribute('property') === $property) return $meta->getAttribute('content');
        }
        return '';
    }

    private static function structuredData(\DOMDocument $document): array
    {
        foreach ($document->getElementsByTagName('script') as $script) {
            if ($script->getAttribute('type') !== 'application/ld+json') continue;
            $data = json_decode($script->textContent, true);
            if (is_array($data) && isset($data['name'])) return $data;
        }
        return [];
    }

    public static function preview(array $data): string
    {
        $escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $image = $data['image'] ?? '';
        $parts = parse_url($image);
        if (($parts['scheme'] ?? '') !== 'https') $image = '';
        $picture = $image === '' ? '' : '<img alt="" width="124" src="'.$escape($image).'">';
        return '<div class="thumbnail">'.$picture.'<div class="caption"><h3>'.$escape($data['title']).'</h3><p>'.$escape(mb_substr($data['description'], 0, 200)).'</p></div></div>';
    }
}
