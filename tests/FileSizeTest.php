<?php

namespace Omatech\Editora\Tests;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;

final class FileSizeTest extends TestCase
{
    public function test_a_file_only_on_the_uploads_disk_reports_its_size(): void
    {
        Storage::disk('editora_public')->put('uploads/20261117/fixture.pdf', str_repeat('x', 1536));

        self::assertFileDoesNotExist(public_path('uploads/20261117/fixture.pdf'));
        self::assertSame('1.5 KB', _getFileSize('/uploads/20261117/fixture.pdf'));
        self::assertSame('1.5 KB', _getFileSize('uploads/20261117/fixture.pdf'));
    }

    public function test_an_empty_upload_is_zero_bytes_and_a_missing_one_has_no_size(): void
    {
        Storage::disk('editora_public')->put('uploads/20261117/empty.pdf', '');

        self::assertSame('0 bytes', _getFileSize('/uploads/20261117/empty.pdf'));
        self::assertSame('', _getFileSize('/uploads/20261117/absent.pdf'));
        self::assertSame([], RemoteHeaders::$requested);
    }

    public function test_a_file_only_in_the_bucket_is_sized_from_its_metadata(): void
    {
        $commands = [];
        $this->useBucket(['public/uploads/20261117/fixture.pdf' => 3 * 1024 * 1024], $commands);

        self::assertSame('3 MB', _getFileSize('/uploads/20261117/fixture.pdf'));
        self::assertSame('', _getFileSize('/uploads/20261117/absent.pdf'));
        self::assertSame([
            ['HeadObject', 'public/uploads/20261117/fixture.pdf'],
            ['HeadObject', 'public/uploads/20261117/absent.pdf'],
        ], $commands);
    }

    public function test_the_configured_uploads_prefix_is_part_of_the_key(): void
    {
        config(['editora-admin.uploads-storage-dir' => '/cms/uploads/']);
        Storage::disk('editora_public')->put('cms/uploads/20261117/fixture.pdf', str_repeat('x', 2048));
        Storage::disk('editora_public')->put('uploads/20261117/fixture.pdf', str_repeat('x', 2048));

        self::assertSame('2 KB', _getFileSize('/cms/uploads/20261117/fixture.pdf'));
        self::assertSame('', _getFileSize('/uploads/20261117/fixture.pdf'));
    }

    public function test_an_url_to_the_app_uploads_reads_the_disk_without_a_request_to_itself(): void
    {
        $this->app->instance('request', Request::create('https://admin.editora.test/editora/'));
        Storage::disk('editora_public')->put('uploads/20261117/fixture file.pdf', str_repeat('x', 1536));

        self::assertSame('1.5 KB', _getFileSize('https://editora.test/uploads/20261117/fixture%20file.pdf'));
        self::assertSame('1.5 KB', _getFileSize('https://ADMIN.editora.test/uploads/20261117/fixture%20file.pdf'));
        self::assertSame('', _getFileSize('https://editora.test/uploads/20261117/absent.pdf'));
        self::assertSame([], RemoteHeaders::$requested);
    }

    public function test_an_url_on_another_host_is_still_asked_to_that_host(): void
    {
        Storage::disk('editora_public')->put('uploads/20261117/fixture.pdf', str_repeat('x', 1536));

        self::assertSame('2 KB', _getFileSize('https://cdn.other.test/uploads/20261117/fixture.pdf'));
        self::assertSame(['https://cdn.other.test/uploads/20261117/fixture.pdf'], RemoteHeaders::$requested);
    }

    public function test_values_outside_the_uploads_directory_never_reach_the_disk(): void
    {
        $manager = $this->createMock(FilesystemManager::class);
        $manager->expects(self::never())->method('disk');
        Storage::swap($manager);

        foreach (['', '/', 'uploads', '/uploads/', '/uploadsx/a.pdf', '/img/a.pdf', '/uploads/../.env', 'uploads/20261117/../../secret.pdf'] as $value) {
            self::assertSame('', _getFileSize($value), $value);
        }
        self::assertSame([], RemoteHeaders::$requested);
    }

    public function test_a_file_kept_only_in_public_still_reports_its_size(): void
    {
        mkdir(public_path('docs'));
        file_put_contents(public_path('docs/legacy.pdf'), str_repeat('x', 1536));
        mkdir(public_path('uploads/20261117'), 0700, true);
        file_put_contents(public_path('uploads/20261117/legacy.pdf'), str_repeat('x', 2048));

        self::assertSame('1.5 KB', _getFileSize('/docs/legacy.pdf'));
        self::assertSame('2 KB', _getFileSize('/uploads/20261117/legacy.pdf'));
    }

    public function test_a_bucket_error_other_than_not_found_is_reported_and_shows_no_size(): void
    {
        Exceptions::fake();
        $commands = [];
        $this->useBucket([], $commands, [
            'public/uploads/20261117/forbidden.pdf' => ['AccessDenied', 403],
            'public/uploads/20261117/unreachable.pdf' => ['RequestTimeout', null],
        ]);

        self::assertSame('', _getFileSize('/uploads/20261117/absent.pdf'));
        Exceptions::assertNothingReported();
        self::assertSame('', _getFileSize('/uploads/20261117/forbidden.pdf'));
        self::assertSame('', _getFileSize('/uploads/20261117/unreachable.pdf'));
        Exceptions::assertReportedCount(2);
        Exceptions::assertReported(fn (UnableToRetrieveMetadata $e) => $e->location() === 'uploads/20261117/forbidden.pdf');
        Exceptions::assertReported(fn (UnableToRetrieveMetadata $e) => $e->location() === 'uploads/20261117/unreachable.pdf');
    }

    public function test_a_literal_percent_left_by_clean_file_name_stays_in_the_key(): void
    {
        // clean_file_name() keeps "%": an upload named "50%20off.pdf" is stored with that name.
        Storage::disk('editora_public')->put('uploads/20261117/50%20off.pdf', str_repeat('x', 1536));

        self::assertSame('1.5 KB', _getFileSize('/uploads/20261117/50%20off.pdf'));
    }
}
