<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <title>{{ __('ui.roadmap') }} - {{ $show->name }}</title>
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

            .route-grid {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 10px;
            }

            .route-grid td {
                width: 50%;
                vertical-align: top;
                padding-right: 10px;
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
                            <div class="eyebrow">{{ __('ui.roadmap') }}</div>
                            <div style="font-size: 11px; color: #334155; margin-bottom: 8px; font-weight: bold;">{{ $brandName }}</div>
                            <h1>{{ $show->name }}</h1>
                            <div class="subtitle">
                                {{ $show->date->format('d/m/Y') }} - {{ $show->city }} - {{ $show->venue ?: __('ui.pending_venue') }}
                            </div>
                            @if ($headerText)
                                <div style="margin-top: 10px; color: #334155; font-size: 11px;">{{ $headerText }}</div>
                            @endif
                            <div style="margin-top: 12px;">
                                <span class="badge">{{ $show->translatedCurrentStatus() }}</span>
                                <span class="badge">{{ $show->tour?->name ?: __('ui.no_tour') }}</span>
                            </div>
                        </td>
                        <td class="header-side">
                            <div class="contact-box">
                                <div class="contact-title">{{ __('ui.contact') }}</div>
                                <div class="contact-line"><strong>{{ __('ui.name') }}:</strong> {{ $show->contact_name ?: '-' }}</div>
                                <div class="contact-line"><strong>{{ __('ui.role') }}:</strong> {{ $show->contact_role ?: '-' }}</div>
                                <div class="contact-line"><strong>{{ __('ui.phone') }}:</strong> {{ $show->contact_phone ?: '-' }}</div>
                                <div class="contact-line"><strong>{{ __('ui.email') }}:</strong> {{ $show->contact_email ?: '-' }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            @if ($alerts !== [])
                <div class="section">
                    <h2>{{ __('ui.alerts') }}</h2>
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
                    <div class="label">{{ __('ui.venue') }}</div>
                    <div class="value">{{ $show->venue ?: __('ui.pending_capitalized') }}</div>
                </div>
                <div class="card">
                    <div class="label">{{ __('ui.route_to_venue') }}</div>
                    <table class="route-grid">
                        <tr>
                            <td>
                                <div class="label">{{ __('ui.travel_mode') }}</div>
                                <div class="body-text">{{ $travelModeOptions[$show->travel_mode ?: 'van'] ?? ($show->travel_mode ?: 'van') }}</div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="label">{{ __('ui.origin') }}</div>
                                <div class="body-text">{{ $travelRoute['origin'] ?: __('ui.pending_capitalized') }}</div>
                            </td>
                            <td>
                                <div class="label">{{ __('ui.destination') }}</div>
                                <div class="body-text">{{ $travelRoute['destination'] ?: __('ui.pending_capitalized') }}</div>
                            </td>
                        </tr>
                        @if (! empty($travelRoute['available']))
                            <tr>
                                <td>
                                    <div class="label">{{ __('ui.estimated_time') }}</div>
                                    <div class="value">{{ $travelRoute['duration_text'] }}</div>
                                </td>
                                <td>
                                    <div class="label">{{ __('ui.distance') }}</div>
                                    <div class="value">{{ $travelRoute['distance_text'] }}</div>
                                </td>
                            </tr>
                        @endif
                    </table>

                    @if (! empty($travelRoute['available']))
                        <div class="body-text">{{ __('ui.open_route') }}: {{ $travelRoute['directions_url'] }}</div>
                    @elseif (($travelRoute['reason'] ?? null) === 'plane_mode')
                        <div class="body-text">{{ __('ui.plane_mode_route_notice') }}</div>
                        <table class="route-grid">
                            <tr>
                                <td>
                                    <div class="label">{{ __('ui.flight_origin') }}</div>
                                    <div class="body-text">{{ $show->flight_origin ?: __('ui.pending_capitalized') }}</div>
                                </td>
                                <td>
                                    <div class="label">{{ __('ui.flight_destination') }}</div>
                                    <div class="body-text">{{ $show->flight_destination ?: __('ui.pending_capitalized') }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="label">{{ __('ui.estimated_duration') }}</div>
                                    <div class="body-text">{{ $show->flight_duration_estimate ?: __('ui.pending_capitalized') }}</div>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                        <div class="body-text">{{ $show->flight_notes ?: __('ui.no_flight_notes') }}</div>
                    @elseif (($travelRoute['reason'] ?? null) === 'missing_addresses')
                        <div class="body-text">{{ __('ui.missing_addresses_notice') }}</div>
                    @elseif (($travelRoute['reason'] ?? null) === 'geocoding_failed')
                        <div class="body-text">{{ __('ui.pdf_route_geocoding_failed') }}</div>
                    @else
                        <div class="body-text">{{ __('ui.pdf_route_unavailable') }}</div>
                    @endif
                </div>
                <div class="card">
                    <div class="label">{{ __('ui.space_notes') }}</div>
                    <div class="body-text markdown">
                        {!! $show->space_notes
                            ? \Illuminate\Support\Str::markdown($show->space_notes, [
                                'html_input' => 'strip',
                                'allow_unsafe_links' => false,
                            ])
                            : '<p>'.e(__('ui.no_space_notes')).'</p>' !!}
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>{{ __('ui.general_notes') }}</h2>
                <div class="card">
                    <div class="body-text markdown">
                        {!! $show->general_notes
                            ? \Illuminate\Support\Str::markdown($show->general_notes, [
                                'html_input' => 'strip',
                                'allow_unsafe_links' => false,
                            ])
                            : '<p>'.e(__('ui.no_general_notes')).'</p>' !!}
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>{{ __('ui.schedules') }}</h2>
                <table class="schedule">
                    @foreach ([
                        __('ui.load_in') => $show->getRawOriginal('load_in_at'),
                        __('ui.meal') => $show->getRawOriginal('meal_at'),
                        __('ui.soundcheck') => $show->getRawOriginal('soundcheck_at'),
                        __('ui.doors') => $show->getRawOriginal('doors_at'),
                        'Show' => $show->getRawOriginal('show_at'),
                        __('ui.show_end') => $show->getRawOriginal('show_end_at'),
                        __('ui.load_out') => $show->getRawOriginal('load_out_at'),
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
                    <h2>{{ __('ui.tour_contacts') }}</h2>
                    @foreach ($show->tour->contacts as $contact)
                        <div class="card">
                            <div class="body-text">
                                <strong>{{ $contact->name }}</strong> - {{ $contact->role ?: __('ui.no_role') }} - {{ $contact->phone ?: __('ui.no_phone') }} - {{ $contact->email ?: __('ui.no_email') }}
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
                    <div>{{ __('ui.generated_at') }} {{ now()->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>
    </body>
</html>
