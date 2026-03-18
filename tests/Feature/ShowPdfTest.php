<?php

namespace Tests\Feature;

use App\Models\Show;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_show_pdf(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'Sala Test',
            'name' => 'Bolo PDF',
            'status' => 'confirmed',
            'general_notes' => 'Notas para PDF',
        ]);

        $response = $this->actingAs($user)->get(route('shows.pdf', $show));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
