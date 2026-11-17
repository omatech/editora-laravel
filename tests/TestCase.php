<?php

namespace Omatech\Editora\Tests;

use Aws\CommandInterface;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Testing\TestCase as FrameworkTestCase;
use Illuminate\Support\Facades\File;
use Omatech\Editora\EditoraServiceProvider;

abstract class TestCase extends FrameworkTestCase
{
    protected string $fixture;

    public function createApplication()
    {
        $this->fixture = sys_get_temp_dir().'/editora-admin-'.bin2hex(random_bytes(8));
        foreach (['bootstrap/cache', 'storage/framework/views', 'storage/logs', 'public', 'disk'] as $folder) {
            mkdir($this->fixture.'/'.$folder, 0700, true);
        }
        $app = Application::configure(basePath: $this->fixture)
            ->withProviders([EditoraServiceProvider::class])
            ->withExceptions()
            ->create();
        $fixture = $this->fixture;
        $app->afterBootstrapping(LoadConfiguration::class, static function (Application $app) use ($fixture): void {
            $app['config']->set([
                'app.key' => 'base64:'.base64_encode(str_repeat('f', 32)),
                'app.url' => 'https://editora.test',
                'cache.default' => 'array', 'session.driver' => 'array',
                'logging.default' => 'null', 'logging.channels.null' => ['driver' => 'monolog', 'handler' => \Monolog\Handler\NullHandler::class],
                // The uploads disk is not public/: a file there has no local copy, as on Laravel Cloud.
                'filesystems.disks.editora_public' => ['driver' => 'local', 'root' => $fixture.'/disk', 'throw' => true],
            ]);
        });
        $app->make(Kernel::class)->bootstrap();
        RemoteHeaders::reset();

        return $app;
    }

    /**
     * An S3 disk whose HTTP layer is an in-memory bucket that records each command it receives.
     * $objects maps keys to sizes; $failures maps keys to [error code, HTTP status or null for a network failure].
     */
    protected function useBucket(array $objects, array &$commands, array $failures = []): void
    {
        config([
            'editora-admin.uploads-storage' => 'fixture_bucket',
            'filesystems.disks.fixture_bucket' => [
                'driver' => 's3', 'key' => 'fixture', 'secret' => 'fixture', 'region' => 'auto',
                'bucket' => 'fixture-bucket', 'endpoint' => 'https://bucket.fixture.test', 'use_path_style_endpoint' => true,
                'root' => 'public', 'visibility' => 'private', 'throw' => true,
                'handler' => static function (CommandInterface $command) use ($objects, $failures, &$commands) {
                    $commands[] = [$command->getName(), $command['Key']];
                    if ($command->getName() === 'HeadObject' && isset($objects[$command['Key']])) {
                        return Create::promiseFor(new Result(['ContentLength' => $objects[$command['Key']]]));
                    }
                    [$code, $status] = $failures[$command['Key']] ?? ['NotFound', 404];

                    return Create::rejectionFor(new S3Exception($code, $command, [
                        'code' => $code, 'connection_error' => $status === null,
                        'request' => new Psr7Request('HEAD', 'https://bucket.fixture.test'),
                        'response' => $status === null ? null : new Response($status),
                    ]));
                },
            ],
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->fixture);
        parent::tearDown();
    }
}
