<?php

namespace App\Models;

use Database\Factories\WeeklyCheckinFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'program_id',
    'minggu_ke',
    'berat_kg',
    'pinggang_cm',
    'energi',
    'catatan',
])]
class WeeklyCheckin extends Model
{
    /** @use HasFactory<WeeklyCheckinFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minggu_ke' => 'integer',
            'berat_kg' => 'decimal:2',
            'pinggang_cm' => 'decimal:2',
            'energi' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
