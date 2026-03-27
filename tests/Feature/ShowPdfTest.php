<?php

namespace Tests\Feature;

use App\Models\UserPdfSetting;
use App\Models\Show;
use App\Models\Tour;
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

    public function test_pdf_template_uses_user_pdf_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserPdfSetting::create([
            'user_id' => $user->id,
            'brand_name' => 'Roto Touring',
            'primary_color' => '#1d4ed8',
            'header_text' => 'Produccion tecnica',
            'footer_text' => 'Documento interno',
            'show_generated_at' => false,
        ]);

        $tour = Tour::create([
            'owner_id' => $user->id,
            'name' => 'Gira PDF',
            'color' => '#2563EB',
        ]);

        $show = Show::create([
            'owner_id' => $user->id,
            'tour_id' => $tour->id,
            'date' => '2026-05-22',
            'city' => 'Madrid',
            'venue' => 'Sala Test',
            'name' => 'Bolo PDF',
            'status' => 'confirmed',
            'general_notes' => 'Notas para PDF',
        ]);

        $html = view('shows.pdf.roadmap', [
            'show' => $show->load('tour.contacts'),
            'statusOptions' => Show::STATUS_OPTIONS,
            'alerts' => [],
            'pdfSettings' => $user->pdfSettings,
        ])->render();

        $this->assertStringContainsString('Roto Touring', $html);
        $this->assertStringContainsString('Produccion tecnica', $html);
        $this->assertStringContainsString('Documento interno', $html);
        $this->assertStringNotContainsString('Generado el', $html);
    }
}
