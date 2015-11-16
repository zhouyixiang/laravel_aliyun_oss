<?php
namespace Yixiang\LaravelAliOss;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class AliOssServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . "/../config/ali_oss.php" => config_path('ali_oss.php')
        ]);

        Storage::extend('oss', function ($app, $config) {
            return new Filesystem(new AliOssAdapter($app['oss'], $config['bucket'], $config['prefix']));
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('oss', function ($app) {
            $config = config('ali_oss');
            // set sdk default log directory path to laravel logs dir.
            define('ALI_LOG_PATH', storage_path('logs'));
            return new \ALIOSS($config['access_id'], $config['access_key'], $config['hostname_internal']);
        });
    }
}