<?php

namespace Tests\Feature;

use App\Models\Show;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleCalendarImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_and_import_events_from_ics_url(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        Http::fake([
            'https://calendar.test/tour.ics' => Http::response(<<<'ICS'
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ShowControl//EN
BEGIN:VEVENT
UID:event-1
DTSTAMP:20260311T100000Z
DTSTART:20260910T210000Z
SUMMARY:Gira Norte - Bilbao
LOCATION:Sala BBK, Bilbao
DESCRIPTION:Evento importado
END:VEVENT
END:VCALENDAR
ICS, 200),
        ]);

        $previewResponse = $this->actingAs($user)->get(route('tours.google-calendar.index', [
            'ics_url' => 'https://calendar.test/tour.ics',
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-30',
        ]));

        $previewResponse->assertOk();
        $previewResponse->assertSee('Gira Norte - Bilbao');
        $previewResponse->assertSee('Gira Norte');

        $importResponse = $this->actingAs($user)->post(route('tours.google-calendar.import'), [
            'ics_url' => 'https://calendar.test/tour.ics',
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-30',
            'selected_event_ids' => ['event-1'],
        ]);

        $importResponse->assertRedirect(route('tours.google-calendar.index', [
            'ics_url' => 'https://calendar.test/tour.ics',
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-30',
        ]));

        $this->assertDatabaseHas('tours', [
            'owner_id' => $user->id,
            'name' => 'Gira Norte',
        ]);

        $show = Show::first();

        $this->assertNotNull($show);
        $this->assertSame($user->id, $show->owner_id);
        $this->assertSame('ics', $show->external_source);
        $this->assertSame(sha1('https://calendar.test/tour.ics'), $show->external_calendar_id);
        $this->assertSame('event-1', $show->external_event_id);
        $this->assertSame('Bilbao', $show->name);
        $this->assertSame('Bilbao', $show->city);
    }
}
