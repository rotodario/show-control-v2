<?php

namespace Tests\Feature;

use App\Models\Show;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShowTravelRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_displays_google_maps_route_summary(): void
    {
        Cache::flush();
        $this->seed(RolesAndPermissionsSeeder::class);

        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::sequence()
                ->push([['lat' => '40.4066', 'lon' => '-3.6892']])
                ->push([['lat' => '40.4362', 'lon' => '-3.5995']]),
            'https://router.project-osrm.org/*' => Http::response([
                'routes' => [[
                    'duration' => 5400,
                    'distance' => 125000,
                    'geometry' => [
                        'coordinates' => [[-3.6892, 40.4066], [-3.5995, 40.4362]],
                        'type' => 'LineString',
                    ],
                ]],
            ]),
        ]);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'WiZink Center',
            'travel_origin' => 'Atocha, Madrid',
            'travel_mode' => 'van',
            'name' => 'Bolo ruta',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get(route('shows.show', $show));

        $response->assertOk();
        $response->assertSee('Ruta al venue');
        $response->assertSee('1 h 30 min');
        $response->assertSee('125,0 km');
        $response->assertSee('Atocha, Madrid');
        $response->assertSee('WiZink Center, Madrid');
        $response->assertSee('Abrir ruta');
        $response->assertSee('Furgo');
        $response->assertSee('show-route-map', false);
    }

    public function test_show_page_displays_manual_flight_data_when_travel_mode_is_plane(): void
    {
        Cache::flush();
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'WiZink Center',
            'travel_origin' => 'Madrid',
            'travel_mode' => 'plane',
            'flight_origin' => 'MAD',
            'flight_destination' => 'BCN',
            'flight_duration_estimate' => '1 h 20 min',
            'flight_notes' => 'Citar equipo 2 horas antes en terminal T4.',
            'name' => 'Bolo vuelo',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get(route('shows.show', $show));

        $response->assertOk();
        $response->assertSee('Avion');
        $response->assertSee('MAD');
        $response->assertSee('BCN');
        $response->assertSee('1 h 20 min');
        $response->assertSee('terminal T4');
    }

    public function test_edit_route_preview_calculates_without_persisting_show_changes(): void
    {
        Cache::flush();
        $this->seed(RolesAndPermissionsSeeder::class);

        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::sequence()
                ->push([['lat' => '40.4066', 'lon' => '-3.6892']])
                ->push([['lat' => '41.3851', 'lon' => '2.1734']]),
            'https://router.project-osrm.org/*' => Http::response([
                'routes' => [[
                    'duration' => 21600,
                    'distance' => 620000,
                    'geometry' => [
                        'coordinates' => [[-3.6892, 40.4066], [2.1734, 41.3851]],
                        'type' => 'LineString',
                    ],
                ]],
            ]),
        ]);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'Venue inicial',
            'travel_origin' => 'Origen inicial',
            'travel_mode' => 'van',
            'name' => 'Bolo preview',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->from(route('shows.edit', $show))->put(route('shows.preview-route', $show), [
            'date' => '2026-05-22',
            'city' => 'Barcelona',
            'venue' => 'Palau Sant Jordi',
            'travel_origin' => 'Atocha, Madrid',
            'travel_mode' => 'van',
            'name' => 'Bolo preview',
            'status' => 'confirmed',
        ]);

        $response->assertRedirect(route('shows.edit', $show));
        $this->assertDatabaseHas('shows', [
            'id' => $show->id,
            'city' => 'Madrid',
            'venue' => 'Venue inicial',
            'travel_origin' => 'Origen inicial',
        ]);

        $editResponse = $this->actingAs($user)->get(route('shows.edit', $show));
        $editResponse->assertSee('Resultado del calculo de ruta');
        $editResponse->assertSee('6 h');
        $editResponse->assertSee('620,0 km');
        $editResponse->assertSee('Palau Sant Jordi');
    }
}
