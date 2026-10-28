<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckinRequest extends FormRequest
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
            'minggu_ke' => ['required', 'integer', 'between:1,4'],
            'berat_kg' => ['required', 'numeric', 'between:30,200'],
            'pinggang_cm' => ['nullable', 'numeric', 'between:40,200'],
            'energi' => ['nullable', 'integer', 'between:1,5'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'berat_kg.between' => 'Berat badan harus di antara 30 dan 200 kg.',
            'pinggang_cm.between' => 'Lingkar pinggang harus di antara 40 dan 200 cm.',
            'energi.between' => 'Level energi harus di antara 1 dan 5.',
        ];
    }
}
