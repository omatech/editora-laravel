<?php

namespace Omatech\Editora\Tests;

/** Stands in for get_headers(): what the remote host answers and which URLs were asked. */
final class RemoteHeaders
{
    /** @var list<string> */
    public static array $requested = [];

    public static string $contentLength = '2048';

    public static function reset(): void
    {
        self::$requested = [];
        self::$contentLength = '2048';
    }

    public static function contentLength(string $url): string
    {
        self::$requested[] = $url;

        return self::$contentLength;
    }
}
