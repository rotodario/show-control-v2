<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_tour(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->post(route('tours.store'), [
            'name' => 'Gira Test 2026',
            'color' => '#0EA5E9',
            'notes' => 'Primera gira de prueba.',
        ]);

        $tour = Tour::first();

        $response->assertRedirect(route('tours.show', $tour));
        $this->assertDatabaseHas('tours', [
            'name' => 'Gira Test 2026',
            'color' => '#0EA5E9',
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'tour.created',
            'tour_id' => $tour->id,
        ]);
    }

    public function test_admin_can_view_tour_index(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $tour = Tour::create([
            'owner_id' => $user->id,
            'name' => 'Ruta Primavera',
            'color' => '#2563EB',
            'notes' => 'Notas base',
        ]);

        $response = $this->actingAs($user)->get(route('tours.index'));

        $response->assertOk();
        $response->assertSee($tour->name);
    }

    public function test_admin_only_sees_owned_tours_in_index(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->assignRole('admin');
        $otherUser->assignRole('admin');

        Tour::create([
            'owner_id' => $user->id,
            'name' => 'Mi gira',
            'color' => '#2563EB',
        ]);

        Tour::create([
            'owner_id' => $otherUser->id,
            'name' => 'Gira ajena',
            'color' => '#0EA5E9',
        ]);

        $response = $this->actingAs($user)->get(route('tours.index'));

        $response->assertOk();
        $response->assertSee('Mi gira');
        $response->assertDontSee('Gira ajena');
    }
}
