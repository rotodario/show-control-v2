<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../sc_app/vendor/autoload.php';
$app = require __DIR__.'/../sc_app/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$token = $_GET['token'] ?? '';
$expectedToken = env('MAINTENANCE_RUN_TOKEN', '');

$authorized = $expectedToken !== '' && hash_equals($expectedToken, (string) $token);
$statusCode = 200;
$output = '';
$ran = false;

if (! $authorized) {
    $statusCode = 403;
} else {
    try {
        $steps = [];

        Artisan::call('migrate', ['--force' => true]);
        $steps[] = "== php artisan migrate --force ==\n".trim(Artisan::output());

        Artisan::call('optimize:clear');
        $steps[] = "== php artisan optimize:clear ==\n".trim(Artisan::output());

        $output = implode("\n\n", array_filter($steps));
        $ran = true;
    } catch (Throwable $exception) {
        $statusCode = 500;
        $output = $exception->getMessage()."\n\n".$exception->getTraceAsString();
    }
}

http_response_code($statusCode);
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Run Chat Migration</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
        }
        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background: #111827;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }
        .badge {
            display: inline-block;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .ok { background: #052e16; color: #86efac; }
        .error { background: #450a0a; color: #fca5a5; }
        .warn { background: #3f2305; color: #fcd34d; }
        pre {
            margin: 20px 0 0;
            padding: 16px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border-radius: 14px;
            background: #020617;
            border: 1px solid #1e293b;
            color: #cbd5e1;
        }
        code {
            background: #020617;
            padding: 2px 6px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <p>
                <?php if ($authorized && $ran): ?>
                    <span class="badge ok">Migracion ejecutada</span>
                <?php elseif ($authorized): ?>
                    <span class="badge error">Error en migracion</span>
                <?php else: ?>
                    <span class="badge warn">Acceso denegado</span>
                <?php endif; ?>
            </p>

            <h1>Chat migration runner</h1>
            <p>Este script ejecuta <code>php artisan migrate --force</code> y <code>php artisan optimize:clear</code> dentro de Laravel.</p>

            <?php if (! $authorized): ?>
                <p>Debes llamar esta URL con <code>?token=TU_TOKEN</code> y definir <code>MAINTENANCE_RUN_TOKEN</code> en el <code>.env</code>.</p>
            <?php endif; ?>

            <pre><?= htmlspecialchars($output !== '' ? $output : 'Sin salida.') ?></pre>
        </div>
    </div>
</body>
</html>
