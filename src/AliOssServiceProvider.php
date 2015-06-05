<?php
namespace Yixiang\LaravelAliOss;

use Illuminate\Support\ServiceProvider;

class AliOssServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__."/../config/ali_oss.php" => config_path('ali_oss.php')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('oss', function($app) {
            $config = config('ali_oss');
            // set sdk default log directory path to laravel logs dir.
            define('ALI_LOG_PATH', storage_path('logs'));
            return new \ALIOSS($config['access_id'], $config['access_key'], $config['hostname_internal']);
        });
    }
}