<?php

// 1. Prepare SQLite database in /tmp for full read/write permissions on Vercel
$sourceDb = __DIR__ . '/../database/database.sqlite';
$targetDb = '/tmp/database.sqlite';

if (file_exists($sourceDb) && !file_exists($targetDb)) {
    copy($sourceDb, $targetDb);
}

if (file_exists($targetDb)) {
    putenv("DB_DATABASE={$targetDb}");
    $_ENV['DB_DATABASE'] = $targetDb;
    $_SERVER['DB_DATABASE'] = $targetDb;
}

// 2. Ensure framework directories exist in /tmp
$dirs = [
    '/tmp/framework/views',
    '/tmp/framework/cache/data',
    '/tmp/framework/sessions',
    '/tmp/logs'
];
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

// 3. Set custom view compiled path in environment
putenv('VIEW_COMPILED_PATH=/tmp/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/framework/views';

// 4. Forward to Laravel public entrypoint
require __DIR__ . '/../public/index.php';
