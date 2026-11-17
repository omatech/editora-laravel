<?php

namespace Omatech\Editora\Tests;

use InvalidArgumentException;
use Omatech\Editora\Admin\Util\StoreMetadata;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AppMetadataTest extends TestCase
{
    public function test_store_metadata_parses_unicode_and_structured_data_without_the_removed_phpquery(): void
    {
        $html = '<meta property="og:title" content="Aplicación ☀"><meta property="og:description" content="Descripción"><meta property="og:image" content="https://fixture.invalid/app.png">';
        $data = StoreMetadata::parseGoogle($html)['General'][0];
        self::assertSame('Aplicación ☀', $data['app_title']);
        self::assertSame('Descripción', $data['text_plain_app_description']);
        self::assertSame('https://fixture.invalid/app.png', $data['banner_icon']);
        $structured = '<script type="application/ld+json">{"name":"Structured fixture","description":"Synthetic"}</script>';
        self::assertSame('Structured fixture', StoreMetadata::parseGoogle($structured)['General'][0]['app_title']);
    }

    public static function invalidUrls(): array
    {
        return [['file:///etc/passwd'], ['https://127.0.0.1/store/apps/details?id=com.fixture.app'], ['https://play.google.com.evil.invalid/store/apps/details?id=com.fixture.app'], ['https://play.google.com:443/store/apps/details?id=com.fixture.app'], ['https://play.google.com/store/apps/details?id[]=com.fixture.app']];
    }

    #[DataProvider('invalidUrls')]
    public function test_invalid_store_urls_are_rejected_before_any_connection(string $url): void
    {
        $this->expectException(InvalidArgumentException::class);
        StoreMetadata::google($url);
    }

    public function test_preview_escapes_store_text_and_refuses_a_script_image(): void
    {
        $html = StoreMetadata::preview(['title' => '<svg onload="alert(1)">', 'description' => 'Hello <team>', 'image' => 'javascript:alert(1)']);
        self::assertStringNotContainsString('<svg', $html);
        self::assertStringContainsString('Hello &lt;team&gt;', $html);
        self::assertStringNotContainsString('javascript:', $html);
    }
}
