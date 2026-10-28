<?php

namespace App\Models;

use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'level',
    'goal',
    'usia',
    'berat_kg',
    'tinggi_cm',
    'pengalaman_bulan',
    'gender',
    'split',
    'kalori',
    'protein_g',
    'lemak_g',
    'karbo_g',
    'air_ml',
    'sesi_per_minggu',
    'schedule_days',
    'mulai_pada',
    'aktif',
    'consent_at',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'usia' => 'integer',
            'berat_kg' => 'decimal:2',
            'tinggi_cm' => 'decimal:2',
            'pengalaman_bulan' => 'integer',
            'kalori' => 'integer',
            'protein_g' => 'integer',
            'lemak_g' => 'integer',
            'karbo_g' => 'integer',
            'air_ml' => 'integer',
            'sesi_per_minggu' => 'integer',
            'schedule_days' => 'array',
            'mulai_pada' => 'date',
            'aktif' => 'boolean',
            'consent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function planSessions(): HasMany
    {
        return $this->hasMany(PlanSession::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(WeeklyCheckin::class);
    }

    public function isDeloadWeek(int $week): bool
    {
        return $week === 4;
    }

    public function currentWeek(): int
    {
        $start = $this->mulai_pada->copy()->startOfWeek();
        $now = now()->startOfWeek();

        if ($start->greaterThan($now)) {
            return 1;
        }

        return max(1, min(4, (int) $start->diffInWeeks($now) + 1));
    }
}
