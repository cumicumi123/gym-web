<?php

namespace App\Services;

use App\Support\Bmi;

class NutritionCalculator
{
    /**
     * Hitung perkiraan kebutuhan kalori dan makro dari profil pengguna.
     *
     * @return array{
     *     bmi: float,
     *     bmi_category: string,
     *     bmr: int,
     *     tdee: int,
     *     activity_factor: float,
     *     calories: int,
     *     calories_min: int,
     *     calories_max: int,
     *     protein_g: int,
     *     protein_min_g: int,
     *     protein_max_g: int,
     *     fat_g: int,
     *     fat_min_g: int,
     *     fat_max_g: int,
     *     carbs_g: int,
     *     carbs_min_g: int,
     *     carbs_max_g: int,
     *     fiber_min_g: int,
     *     fiber_max_g: int,
     *     water_ml: int,
     *     water_min_ml: int,
     *     water_max_ml: int,
     *     notes: array<int, string>,
     *     warnings: array<int, string>,
     *     is_adult: bool,
     *     is_underweight: bool,
     *     goal: string,
     *     disclaimer: string
     * }
     */
    public function calculate(
        float $weightKg,
        float $heightCm,
        int $age,
        ?string $gender,
        string $goal,
        int $sessionsPerWeek,
    ): array {
        $gender = in_array($gender, ['Male', 'Female'], true) ? $gender : null;
        $bmi = Bmi::value($weightKg, $heightCm);
        $bmiCategory = Bmi::category($bmi);
        $isAdult = $age >= 18;
        $isUnderweight = $bmi < 18.5;

        $genderOffset = match ($gender) {
            'Male' => 5,
            'Female' => -161,
            default => -78,
        };

        $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + $genderOffset;
        $activityFactor = match (true) {
            $sessionsPerWeek <= 2 => 1.375,
            $sessionsPerWeek <= 4 => 1.55,
            default => 1.725,
        };
        $tdee = $bmr * $activityFactor;

        $notes = [];
        $warnings = [];

        if ($gender === null) {
            $notes[] = 'Gender belum diisi, jadi BMR dihitung dengan nilai netral. Isi gender untuk perkiraan yang lebih dekat.';
        }

        $target = $tdee;
        $rangeLow = $tdee;
        $rangeHigh = $tdee;

        if (! $isAdult) {
            $notes[] = 'Usia di bawah 18 tahun: kami tidak memberi target defisit atau surplus. Fokus pada pola makan seimbang, protein cukup, air, dan tidur yang cukup.';
            $warnings[] = 'Bicarakan rencana makan dan latihanmu dengan orang tua, dokter, atau pelatih sebelum mengubah asupan secara besar.';
        } elseif ($isUnderweight && $goal === 'cutting') {
            $notes[] = 'BMI-mu di bawah 18,5. Menurunkan berat badan lagi bukan prioritas; arahkan ke pola makan pemeliharaan atau surplus ringan.';
            $rangeLow = $tdee;
            $rangeHigh = $tdee + 150;
        } else {
            if ($goal === 'cutting') {
                $maxDeficit = $tdee * 0.20;
                $targetDeficit = min(400, $maxDeficit);
                $lowDeficit = min(300, $maxDeficit);
                $highDeficit = min(500, $maxDeficit);
                $target = $tdee - $targetDeficit;
                $rangeLow = $tdee - $highDeficit;
                $rangeHigh = $tdee - $lowDeficit;
            } elseif ($goal === 'bulking') {
                $target = $tdee + 300;
                $rangeLow = $tdee + 250;
                $rangeHigh = $tdee + 400;
            } else {
                $target = $tdee + 75;
                $rangeLow = $tdee;
                $rangeHigh = $tdee + 150;
            }
        }

        $genderFloor = $gender === 'Male' ? 1500 : 1200;
        $floor = max($bmr, $genderFloor);
        $floorCalories = (int) (ceil($floor / 10) * 10);

        $calories = max($this->roundToTen($target), $floorCalories);
        $caloriesMin = max($this->roundToTen($rangeLow), $floorCalories);
        $caloriesMax = max($this->roundToTen($rangeHigh), $caloriesMin);

        if ($this->roundToTen($target) < $floorCalories) {
            $notes[] = 'Hasil hitungan di bawah batas aman, jadi target kalori dinaikkan ke '.$floorCalories.' kkal. Jangan makan di bawah BMR-mu.';
        }

        $proteinFactor = $goal === 'cutting' ? 2.0 : 1.8;
        $proteinMinFactor = $goal === 'cutting' ? 1.8 : 1.6;
        $proteinMaxFactor = $goal === 'cutting' ? 2.2 : 2.0;

        $protein = (int) round($weightKg * $proteinFactor);
        $proteinMin = (int) round($weightKg * $proteinMinFactor);
        $proteinMax = (int) round($weightKg * $proteinMaxFactor);

        $fat = (int) round($weightKg * 0.9);
        $fatMin = (int) round($weightKg * 0.8);
        $fatMax = (int) round($weightKg * 1.0);

        $carbs = $this->carbsFromCalories($calories, $protein, $fat);
        $carbsMin = $this->carbsFromCalories($caloriesMin, $protein, $fat);
        $carbsMax = $this->carbsFromCalories($caloriesMax, $protein, $fat);

        return [
            'bmi' => $bmi,
            'bmi_category' => $bmiCategory['label'],
            'bmr' => (int) round($bmr),
            'tdee' => (int) round($tdee),
            'activity_factor' => $activityFactor,
            'calories' => $calories,
            'calories_min' => $caloriesMin,
            'calories_max' => $caloriesMax,
            'protein_g' => $protein,
            'protein_min_g' => $proteinMin,
            'protein_max_g' => $proteinMax,
            'fat_g' => $fat,
            'fat_min_g' => $fatMin,
            'fat_max_g' => $fatMax,
            'carbs_g' => $carbs,
            'carbs_min_g' => $carbsMin,
            'carbs_max_g' => $carbsMax,
            'fiber_min_g' => 25,
            'fiber_max_g' => 35,
            'water_ml' => (int) round($weightKg * 35),
            'water_min_ml' => (int) round($weightKg * 30),
            'water_max_ml' => (int) round($weightKg * 40),
            'notes' => $notes,
            'warnings' => $warnings,
            'is_adult' => $isAdult,
            'is_underweight' => $isUnderweight,
            'goal' => $goal,
            'disclaimer' => 'Bukan saran medis. Angka ini perkiraan untuk edukasi.',
        ];
    }

    private function roundToTen(float $value): int
    {
        return (int) (round($value / 10) * 10);
    }

    private function carbsFromCalories(int $calories, int $protein, int $fat): int
    {
        $remaining = $calories - ($protein * 4) - ($fat * 9);

        return max(0, (int) round($remaining / 4));
    }
}
