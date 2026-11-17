<?php

namespace Omatech\Editora\Tests;

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

        return $app;
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->fixture);
        parent::tearDown();
    }
}
