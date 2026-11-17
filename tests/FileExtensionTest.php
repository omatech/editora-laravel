<?php

namespace Omatech\Editora\Tests;

final class FileExtensionTest extends TestCase
{
    public function test_a_file_only_in_the_bucket_has_its_extension(): void
    {
        $commands = [];
        $this->useBucket(['public/uploads/20261117/fixture.pdf' => 1536, 'public/uploads/20261117/README' => 10], $commands);

        self::assertSame('PDF', _fileExtension('/uploads/20261117/fixture.pdf'));
        self::assertSame('', _fileExtension('/uploads/20261117/README'));
        self::assertSame('', _fileExtension('/uploads/20261117/absent.pdf'));
        self::assertSame([
            ['HeadObject', 'public/uploads/20261117/fixture.pdf'],
            ['HeadObject', 'public/uploads/20261117/README'],
            ['HeadObject', 'public/uploads/20261117/absent.pdf'],
        ], $commands);
    }

    public function test_values_that_are_not_uploads_keep_their_extension_rules(): void
    {
        file_put_contents(public_path('legacy.pdf'), 'x');

        self::assertSame('PDF', _fileExtension('/legacy.pdf'));
        self::assertSame('', _fileExtension('/absent.pdf'));
        self::assertSame('PDF', _fileExtension('https://cdn.other.test/files/a.pdf'));
        self::assertSame('', _fileExtension(''));
    }

    public function test_the_file_view_shows_and_links_a_file_only_in_the_bucket(): void
    {
        $commands = [];
        $this->useBucket(['public/uploads/20261117/fixture.pdf' => 3 * 1024 * 1024], $commands);

        $html = view('editora::attributes.F', [
            'p_mode' => 'V', 'attribute_name' => 'atr_OF_7',
            'attribute' => ['id' => 7, 'name' => 'document', 'type' => 'F', 'caption' => 'Document',
                'atrib_values' => [['text_val' => '/uploads/20261117/fixture.pdf']]],
        ])->render();

        self::assertStringContainsString('Extension: PDF - Size: 3 MB', $html);
        self::assertStringContainsString('<a href="/uploads/20261117/fixture.pdf" target="_blank"><i class="icon-eye"></i></a>', $html);
        self::assertCount(2, $commands, 'one HeadObject for the extension and one for the size');
    }
}
