<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Update Server URL
    |--------------------------------------------------------------------------
    |
    | URL server untuk mengecek update terbaru. Jika null, akan menggunakan
    | database lokal untuk mengecek update.
    |
    */
    'server_url' => env('UPDATE_SERVER_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Auto Check Update
    |--------------------------------------------------------------------------
    |
    | Apakah aplikasi akan otomatis mengecek update secara berkala.
    |
    */
    'auto_check' => env('UPDATE_AUTO_CHECK', false),

    /*
    |--------------------------------------------------------------------------
    | Check Interval (days)
    |--------------------------------------------------------------------------
    |
    | Interval dalam hari untuk auto check update.
    |
    */
    'check_interval' => env('UPDATE_CHECK_INTERVAL', 7),

    /*
    |--------------------------------------------------------------------------
    | Backup Before Update
    |--------------------------------------------------------------------------
    |
    | Apakah akan backup database sebelum update.
    |
    */
    /*
    |--------------------------------------------------------------------------
    | Backup Before Update
    |--------------------------------------------------------------------------
    |
    | Apakah akan backup database sebelum update.
    |
    */
    'backup_before_update' => env('UPDATE_BACKUP_BEFORE_UPDATE', true),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificate when connecting to update server.
    | Set to false if using self-signed certificate or having SSL issues.
    |
    */
    'verify_ssl' => env('UPDATE_VERIFY_SSL', true),
];

