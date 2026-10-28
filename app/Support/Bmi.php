<?php

namespace App\Support;

final class Bmi
{
    public static function value(float $weightKg, float $heightCm): float
    {
        $heightMeters = $heightCm / 100;

        if ($heightMeters <= 0) {
            return 0.0;
        }

        return round($weightKg / ($heightMeters ** 2), 2);
    }

    /**
     * @return array{label: string, tone: string, range: string}
     */
    public static function category(float $bmi): array
    {
        return match (true) {
            $bmi < 18.5 => ['label' => 'Kurang', 'tone' => 'orange', 'range' => '< 18,5'],
            $bmi < 25 => ['label' => 'Normal', 'tone' => 'green', 'range' => '18,5–24,9'],
            $bmi < 30 => ['label' => 'Berlebih', 'tone' => 'orange', 'range' => '25–29,9'],
            default => ['label' => 'Obesitas', 'tone' => 'red', 'range' => '>= 30'],
        };
    }
}
