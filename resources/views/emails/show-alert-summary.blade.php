<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:680px;margin:0 auto;padding:32px 20px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:24px;padding:32px;">
            <p style="margin:0 0 18px;font-size:12px;font-weight:700;letter-spacing:0.24em;text-transform:uppercase;color:#64748b;">Show Control</p>
            <h1 style="margin:0 0 24px;font-size:24px;line-height:1.2;color:#0f172a;">Alerta operativa: {{ $show->name }}</h1>

            @if (! blank($alertLines))
                <div style="margin:0 0 24px;border:1px solid #f59e0b;background:#fff7ed;border-radius:18px;padding:20px;">
                    <p style="margin:0 0 12px;font-size:13px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#9a3412;">Alertas activas del bolo</p>
                    <div style="font-size:14px;line-height:1.7;color:#7c2d12;">{!! nl2br(e($alertLines)) !!}</div>
                </div>
            @endif

            <div style="font-size:15px;line-height:1.7;color:#334155;">{!! nl2br(e($body)) !!}</div>
        </div>
    </div>
</body>
</html>
