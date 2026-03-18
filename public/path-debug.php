<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$baseDir = __DIR__;
$scAppPath = dirname(__DIR__).'/sc_app';
$fallbackAppPath = dirname(__DIR__);

echo "PATH DEBUG\n";
echo "==========\n\n";

echo "__DIR__:\n{$baseDir}\n\n";
echo "dirname(__DIR__):\n".dirname(__DIR__)."\n\n";
echo "sc_app candidate:\n{$scAppPath}\n";
echo "exists: ".(is_dir($scAppPath) ? 'yes' : 'no')."\n\n";

echo "sc/index.php exists:\n".(file_exists($baseDir.'/index.php') ? 'yes' : 'no')."\n";
echo "sc/build/manifest.json exists:\n".(file_exists($baseDir.'/build/manifest.json') ? 'yes' : 'no')."\n\n";

echo "sc_app/bootstrap/app.php exists:\n".(file_exists($scAppPath.'/bootstrap/app.php') ? 'yes' : 'no')."\n";
echo "sc_app/vendor/autoload.php exists:\n".(file_exists($scAppPath.'/vendor/autoload.php') ? 'yes' : 'no')."\n\n";

$appRoot = file_exists($scAppPath.'/bootstrap/app.php')
    ? $scAppPath
    : $fallbackAppPath;

echo "appRoot chosen by public/index.php logic:\n{$appRoot}\n\n";

if (file_exists($appRoot.'/bootstrap/app.php')) {
    require $appRoot.'/vendor/autoload.php';
    $app = require $appRoot.'/bootstrap/app.php';

    echo "Laravel public_path():\n".$app->publicPath()."\n\n";
    echo "Laravel base_path():\n".$app->basePath()."\n\n";
    echo "Laravel manifest path expected:\n".$app->publicPath('build/manifest.json')."\n";
    echo "manifest exists there: ".(file_exists($app->publicPath('build/manifest.json')) ? 'yes' : 'no')."\n";
} else {
    echo "bootstrap/app.php not found for selected appRoot.\n";
}
