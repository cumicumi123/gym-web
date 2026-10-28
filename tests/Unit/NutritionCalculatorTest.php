<?php

namespace Tests\Unit;

use App\Services\NutritionCalculator;
use PHPUnit\Framework\TestCase;

class NutritionCalculatorTest extends TestCase
{
    public function test_it_calculates_cutting_targets_for_an_adult_male(): void
    {
        $nutrition = (new NutritionCalculator)->calculate(80, 180, 30, 'Male', 'cutting', 4);

        $this->assertSame(1780, $nutrition['bmr']);
        $this->assertSame(2759, $nutrition['tdee']);
        $this->assertSame(2360, $nutrition['calories']);
        $this->assertSame(2260, $nutrition['calories_min']);
        $this->assertSame(2460, $nutrition['calories_max']);
        $this->assertSame(160, $nutrition['protein_g']);
        $this->assertSame(144, $nutrition['protein_min_g']);
        $this->assertSame(176, $nutrition['protein_max_g']);
        $this->assertSame(72, $nutrition['fat_g']);
        $this->assertSame(268, $nutrition['carbs_g']);
        $this->assertSame(2800, $nutrition['water_ml']);
        $this->assertSame(25, $nutrition['fiber_min_g']);
        $this->assertSame(35, $nutrition['fiber_max_g']);
        $this->assertSame([], $nutrition['warnings']);
    }

    public function test_underweight_cutting_gets_maintenance_instead_of_a_deficit(): void
    {
        $nutrition = (new NutritionCalculator)->calculate(50, 180, 25, 'Female', 'cutting', 3);

        $this->assertTrue($nutrition['is_underweight']);
        $this->assertGreaterThanOrEqual($nutrition['tdee'], $nutrition['calories']);
        $this->assertGreaterThanOrEqual($nutrition['tdee'], $nutrition['calories_min']);
        $this->assertStringContainsString('BMI-mu di bawah 18,5', implode(' ', $nutrition['notes']));
    }

    public function test_minors_do_not_receive_a_deficit_or_surplus(): void
    {
        $nutrition = (new NutritionCalculator)->calculate(60, 170, 17, 'Male', 'bulking', 3);

        $this->assertFalse($nutrition['is_adult']);
        $this->assertStringContainsString('Usia di bawah 18 tahun', implode(' ', $nutrition['notes']));
        $this->assertStringContainsString('orang tua, dokter, atau pelatih', implode(' ', $nutrition['warnings']));
        $this->assertSame(2450, $nutrition['calories']);
    }

    public function test_calorie_floor_is_applied_for_a_small_adult_female(): void
    {
        $nutrition = (new NutritionCalculator)->calculate(36.5, 140, 30, 'Female', 'cutting', 3);

        $this->assertSame(1200, $nutrition['calories']);
        $this->assertGreaterThanOrEqual(1200, $nutrition['calories_min']);
        $this->assertStringContainsString('batas aman', implode(' ', $nutrition['notes']));
    }

    public function test_unknown_gender_uses_a_neutral_bmr_note(): void
    {
        $nutrition = (new NutritionCalculator)->calculate(70, 170, 28, null, 'strength', 4);

        $this->assertSame('Gender belum diisi, jadi BMR dihitung dengan nilai netral. Isi gender untuk perkiraan yang lebih dekat.', $nutrition['notes'][0]);
        $this->assertSame(1.55, $nutrition['activity_factor']);
    }

    public function test_activity_factor_changes_with_training_frequency(): void
    {
        $calculator = new NutritionCalculator;

        $this->assertSame(1.375, $calculator->calculate(70, 170, 28, 'Male', 'strength', 2)['activity_factor']);
        $this->assertSame(1.55, $calculator->calculate(70, 170, 28, 'Male', 'strength', 4)['activity_factor']);
        $this->assertSame(1.725, $calculator->calculate(70, 170, 28, 'Male', 'strength', 5)['activity_factor']);
    }
}
