<?php

namespace Tests\Feature;

use App\Models\PlanSession;
use App\Models\Program;
use App\Models\User;
use App\Models\WeeklyCheckin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_cannot_access_progress_or_submit_checkins(): void
    {
        $this->get(route('progres.index'))->assertRedirect(route('login'));
        $this->post(route('progres.checkin'), ['minggu_ke' => 1, 'berat_kg' => 70])
            ->assertRedirect(route('login'));
    }

    public function test_checkins_require_an_active_program(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('progres.checkin'), ['minggu_ke' => 1, 'berat_kg' => 70])
            ->assertRedirect(route('program.show'))
            ->assertSessionHas('status');

        $this->assertDatabaseCount('weekly_checkins', 0);
    }

    public function test_a_weekly_checkin_can_be_created_and_updated(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);

        $this->actingAs($user)
            ->post(route('progres.checkin'), [
                'minggu_ke' => 1,
                'berat_kg' => 79.4,
                'pinggang_cm' => 83,
                'energi' => 4,
                'catatan' => 'Tidur cukup.',
            ])
            ->assertRedirect(route('progres.index'));

        $this->assertDatabaseHas('weekly_checkins', [
            'program_id' => $program->id,
            'minggu_ke' => 1,
            'berat_kg' => 79.4,
        ]);

        $this->actingAs($user)
            ->post(route('progres.checkin'), [
                'minggu_ke' => 1,
                'berat_kg' => 78.8,
                'pinggang_cm' => 82.5,
                'energi' => 5,
                'catatan' => 'Minggu yang lebih baik.',
            ])
            ->assertRedirect(route('progres.index'));

        $this->assertDatabaseCount('weekly_checkins', 1);
        $this->assertDatabaseHas('weekly_checkins', [
            'program_id' => $program->id,
            'minggu_ke' => 1,
            'berat_kg' => 78.8,
            'energi' => 5,
        ]);
    }

    public function test_the_progress_page_shows_charts_after_two_checkins(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);

        WeeklyCheckin::factory()->for($program)->create([
            'minggu_ke' => 1,
            'berat_kg' => 80,
            'pinggang_cm' => 85,
        ]);
        WeeklyCheckin::factory()->for($program)->create([
            'minggu_ke' => 2,
            'berat_kg' => 79.2,
            'pinggang_cm' => 84,
        ]);

        $response = $this->actingAs($user)->get(route('progres.index'));

        $response->assertOk();
        $response->assertSee('Tracking berat');
        $response->assertSee('Lingkar tubuh');
        $response->assertSee('Konsistensi 12 minggu');
        $response->assertSee('data-chart="weight"', false);
        $response->assertSee('data-chart="combination"', false);
        $response->assertDontSee('chart-skeleton');
    }

    public function test_the_progress_page_shows_charts_after_the_first_checkin(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);

        WeeklyCheckin::factory()->for($program)->create([
            'minggu_ke' => 1,
            'berat_kg' => 80,
            'pinggang_cm' => 85,
        ]);

        $response = $this->actingAs($user)->get(route('progres.index'));

        $response->assertOk();
        $response->assertSee('Lingkar tubuh');
        $response->assertDontSee('Sesi & lingkar tubuh');
        $response->assertSee('data-chart="weight"', false);
        $response->assertSee('data-chart="combination"', false);
        $response->assertDontSee('chart-skeleton');
    }

    public function test_the_progress_page_has_an_empty_state_without_a_program(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('progres.index'))
            ->assertOk()
            ->assertSee('Belum ada')
            ->assertSee('data progres');
    }

    public function test_a_session_can_be_marked_complete_and_undone(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);
        $session = $this->sessionFor($program);

        $this->actingAs($user)
            ->patch(route('jadwal.complete', $session))
            ->assertRedirect();

        $this->assertNotNull($session->fresh()->completed_at);

        $this->actingAs($user)
            ->patch(route('jadwal.complete', $session))
            ->assertRedirect();

        $this->assertNull($session->fresh()->completed_at);
    }

    public function test_a_user_cannot_complete_another_users_session(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $program = $this->programFor($owner);
        $session = $this->sessionFor($program);

        $this->actingAs($otherUser)
            ->patch(route('jadwal.complete', $session))
            ->assertForbidden();

        $this->assertNull($session->fresh()->completed_at);
    }

    public function test_the_schedule_page_lists_the_four_week_plan(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);
        $this->sessionFor($program);

        $response = $this->actingAs($user)->get(route('jadwal.index'));

        $response->assertOk();
        $response->assertSee('Pola 7 hari');
        $response->assertSee('Minggu 4');
        $response->assertSee('Deload');
    }

    public function test_sessions_after_the_current_week_cannot_be_marked_complete(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);
        $session = PlanSession::factory()->for($program)->create([
            'minggu_ke' => 2,
            'hari' => 'Senin',
            'tanggal' => now()->startOfWeek()->addWeek()->toDateString(),
            'tipe' => 'latihan',
        ]);

        $this->actingAs($user)
            ->patch(route('jadwal.complete', $session))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertNull($session->fresh()->completed_at);
    }

    public function test_sessions_from_the_previous_week_can_still_be_marked_complete(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);
        $program->update(['mulai_pada' => now()->startOfWeek()->subWeek()->toDateString()]);

        $session = PlanSession::factory()->for($program)->create([
            'minggu_ke' => 1,
            'hari' => 'Senin',
            'tanggal' => now()->startOfWeek()->subWeek()->toDateString(),
            'tipe' => 'latihan',
        ]);

        $this->actingAs($user)
            ->patch(route('jadwal.complete', $session))
            ->assertRedirect();

        $this->assertNotNull($session->fresh()->completed_at);
    }

    public function test_the_consistency_heatmap_includes_upcoming_program_weeks(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);
        $upcoming = now()->startOfWeek()->addWeek();
        $program->update(['mulai_pada' => $upcoming->toDateString()]);

        PlanSession::factory()->for($program)->create([
            'minggu_ke' => 1,
            'hari' => 'Senin',
            'tanggal' => $upcoming->toDateString(),
            'tipe' => 'latihan',
        ]);

        $response = $this->actingAs($user)->get(route('progres.index'));

        $response->assertOk();
        $response->assertSee($upcoming->format('d/m/Y'));
    }

    public function test_optional_cardio_does_not_count_toward_the_weekly_session_target(): void
    {
        $user = User::factory()->create();
        $program = $this->programFor($user);
        $this->sessionFor($program);

        PlanSession::factory()->for($program)->create([
            'minggu_ke' => 1,
            'hari' => 'Selasa',
            'tanggal' => now()->startOfWeek()->addDay()->toDateString(),
            'tipe' => 'kardio',
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('progres.index'))
            ->assertOk()
            ->assertSee('0/1 sesi');

        $this->actingAs($user)
            ->get(route('jadwal.index'))
            ->assertOk()
            ->assertSee('Kardio · opsional')
            ->assertSee('0 / 1 sesi selesai');
    }

    private function programFor(User $user): Program
    {
        return Program::factory()->for($user)->create([
            'mulai_pada' => now()->startOfWeek()->toDateString(),
            'sesi_per_minggu' => 3,
            'schedule_days' => ['Senin', 'Rabu', 'Jumat'],
            'aktif' => true,
        ]);
    }

    private function sessionFor(Program $program): PlanSession
    {
        return PlanSession::factory()->for($program)->create([
            'minggu_ke' => 1,
            'hari' => 'Senin',
            'tanggal' => now()->startOfWeek()->toDateString(),
            'tipe' => 'latihan',
        ]);
    }
}
