<?php

namespace Tests\Unit;

use App\Models\Show;
use App\Models\User;
use App\Models\UserAlertSetting;
use App\Support\ShowAlertService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowAlertServiceTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_it_respects_disabled_alert_types_from_user_settings(): void
    {
        $service = new ShowAlertService();

        $user = User::factory()->create();

        UserAlertSetting::create([
            'user_id' => $user->id,
            'core_info_enabled' => false,
            'status_enabled' => true,
            'status_days' => 30,
            'validations_enabled' => false,
        ]);

        $show = new Show([
            'owner_id' => $user->id,
            'date' => Carbon::parse('2026-04-10'),
            'city' => 'Madrid',
            'name' => 'Bolo alerta',
            'status' => 'tentative',
            'lighting_validated' => false,
            'sound_validated' => false,
            'space_validated' => false,
            'general_validated' => false,
        ]);
        $show->setRelation('owner', $user->load('alertSettings'));

        $alerts = $service->alertsForShow($show, Carbon::parse('2026-04-05'));

        $this->assertCount(1, $alerts);
        $this->assertSame('status_not_ready', $alerts[0]['key']);
    }

    public function test_it_respects_custom_thresholds_from_user_settings(): void
    {
        $service = new ShowAlertService();

        $user = User::factory()->create();

        UserAlertSetting::create([
            'user_id' => $user->id,
            'core_info_enabled' => true,
            'core_info_days' => 3,
            'status_enabled' => true,
            'status_days' => 3,
            'validations_enabled' => true,
            'validations_days' => 3,
        ]);

        $show = new Show([
            'owner_id' => $user->id,
            'date' => Carbon::parse('2026-04-10'),
            'city' => 'Madrid',
            'name' => 'Bolo alerta',
            'status' => 'tentative',
            'lighting_validated' => false,
            'sound_validated' => false,
            'space_validated' => false,
            'general_validated' => false,
        ]);
        $show->setRelation('owner', $user->load('alertSettings'));

        $alerts = $service->alertsForShow($show, Carbon::parse('2026-04-05'));

        $this->assertSame([], $alerts);
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
