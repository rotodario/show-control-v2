<?php

namespace Tests\Feature;

use App\Models\Show;
use App\Models\ShowMessageRead;
use App\Models\ShowSectionMessage;
use App\Models\Tour;
use App\Models\User;
use App\Models\UserAlertSetting;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShowManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_show(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $tour = Tour::create([
            'owner_id' => $user->id,
            'name' => 'Tour Test',
            'color' => '#2563EB',
            'notes' => 'Notas',
        ]);

        $response = $this->actingAs($user)->post(route('shows.store'), [
            'tour_id' => $tour->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'WiZink',
            'name' => 'Bolo Madrid',
            'status' => 'confirmed',
            'show_at' => '21:00',
            'lighting_validated' => '1',
        ]);

        $show = Show::first();

        $response->assertRedirect(route('shows.show', $show));
        $this->assertDatabaseHas('shows', [
            'name' => 'Bolo Madrid',
            'city' => 'Madrid',
            'status' => 'confirmed',
            'tour_id' => $tour->id,
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'show.created',
            'show_id' => $show->id,
        ]);
    }

    public function test_admin_can_upload_a_show_document(): void
    {
        Storage::fake('public');
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'name' => 'Bolo Madrid',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('shows.documents.store', $show), [
            'document_type' => 'Rider',
            'title' => 'Rider final',
            'file' => UploadedFile::fake()->create('rider.pdf', 250, 'application/pdf'),
        ]);

        $response->assertRedirect(route('shows.show', $show));
        $this->assertDatabaseHas('show_documents', [
            'show_id' => $show->id,
            'document_type' => 'Rider',
            'title' => 'Rider final',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'show_document.created',
            'show_id' => $show->id,
        ]);
    }

    public function test_admin_can_update_show_contact_fields(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'name' => 'Bolo Madrid',
            'status' => 'confirmed',
            'contact_name' => 'Viejo nombre',
        ]);

        $response = $this->actingAs($user)->put(route('shows.update', $show), [
            'tour_id' => '',
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'Sala X',
            'name' => 'Bolo Madrid',
            'status' => 'confirmed',
            'load_in_at' => '10:00',
            'meal_at' => '',
            'soundcheck_at' => '',
            'doors_at' => '',
            'show_at' => '21:00',
            'show_end_at' => '',
            'load_out_at' => '',
            'contact_name' => 'Nuevo contacto',
            'contact_role' => 'Produccion',
            'contact_phone' => '600123123',
            'contact_email' => 'contacto@test.local',
            'lighting_notes' => 'OK',
            'sound_notes' => 'OK',
            'space_notes' => 'OK',
            'general_notes' => 'OK',
        ]);

        $response->assertRedirect(route('shows.show', $show));
        $this->assertDatabaseHas('shows', [
            'id' => $show->id,
            'contact_name' => 'Nuevo contacto',
            'contact_role' => 'Produccion',
            'contact_phone' => '600123123',
            'contact_email' => 'contacto@test.local',
        ]);
    }

    public function test_admin_can_post_internal_message_in_show_section_chat(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'name' => 'Bolo Madrid',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('shows.section-messages.store', $show), [
            'section' => 'sound',
            'message' => 'Revisar patch y monitores antes de viajar.',
        ]);

        $response->assertRedirect(route('shows.show', $show).'#section-chat-sound');
        $this->assertDatabaseHas('show_section_messages', [
            'show_id' => $show->id,
            'section' => 'sound',
            'message' => 'Revisar patch y monitores antes de viajar.',
            'user_id' => $user->id,
            'author_name' => $user->name,
        ]);
    }

    public function test_admin_can_open_shows_calendar_and_see_daily_agenda(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $tour = Tour::create([
            'owner_id' => $user->id,
            'name' => 'Tour Calendario',
            'color' => '#2563EB',
        ]);

        Show::create([
            'owner_id' => $user->id,
            'tour_id' => $tour->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'Sala 1',
            'name' => 'Bolo agenda',
            'status' => 'confirmed',
            'show_at' => '21:00:00',
            'lighting_validated' => true,
        ]);

        Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-23',
            'city' => 'Valencia',
            'name' => 'Otro bolo',
            'status' => 'tentative',
        ]);

        $response = $this->actingAs($user)->get(route('shows.calendar', [
            'month' => '2026-05',
            'date' => '2026-05-22',
            'tour_id' => $tour->id,
        ]));

        $response->assertOk();
        $response->assertSee('Calendario de bolos');
        $response->assertSee('Bolo agenda');
        $response->assertSee('Agenda');
        $response->assertDontSee('Otro bolo');
    }

    public function test_show_index_displays_alerts_and_unread_message_count(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => now()->addDays(5)->toDateString(),
            'city' => 'Madrid',
            'name' => 'Bolo con mensajes',
            'status' => 'tentative',
        ]);

        ShowSectionMessage::create([
            'show_id' => $show->id,
            'section' => 'general',
            'message' => 'Falta cerrar horarios definitivos.',
            'user_id' => $user->id,
            'author_name' => $user->name,
        ]);

        ShowMessageRead::create([
            'show_id' => $show->id,
            'user_id' => $user->id,
            'last_read_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get(route('shows.index'));

        $response->assertOk();
        $response->assertSee('3 alertas');
        $response->assertSee('1 mensajes nuevos');
    }

    public function test_show_index_respects_user_alert_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserAlertSetting::create([
            'user_id' => $user->id,
            'core_info_enabled' => false,
            'status_enabled' => false,
            'validations_enabled' => true,
            'validations_days' => 7,
        ]);

        Show::create([
            'owner_id' => $user->id,
            'date' => now()->addDays(5)->toDateString(),
            'city' => 'Madrid',
            'name' => 'Bolo con ajustes',
            'status' => 'tentative',
        ]);

        $response = $this->actingAs($user)->get(route('shows.index'));

        $response->assertOk();
        $response->assertSee('1 alertas');
        $response->assertDontSee('3 alertas');
    }

    public function test_admin_cannot_open_other_users_show(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->assignRole('admin');
        $otherUser->assignRole('admin');

        $show = Show::create([
            'owner_id' => $otherUser->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'name' => 'Bolo ajeno',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get(route('shows.show', $show));

        $response->assertNotFound();
    }
}
