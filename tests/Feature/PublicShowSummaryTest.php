<?php

namespace Tests\Feature;

use App\Models\Show;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicShowSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_gets_public_summary_token_on_create(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-07-15',
            'city' => 'Madrid',
            'venue' => 'La Riviera',
            'name' => 'Madrid Show',
            'status' => 'confirmed',
        ]);

        $this->assertNotEmpty($show->public_summary_token);
        $this->assertSame(route('public-shows.show', $show->public_summary_token), $show->publicSummaryUrl());
    }

    public function test_public_show_summary_is_available_without_login(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '40.4168', 'lon' => '-3.7038'],
            ], 200),
            'https://router.project-osrm.org/*' => Http::response([
                'routes' => [[
                    'duration' => 7200,
                    'distance' => 620000,
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => [[-3.7038, 40.4168], [2.1734, 41.3851]],
                    ],
                ]],
            ], 200),
        ]);

        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-07-15',
            'city' => 'Barcelona',
            'venue' => 'Palau Sant Jordi',
            'travel_origin' => 'Madrid',
            'travel_mode' => 'van',
            'contact_name' => 'Promotor',
            'contact_phone' => '600123123',
            'contact_email' => 'promotor@example.com',
            'name' => 'Barcelona Show',
            'status' => 'confirmed',
            'general_notes' => 'Notas visibles',
        ]);

        $response = $this->get(route('public-shows.show', $show->public_summary_token));

        $response->assertOk();
        $response->assertSee('Barcelona Show');
        $response->assertSee('Palau Sant Jordi');
        $response->assertSee('Notas visibles');
    }
}
