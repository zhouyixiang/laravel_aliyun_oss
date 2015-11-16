# laravel_aliyun_oss
A simple laravel wrapper for aliyun oss sdk.

Included aliyun OSS SDK version: oss_php_sdk_20140625

# Usage

## ALIOSS
Add the following code to composer.json and run composer update.
```json
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/zhouyixiang/laravel_aliyun_oss"
    }
  ]
```
``` json
"require": {
    "yixiang/laravel_ali_oss": "dev-master"
}
```

Add service provider in the providers array of your config/app.php
``` php
'Yixiang\LaravelAliOss\AliOssServiceProvider',
```

Publish config file:
```bash
php artisan vendor:publish
```
Supply your OSS key in config/ali_oss.php

To obtain an instance of ALIOSS just write:
``` php
$aliOss = app('oss');
```

## Flysystem

Add configuration in config/filesystem.php
``` php
        'oss' => [
            'driver' => 'oss',
            'bucket' => env('OSS_BUCKET_NAME', ''),
            'prefix' => env('OSS_PREFIX', '')
        ]
```

**Noted:**
Aliyun OSS doesn't allow the first character of object identifier to be '/', so your OSS prefix should not start with it.

For example, you could configure your prefix like blow:

OSS\_PREFIX=cus/

# Credits
Flysystem Adapter is based on twn39's.
https://github.com/twn39/flysystem-aliyun-oss
