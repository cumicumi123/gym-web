<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\WeeklyCheckin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeeklyCheckin>
 */
class WeeklyCheckinFactory extends Factory
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
            'berat_kg' => 72,
            'pinggang_cm' => 82,
            'energi' => 3,
            'catatan' => null,
        ];
    }
}
