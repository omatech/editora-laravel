<?php

namespace Omatech\Editora\Admin\Util;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;

/**
 * Files uploaded from the admin live on the editora uploads disk (local public/
 * or a bucket on Laravel Cloud) under the uploads directory; the stored value is
 * "/uploads/20261103/a.pdf", the disk key "uploads/20261103/a.pdf".
 */
final class UploadsDisk
{
    public static function dir(): string
    {
        return trim((string) config('editora-admin.uploads-storage-dir', 'uploads'), '/');
    }

    /** The disk key of a stored value or of an URL to it on this app, or null when it is not an upload. */
    public static function key(string $value): ?string
    {
        $path = filter_var($value, FILTER_VALIDATE_URL) ? self::appUrlPath($value) : $value;
        $path = ltrim((string) $path, '/');
        if (!str_starts_with($path, self::dir().'/') || str_ends_with($path, '/') || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }

    /**
     * Bytes of a stored file, or null when it cannot be found. An upload is
     * sized from the disk metadata (HeadObject on S3, the body is never read).
     */
    public static function bytes(string $value): ?int
    {
        $key = self::key($value);
        if ($key !== null) {
            return self::diskBytes($key) ?? self::found(_getLocalFileSize(public_path($key)));
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return self::found(_getRemoteFileSize($value));
        }

        return self::found(_getLocalFileSize(public_path($value)));
    }

    private static function diskBytes(string $key): ?int
    {
        try {
            return Storage::disk(config('editora-admin.uploads-storage'))->size($key);
        } catch (UnableToRetrieveMetadata) {
            return null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /** The local and HTTP readers answer 0 both for an empty and for a missing file. */
    private static function found($size): ?int
    {
        return (int) $size > 0 ? (int) $size : null;
    }

    private static function appUrlPath(string $url): ?string
    {
        $parts = parse_url($url);
        $hosts = [parse_url((string) config('app.url'), PHP_URL_HOST), app('request')->getHost()];
        if (!in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || !in_array(strtolower($parts['host'] ?? ''), array_map('strtolower', array_filter($hosts)), true)) {
            return null;
        }

        return rawurldecode($parts['path'] ?? '');
    }
}
