<?php
return [
    'access_id' => env('OSS_ACCESS_ID', ''),
    'access_key' => env('OSS_ACCESS_KEY', ''),
    'hostname_internal' => env('OSS_HOSTNAME_INTERNAL', ''),
    'hostname_external' => env('OSS_HOSTNAME_EXTERNAL', ''),
    'hostname_custom' => env('OSS_HOSTNAME_CUSTOM', ''),
    'bucket' => env('OSS_BUCKET_NAME', '')
];