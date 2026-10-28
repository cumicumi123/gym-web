<?php

namespace Database\Factories;

use App\Models\PlanSession;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlanSession>
 */
class PlanSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'minggu_ke' => 1,
            'hari' => 'Senin',
            'tanggal' => now()->startOfWeek()->toDateString(),
            'fokus' => 'Full Body A',
            'tipe' => 'latihan',
            'template_key' => 'full_a',
            'detail' => [
                'is_deload' => false,
                'catatan' => null,
                'exercises' => [
                    ['nama' => 'Squat', 'set' => 3, 'rep' => '8-12', 'rpe' => '6'],
                ],
            ],
            'completed_at' => null,
        ];
    }
}
