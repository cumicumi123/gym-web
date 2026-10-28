<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'level' => ['required', Rule::in(['Beginner', 'Intermediate', 'Advanced'])],
            'goal' => ['required', Rule::in(['cutting', 'bulking', 'strength'])],
            'usia' => ['required', 'integer', 'between:14,80'],
            'berat_kg' => ['required', 'numeric', 'between:30,200'],
            'tinggi_cm' => ['required', 'numeric', 'between:120,220'],
            'pengalaman_bulan' => ['required', 'integer', 'between:0,600'],
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'split' => ['nullable', 'string', 'max:120'],
            'sesi_per_minggu' => ['required', 'integer', 'between:1,7'],
            'schedule_days' => ['nullable', 'array'],
            'schedule_days.*' => [Rule::in(config('workout_templates.days'))],
            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'consent.accepted' => 'Kamu perlu menyetujui penyimpanan data program sebelum melanjutkan.',
            'schedule_days.*.in' => 'Pilihan hari tidak valid.',
        ];
    }
}
