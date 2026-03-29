<?php

namespace Tests\Feature;

use App\Models\SharedAccess;
use App\Models\Show;
use App\Models\ShowMessageRead;
use App\Models\ShowSectionMessage;
use App\Models\Tour;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SharedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_shared_access(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $tour = Tour::create([
            'owner_id' => $user->id,
            'name' => 'Gira compartida',
            'color' => '#2563EB',
        ]);

        $response = $this->actingAs($user)->post(route('shared-accesses.store'), [
            'label' => 'Tecnico luces',
            'role' => 'lighting',
            'tour_id' => $tour->id,
        ]);

        $response->assertRedirect(route('shared-accesses.index'));
        $this->assertDatabaseHas('shared_accesses', [
            'label' => 'Tecnico luces',
            'role' => 'lighting',
            'tour_id' => $tour->id,
        ]);
    }

    public function test_public_shared_access_can_view_allowed_show_without_login(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira compartida',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo compartido',
            'status' => 'confirmed',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso PM',
            'role' => 'project_manager',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $response = $this->get(route('public-access.shows.show', [$grant->token, $show]));

        $response->assertOk();
        $response->assertSee('Bolo compartido');
    }

    public function test_public_shared_access_index_displays_mini_calendar_and_can_filter_by_day(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira compartida',
            'color' => '#2563EB',
        ]);

        $show12 = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo 12',
            'status' => 'confirmed',
        ]);

        $show18 = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-18',
            'city' => 'Sevilla',
            'name' => 'Bolo 18',
            'status' => 'confirmed',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso PM',
            'role' => 'project_manager',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $response = $this->get(route('public-access.index', [
            'token' => $grant->token,
            'month' => '2026-06',
            'date' => '2026-06-12',
        ]));

        $response->assertOk();
        $response->assertSee('Bolo 12');
        $response->assertSee(route('public-access.shows.show', [$grant->token, $show12]), false);
        $response->assertDontSee(route('public-access.shows.show', [$grant->token, $show18]), false);
        $response->assertSee(__('ui.calendar'));
    }

    public function test_public_shared_access_cannot_view_show_from_other_tour(): void
    {
        $allowedTour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira 1',
            'color' => '#2563EB',
        ]);

        $otherTour = Tour::create([
            'owner_id' => User::factory()->create()->id,
            'name' => 'Gira 2',
            'color' => '#0EA5E9',
        ]);

        $forbiddenShow = Show::create([
            'owner_id' => $otherTour->owner_id,
            'tour_id' => $otherTour->id,
            'date' => '2026-06-12',
            'city' => 'Sevilla',
            'name' => 'Bolo oculto',
            'status' => 'confirmed',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso sound',
            'role' => 'sound',
            'tour_id' => $allowedTour->id,
            'created_by' => $owner,
        ]);

        $response = $this->get(route('public-access.shows.show', [$grant->token, $forbiddenShow]));

        $response->assertNotFound();
    }

    public function test_revoked_shared_access_is_not_available_publicly(): void
    {
        $grant = SharedAccess::create([
            'label' => 'Revocado',
            'role' => 'lighting',
            'revoked_at' => now(),
        ]);

        $response = $this->get(route('public-access.index', $grant->token));

        $response->assertNotFound();
    }

    public function test_project_manager_can_create_and_delete_shows_via_shared_access(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira PM',
            'color' => '#2563EB',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso PM',
            'role' => 'project_manager',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $createResponse = $this->post(route('public-access.shows.store', $grant->token), [
            'date' => '2026-09-10',
            'city' => 'Bilbao',
            'venue' => 'Sala PM',
            'name' => 'Bolo nuevo',
            'status' => 'confirmed',
        ]);

        $show = Show::where('name', 'Bolo nuevo')->first();

        $createResponse->assertRedirect(route('public-access.shows.show', [$grant->token, $show]));
        $this->assertDatabaseHas('shows', [
            'id' => $show->id,
            'tour_id' => $tour->id,
            'city' => 'Bilbao',
            'owner_id' => $owner,
        ]);

        $deleteResponse = $this->delete(route('public-access.shows.destroy', [$grant->token, $show]));

        $deleteResponse->assertRedirect(route('public-access.index', $grant->token));
        $this->assertDatabaseMissing('shows', [
            'id' => $show->id,
        ]);
    }

    public function test_stage_manager_can_edit_show_but_cannot_create_or_delete_it(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira Stage',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo stage',
            'status' => 'confirmed',
            'general_notes' => 'Antes',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso Stage',
            'role' => 'stage_manager',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $updateResponse = $this->put(route('public-access.shows.update', [$grant->token, $show]), [
            'name' => 'Bolo stage editado',
            'date' => '2026-06-13',
            'city' => 'Barcelona',
            'status' => 'closed',
            'general_notes' => 'Despues',
        ]);

        $updateResponse->assertRedirect(route('public-access.shows.show', [$grant->token, $show]));
        $this->assertDatabaseHas('shows', [
            'id' => $show->id,
            'name' => 'Bolo stage editado',
            'city' => 'Barcelona',
            'general_notes' => 'Despues',
        ]);

        $createResponse = $this->post(route('public-access.shows.store', $grant->token), [
            'date' => '2026-09-10',
            'city' => 'Bilbao',
            'name' => 'Intento crear',
            'status' => 'confirmed',
        ]);

        $createResponse->assertForbidden();

        $deleteResponse = $this->delete(route('public-access.shows.destroy', [$grant->token, $show]));

        $deleteResponse->assertForbidden();
    }

    public function test_stage_manager_can_upload_documents(): void
    {
        Storage::fake('public');

        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira Stage',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo stage',
            'status' => 'confirmed',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso Stage',
            'role' => 'stage_manager',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $response = $this->post(route('public-access.documents.store', [$grant->token, $show]), [
            'document_type' => 'Timing',
            'title' => 'Timing stage',
            'file' => UploadedFile::fake()->create('timing.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('public-access.shows.show', [$grant->token, $show]));
        $this->assertDatabaseHas('show_documents', [
            'show_id' => $show->id,
            'document_type' => 'Timing',
            'title' => 'Timing stage',
        ]);
    }

    public function test_lighting_can_only_edit_lighting_section_and_upload_documents(): void
    {
        Storage::fake('public');

        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira luces',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo luces',
            'status' => 'confirmed',
            'lighting_notes' => 'Anterior',
            'sound_notes' => 'Sound intacto',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso luces',
            'role' => 'lighting',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $updateResponse = $this->put(route('public-access.shows.update', [$grant->token, $show]), [
            'lighting_notes' => 'Luces actualizadas',
            'lighting_validated' => '1',
            'sound_notes' => 'No deberia cambiar',
        ]);

        $updateResponse->assertRedirect(route('public-access.shows.show', [$grant->token, $show]));
        $this->assertDatabaseHas('shows', [
            'id' => $show->id,
            'lighting_notes' => 'Luces actualizadas',
            'lighting_validated' => true,
            'sound_notes' => 'Sound intacto',
        ]);

        $uploadResponse = $this->post(route('public-access.documents.store', [$grant->token, $show]), [
            'document_type' => 'Plano',
            'title' => 'Plano luces',
            'file' => UploadedFile::fake()->create('plano.pdf', 120, 'application/pdf'),
        ]);

        $uploadResponse->assertRedirect(route('public-access.shows.show', [$grant->token, $show]));
        $this->assertDatabaseHas('show_documents', [
            'show_id' => $show->id,
            'document_type' => 'Plano',
            'title' => 'Plano luces',
        ]);
    }

    public function test_sound_can_only_edit_sound_section(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira sound',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo sound',
            'status' => 'confirmed',
            'lighting_notes' => 'Luces intactas',
            'sound_notes' => 'Anterior',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso sound',
            'role' => 'sound',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $response = $this->put(route('public-access.shows.update', [$grant->token, $show]), [
            'sound_notes' => 'Sound actualizado',
            'sound_validated' => '1',
            'lighting_notes' => 'No deberia cambiar',
        ]);

        $response->assertRedirect(route('public-access.shows.show', [$grant->token, $show]));
        $this->assertDatabaseHas('shows', [
            'id' => $show->id,
            'sound_notes' => 'Sound actualizado',
            'sound_validated' => true,
            'lighting_notes' => 'Luces intactas',
        ]);
    }

    public function test_lighting_can_post_messages_in_visible_sections_only(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira luces',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => '2026-06-12',
            'city' => 'Madrid',
            'name' => 'Bolo luces',
            'status' => 'confirmed',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso luces',
            'role' => 'lighting',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        $allowedResponse = $this->post(route('public-access.section-messages.store', [$grant->token, $show]), [
            'section' => 'lighting',
            'message' => 'Necesito confirmar medidas de escenario.',
        ]);

        $allowedResponse->assertRedirect(route('public-access.shows.show', [$grant->token, $show]).'#section-chat-lighting');
        $this->assertDatabaseHas('show_section_messages', [
            'show_id' => $show->id,
            'section' => 'lighting',
            'message' => 'Necesito confirmar medidas de escenario.',
            'shared_access_id' => $grant->id,
            'author_name' => 'Acceso luces (Iluminacion)',
        ]);

        $spaceResponse = $this->post(route('public-access.section-messages.store', [$grant->token, $show]), [
            'section' => 'space',
            'message' => 'Confirmad acceso de camiones y medidas de venue.',
        ]);

        $spaceResponse->assertRedirect(route('public-access.shows.show', [$grant->token, $show]).'#section-chat-space');
        $this->assertDatabaseHas('show_section_messages', [
            'show_id' => $show->id,
            'section' => 'space',
            'message' => 'Confirmad acceso de camiones y medidas de venue.',
            'shared_access_id' => $grant->id,
        ]);

        $forbiddenResponse = $this->post(route('public-access.section-messages.store', [$grant->token, $show]), [
            'section' => 'sound',
            'message' => 'Esto no deberia entrar.',
        ]);

        $forbiddenResponse->assertForbidden();
        $this->assertDatabaseMissing('show_section_messages', [
            'show_id' => $show->id,
            'section' => 'sound',
            'message' => 'Esto no deberia entrar.',
        ]);
    }

    public function test_admin_only_sees_own_shared_accesses(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->assignRole('admin');
        $otherUser->assignRole('admin');

        SharedAccess::create([
            'label' => 'Mi token',
            'role' => 'project_manager',
            'created_by' => $user->id,
        ]);

        SharedAccess::create([
            'label' => 'Token ajeno',
            'role' => 'lighting',
            'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('shared-accesses.index'));

        $response->assertOk();
        $response->assertSee('Mi token');
        $response->assertDontSee('Token ajeno');
    }

    public function test_shared_access_index_shows_unread_message_count_for_visible_sections_and_marks_read_on_open(): void
    {
        $tour = Tour::create([
            'owner_id' => $owner = User::factory()->create()->id,
            'name' => 'Gira sound',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $owner,
            'tour_id' => $tour->id,
            'date' => now()->addDays(10)->toDateString(),
            'city' => 'Madrid',
            'name' => 'Bolo sound',
            'status' => 'tentative',
        ]);

        $grant = SharedAccess::create([
            'label' => 'Acceso sound',
            'role' => 'sound',
            'tour_id' => $tour->id,
            'created_by' => $owner,
        ]);

        ShowSectionMessage::create([
            'show_id' => $show->id,
            'section' => 'sound',
            'message' => 'Hay cambio en monitores.',
            'shared_access_id' => $grant->id,
            'author_name' => 'Acceso sound (Sound)',
        ]);

        ShowSectionMessage::create([
            'show_id' => $show->id,
            'section' => 'lighting',
            'message' => 'Mensaje no visible para sound.',
            'author_name' => 'Admin',
        ]);

        $indexResponse = $this->get(route('public-access.index', $grant->token));

        $indexResponse->assertOk();
        $indexResponse->assertSee('2 alertas');
        $indexResponse->assertSee('1 mensajes nuevos');

        $showResponse = $this->get(route('public-access.shows.show', [$grant->token, $show]));

        $showResponse->assertOk();
        $this->assertDatabaseHas('show_message_reads', [
            'show_id' => $show->id,
            'shared_access_id' => $grant->id,
        ]);

        $afterReadResponse = $this->get(route('public-access.index', $grant->token));

        $afterReadResponse->assertOk();
        $afterReadResponse->assertDontSee('1 mensajes nuevos');
    }
}
