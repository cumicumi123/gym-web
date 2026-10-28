<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_cannot_save_a_program(): void
    {
        $this->post(route('program.store'), $this->payload())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('programs', 0);
    }

    public function test_a_user_can_save_a_program_and_its_four_week_schedule(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('program.store'), $this->payload());

        $response->assertRedirect(route('program.show'));
        $response->assertSessionHas('status');

        $program = $user->programs()->firstOrFail();

        $this->assertTrue($program->aktif);
        $this->assertSame('Intermediate', $program->level);
        $this->assertSame('cutting', $program->goal);
        $this->assertSame(4, $program->sesi_per_minggu);
        $this->assertSame(['Senin', 'Selasa', 'Kamis', 'Jumat'], $program->schedule_days);
        $this->assertSame(2360, $program->kalori);
        $this->assertSame(160, $program->protein_g);
        $this->assertNotNull($program->consent_at);
        $this->assertCount(28, $program->planSessions);
        $this->assertSame(4, $program->planSessions->max('minggu_ke'));
        $this->assertSame(4, $program->planSessions->where('tipe', 'latihan')->where('minggu_ke', 1)->count());
    }

    public function test_saving_a_new_program_deactivates_the_previous_one(): void
    {
        $user = User::factory()->create();
        $oldProgram = Program::factory()->for($user)->create(['aktif' => true]);

        $this->actingAs($user)
            ->post(route('program.store'), $this->payload())
            ->assertRedirect(route('program.show'));

        $this->assertFalse($oldProgram->fresh()->aktif);
        $this->assertSame(1, $user->programs()->where('aktif', true)->count());
    }

    public function test_consent_is_required_to_save_a_program(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();
        unset($payload['consent']);

        $this->actingAs($user)
            ->from(route('rekomendasi.create'))
            ->post(route('program.store'), $payload)
            ->assertRedirect(route('rekomendasi.create'))
            ->assertSessionHasErrors('consent');

        $this->assertDatabaseCount('programs', 0);
    }

    public function test_the_program_page_shows_nutrition_and_schedule_notes(): void
    {
        $user = User::factory()->create();
        Program::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('program.show'));

        $response->assertOk();
        $response->assertSee('Perkiraan kebutuhan harian');
        $response->assertSee('Bukan saran medis.');
        $response->assertSee('Ringkasan program');
    }

    public function test_the_program_page_has_an_empty_state_without_a_saved_program(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('program.show'))
            ->assertOk()
            ->assertSee('Belum ada')
            ->assertSee('program aktif');
    }

    public function test_authenticated_users_see_the_save_form_on_the_result_page(): void
    {
        Http::fake([
            '*/predict' => Http::response([
                'kategori' => 'Beginner',
                'label' => 1,
                'probabilitas' => 0.7,
                'probabilities' => [
                    'Beginner' => 0.7,
                    'Intermediate' => 0.2,
                    'Advanced' => 0.1,
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('rekomendasi.store'), [
            'usia' => 28,
            'berat' => 72,
            'tinggi' => 175,
            'pengalaman_bulan' => 18,
            'tujuan' => 'cutting',
            'workout_frequency' => 4,
            'gender' => 'Male',
        ]);

        $response->assertOk();
        $response->assertViewIs('rekomendasi.hasil');
        $response->assertSee('Simpan sebagai program');
        $response->assertSee('name="consent"', false);
        $response->assertSee('name="schedule_days[]"', false);
    }

    public function test_home_page_shows_the_active_program_profile_for_the_user(): void
    {
        Http::fake(['*/metrics' => Http::response([], 200)]);
        Cache::flush();

        $user = User::factory()->create();
        Program::factory()->for($user)->create([
            'usia' => 34,
            'berat_kg' => 80,
            'tinggi_cm' => 180,
            'pengalaman_bulan' => 18,
            'goal' => 'bulking',
            'level' => 'Intermediate',
            'split' => 'Upper/Lower',
            'sesi_per_minggu' => 4,
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('34 tahun')
            ->assertSee('80 kg')
            ->assertSee('180 cm')
            ->assertSee('Bulking')
            ->assertSee('18 bulan')
            ->assertSee('Menengah')
            ->assertSee('Upper/Lower');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'level' => 'Intermediate',
            'goal' => 'cutting',
            'usia' => 30,
            'berat_kg' => 80,
            'tinggi_cm' => 180,
            'pengalaman_bulan' => 18,
            'gender' => 'Male',
            'split' => 'Upper Lower',
            'sesi_per_minggu' => 4,
            'schedule_days' => ['Senin', 'Selasa', 'Kamis', 'Jumat'],
            'consent' => 1,
        ];
    }
}
