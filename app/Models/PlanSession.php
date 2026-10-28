<?php

namespace App\Models;

use Database\Factories\PlanSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'program_id',
    'minggu_ke',
    'hari',
    'tanggal',
    'fokus',
    'tipe',
    'template_key',
    'detail',
    'completed_at',
])]
class PlanSession extends Model
{
    /** @use HasFactory<PlanSessionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minggu_ke' => 'integer',
            'tanggal' => 'date',
            'detail' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function isRest(): bool
    {
        return $this->tipe === 'istirahat';
    }

    public function isCardio(): bool
    {
        return $this->tipe === 'kardio';
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
