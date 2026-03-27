<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Hoja de ruta - {{ $show->name }}</title>
        @php
            $primaryColor = $pdfSettings->primary_color ?: '#0f172a';
            $brandName = $pdfSettings->brand_name ?: ($show->tour?->name ?: 'Show Control');
            $headerText = $pdfSettings->header_text;
            $footerText = $pdfSettings->footer_text;
        @endphp
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                color: #0f172a;
                font-size: 12px;
                margin: 0;
            }

            .page {
                padding: 32px 36px 40px;
            }

            .header {
                border-bottom: 2px solid {{ $primaryColor }}33;
                padding-bottom: 18px;
                margin-bottom: 24px;
            }

            .header-table {
                width: 100%;
                border-collapse: collapse;
            }

            .header-table td {
                vertical-align: top;
                border: none;
                padding: 0;
            }

            .header-main {
                width: 68%;
                padding-right: 20px;
            }

            .header-side {
                width: 32%;
            }

            .eyebrow {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: {{ $primaryColor }};
                margin-bottom: 8px;
                font-weight: bold;
            }

            h1 {
                font-size: 28px;
                margin: 0 0 8px;
                line-height: 1.1;
            }

            .subtitle {
                color: #475569;
                font-size: 13px;
            }

            .badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 999px;
                background: {{ $primaryColor }}22;
                color: {{ $primaryColor }};
                font-size: 10px;
                font-weight: bold;
                margin-right: 6px;
            }

            .contact-box {
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 12px 14px;
                background: #f8fafc;
            }

            .contact-title {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #64748b;
                margin-bottom: 8px;
                font-weight: bold;
            }

            .contact-line {
                font-size: 11px;
                color: #334155;
                margin-bottom: 5px;
                line-height: 1.4;
            }

            .contact-line strong {
                color: #0f172a;
            }

            .section {
                margin-top: 22px;
            }

            .section h2 {
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: {{ $primaryColor }};
                margin: 0 0 12px;
                padding-bottom: 8px;
                border-bottom: 1px solid #e2e8f0;
            }

            .card {
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 12px 14px;
                margin-bottom: 10px;
            }

            .label {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #64748b;
                margin-bottom: 6px;
            }

            .value {
                font-size: 14px;
                font-weight: bold;
                color: #0f172a;
            }

            .body-text {
                color: #334155;
                line-height: 1.35;
                white-space: pre-line;
            }

            .markdown p {
                margin: 0 0 2px;
            }

            .markdown strong {
                color: #0f172a;
            }

            .markdown ul,
            .markdown ol {
                margin: 8px 0 0 18px;
                padding: 0;
            }

            .alerts {
                border: 1px solid #fecaca;
                background: #fff1f2;
                border-radius: 16px;
                padding: 14px 16px;
            }

            .alert-item {
                margin-bottom: 10px;
            }

            .alert-title {
                color: #881337;
                font-weight: bold;
                margin-bottom: 3px;
            }

            .muted {
                color: #64748b;
            }

            table.schedule {
                width: 100%;
                border-collapse: collapse;
            }

            table.schedule td {
                border-bottom: 1px solid #e2e8f0;
                padding: 10px 0;
                vertical-align: top;
            }

            table.schedule td:first-child {
                width: 40%;
                color: #64748b;
                text-transform: uppercase;
                font-size: 10px;
                letter-spacing: 1px;
            }

            table.schedule td:last-child {
                font-weight: bold;
                color: #0f172a;
            }
        </style>
    </head>
    <body>
        <div class="page">
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-main">
                            <div class="eyebrow">Hoja de ruta</div>
                            <div style="font-size: 11px; color: #334155; margin-bottom: 8px; font-weight: bold;">{{ $brandName }}</div>
                            <h1>{{ $show->name }}</h1>
                            <div class="subtitle">
                                {{ $show->date->format('d/m/Y') }} - {{ $show->city }} - {{ $show->venue ?: 'Venue pendiente' }}
                            </div>
                            @if ($headerText)
                                <div style="margin-top: 10px; color: #334155; font-size: 11px;">{{ $headerText }}</div>
                            @endif
                            <div style="margin-top: 12px;">
                                <span class="badge">{{ $statusOptions[$show->status] ?? $show->status }}</span>
                                <span class="badge">{{ $show->tour?->name ?: 'Sin gira' }}</span>
                            </div>
                        </td>
                        <td class="header-side">
                            <div class="contact-box">
                                <div class="contact-title">Contacto</div>
                                <div class="contact-line"><strong>Nombre:</strong> {{ $show->contact_name ?: '-' }}</div>
                                <div class="contact-line"><strong>Rol:</strong> {{ $show->contact_role ?: '-' }}</div>
                                <div class="contact-line"><strong>Tel:</strong> {{ $show->contact_phone ?: '-' }}</div>
                                <div class="contact-line"><strong>Email:</strong> {{ $show->contact_email ?: '-' }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            @if ($alerts !== [])
                <div class="section">
                    <h2>Alertas</h2>
                    <div class="alerts">
                        @foreach ($alerts as $alert)
                            <div class="alert-item">
                                <div class="alert-title">{{ $alert['title'] }}</div>
                                <div>{{ $alert['message'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="section">
                <div class="card">
                    <div class="label">Venue</div>
                    <div class="value">{{ $show->venue ?: 'Pendiente' }}</div>
                </div>
                <div class="card">
                    <div class="label">Notas del espacio</div>
                    <div class="body-text markdown">
                        {!! $show->space_notes
                            ? \Illuminate\Support\Str::markdown($show->space_notes, [
                                'html_input' => 'strip',
                                'allow_unsafe_links' => false,
                            ])
                            : '<p>Sin notas del espacio.</p>' !!}
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Notas generales</h2>
                <div class="card">
                    <div class="body-text markdown">
                        {!! $show->general_notes
                            ? \Illuminate\Support\Str::markdown($show->general_notes, [
                                'html_input' => 'strip',
                                'allow_unsafe_links' => false,
                            ])
                            : '<p>Sin notas generales.</p>' !!}
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Horarios</h2>
                <table class="schedule">
                    @foreach ([
                        'Montaje' => $show->getRawOriginal('load_in_at'),
                        'Comida' => $show->getRawOriginal('meal_at'),
                        'Pruebas' => $show->getRawOriginal('soundcheck_at'),
                        'Apertura de puertas' => $show->getRawOriginal('doors_at'),
                        'Show' => $show->getRawOriginal('show_at'),
                        'Fin show' => $show->getRawOriginal('show_end_at'),
                        'Desmontaje' => $show->getRawOriginal('load_out_at'),
                    ] as $label => $value)
                        <tr>
                            <td>{{ $label }}</td>
                            <td>{{ $value ?: '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            @if ($show->tour && $show->tour->contacts->isNotEmpty())
                <div class="section">
                    <h2>Contactos de gira</h2>
                    @foreach ($show->tour->contacts as $contact)
                        <div class="card">
                            <div class="body-text">
                                <strong>{{ $contact->name }}</strong> - {{ $contact->role ?: 'Sin rol' }} - {{ $contact->phone ?: 'Sin telefono' }} - {{ $contact->email ?: 'Sin email' }}
                            </div>
                            @if ($contact->notes)
                                <div class="body-text" style="margin-top: 8px;">{{ $contact->notes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="section muted" style="margin-top: 28px;">
                @if ($footerText)
                    <div style="margin-bottom: 6px;">{{ $footerText }}</div>
                @endif
                @if ($pdfSettings->show_generated_at)
                    <div>Generado el {{ now()->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>
    </body>
</html>
