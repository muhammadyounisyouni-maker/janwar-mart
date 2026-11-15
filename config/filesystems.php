<?php
return [
    'default' => env('FILESYSTEM_DISK', 'public'),
    'disks' => [
        'local' => ['driver' => 'local', 'root' => storage_path('app/private'), 'serve' => false, 'throw' => false],
        'public' => ['driver' => 'local', 'root' => storage_path('app/public'), 'url' => env('APP_URL').'/storage', 'visibility' => 'public', 'throw' => false],
        // private documents (doctor certificates / licences): never web-accessible, admin-only route streams them
        'private' => ['driver' => 'local', 'root' => storage_path('app/private'), 'throw' => false],
    ],
    'links' => [public_path('storage') => storage_path('app/public')],
];
