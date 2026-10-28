<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RekomendasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'usia' => ['required', 'integer', 'between:14,80'],
            'tinggi' => ['required', 'numeric', 'between:120,220'],
            'berat' => ['required', 'numeric', 'between:30,200'],
            'pengalaman_bulan' => ['required', 'integer', 'between:0,600'],
            'tujuan' => ['required', Rule::in(['cutting', 'bulking', 'strength'])],

            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'workout_type' => ['nullable', Rule::in(['Strength', 'Cardio', 'Yoga', 'HIIT'])],
            'max_bpm' => ['nullable', 'integer', 'between:100,230'],
            'avg_bpm' => ['nullable', 'integer', 'between:80,210'],
            'resting_bpm' => ['nullable', 'integer', 'between:35,120'],
            'session_duration' => ['nullable', 'numeric', 'between:0.25,4'],
            'calories_burned' => ['nullable', 'numeric', 'between:100,3000'],
            'fat_percentage' => ['nullable', 'numeric', 'between:3,60'],
            'water_intake' => ['nullable', 'numeric', 'between:0.5,6'],
            'workout_frequency' => ['nullable', 'integer', 'between:0,7'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'numeric' => ':attribute harus berupa angka.',
            'between' => ':attribute harus berada di antara :min dan :max.',
            'in' => 'Pilihan :attribute tidak valid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'usia' => 'usia',
            'tinggi' => 'tinggi badan',
            'berat' => 'berat badan',
            'pengalaman_bulan' => 'pengalaman gym',
            'tujuan' => 'tujuan latihan',
            'gender' => 'gender',
            'workout_type' => 'tipe latihan',
            'max_bpm' => 'Max BPM',
            'avg_bpm' => 'Avg BPM',
            'resting_bpm' => 'Resting BPM',
            'session_duration' => 'durasi sesi',
            'calories_burned' => 'kalori terbakar',
            'fat_percentage' => 'persentase lemak',
            'water_intake' => 'asupan air',
            'workout_frequency' => 'frekuensi latihan',
        ];
    }
}
