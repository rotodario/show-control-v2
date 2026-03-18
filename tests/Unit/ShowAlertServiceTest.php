<?php

namespace Tests\Unit;

use App\Models\Show;
use App\Support\ShowAlertService;
use Carbon\Carbon;
use Tests\TestCase;

class ShowAlertServiceTest extends TestCase
{
    public function test_it_generates_expected_alerts_for_incomplete_show(): void
    {
        $service = new ShowAlertService();

        $show = new Show([
            'date' => Carbon::parse('2026-04-10'),
            'city' => 'Madrid',
            'name' => 'Bolo alerta',
            'status' => 'tentative',
            'lighting_validated' => false,
            'sound_validated' => false,
            'space_validated' => false,
            'general_validated' => false,
        ]);

        $alerts = $service->alertsForShow($show, Carbon::parse('2026-04-05'));

        $this->assertCount(3, $alerts);
        $this->assertSame('missing_core_info', $alerts[0]['key']);
        $this->assertSame('status_not_ready', $alerts[1]['key']);
        $this->assertSame('missing_validations', $alerts[2]['key']);
    }

    public function test_it_returns_no_alerts_for_closed_complete_show(): void
    {
        $service = new ShowAlertService();

        $show = new Show([
            'date' => Carbon::parse('2026-04-10'),
            'city' => 'Madrid',
            'venue' => 'WiZink',
            'name' => 'Bolo cerrado',
            'status' => 'closed',
            'load_in_at' => '10:00',
            'show_at' => '21:00',
            'contact_name' => 'Produccion',
            'contact_phone' => '600000000',
            'lighting_notes' => 'OK',
            'sound_notes' => 'OK',
            'space_notes' => 'OK',
            'general_notes' => 'OK',
            'lighting_validated' => true,
            'sound_validated' => true,
            'space_validated' => true,
            'general_validated' => true,
        ]);

        $alerts = $service->alertsForShow($show, Carbon::parse('2026-04-05'));

        $this->assertSame([], $alerts);
    }
}
