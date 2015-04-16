<?php
namespace Yixiang\LaravelAliOss;

use Illuminate\Support\ServiceProvider;

class AliOssServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__."../config/ali_oss.php" => config_path('ali_oss')
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
            $config = $app['config']['ali_oss'];
            return new \ALIOSS($config->access_id, $config->access_key, $config->hostname);
        });
    }
}