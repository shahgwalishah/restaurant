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

$setEnvironment = static function (string $key, string $value): void {
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
    putenv("{$key}={$value}");
};

$writeToErrorLog = static function (string $message): void {
    error_log($message);
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
$setEnvironment('SESSION_DRIVER', 'cookie');
$setEnvironment('CACHE_STORE', 'array');
$setEnvironment('QUEUE_CONNECTION', 'sync');
$setEnvironment('FILESYSTEM_DISK', 'public');

$databaseConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'sqlite';
$initializeDemoDatabase = false;

if ($databaseConnection === 'sqlite') {
    if (! extension_loaded('pdo_sqlite')) {
        http_response_code(500);
        $writeToErrorLog('Vercel runtime is missing pdo_sqlite. Configure an external database or use a PHP runtime with SQLite enabled.');
        echo 'Database driver error: pdo_sqlite extension is not available on this server.';

        return;
    }

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

try {
    /** @var Application $app */
    $app = require __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath($storagePath);

    if ($initializeDemoDatabase) {
        Artisan::setFacadeApplication($app);
        $app->make(Kernel::class)->bootstrap();
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
    }

    $app->handleRequest(Request::capture());
} catch (Throwable $exception) {
    http_response_code(500);
    $writeToErrorLog(sprintf(
        "Laravel runtime error: %s in %s:%d\n%s",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));

    if (($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? getenv('APP_DEBUG')) === 'true') {
        echo '<pre>'.htmlspecialchars($exception, ENT_QUOTES, 'UTF-8').'</pre>';
    } else {
        echo 'Laravel runtime error. Check Vercel Runtime Logs for the exact exception.';
    }
}
