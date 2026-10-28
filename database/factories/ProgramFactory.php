<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'level' => 'Beginner',
            'goal' => 'cutting',
            'usia' => 28,
            'berat_kg' => 72,
            'tinggi_cm' => 175,
            'pengalaman_bulan' => 12,
            'gender' => 'Male',
            'split' => 'Full body A/B',
            'kalori' => 2200,
            'protein_g' => 144,
            'lemak_g' => 65,
            'karbo_g' => 260,
            'air_ml' => 2500,
            'sesi_per_minggu' => 3,
            'schedule_days' => ['Senin', 'Rabu', 'Jumat'],
            'mulai_pada' => now()->startOfWeek()->toDateString(),
            'aktif' => true,
            'consent_at' => now(),
        ];
    }
}
