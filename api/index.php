<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

$setDefaultEnvironment = static function (string $key, string $value): void {
    if (isset($_ENV[$key]) || isset($_SERVER[$key]) || getenv($key) !== false) {
        return;
    }

    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
    putenv("{$key}={$value}");
};

$storagePath = '/tmp/storage';

foreach ([
    $storagePath.'/app/private',
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

$setDefaultEnvironment('LOG_CHANNEL', 'stderr');
$setDefaultEnvironment('SESSION_DRIVER', 'cookie');
$setDefaultEnvironment('CACHE_STORE', 'array');

$databaseConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'sqlite';
$initializeDemoDatabase = false;

if ($databaseConnection === 'sqlite') {
    $databasePath = '/tmp/database.sqlite';
    $initializeDemoDatabase = ! file_exists($databasePath);

    if ($initializeDemoDatabase) {
        touch($databasePath);
    }

    $_ENV['DB_DATABASE'] = $databasePath;
    $_SERVER['DB_DATABASE'] = $databasePath;
    putenv("DB_DATABASE={$databasePath}");
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__.'/../bootstrap/app.php';
$app->useStoragePath($storagePath);

if ($initializeDemoDatabase) {
    $app->make(Kernel::class)->bootstrap();
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
}

$app->handleRequest(Request::capture());
