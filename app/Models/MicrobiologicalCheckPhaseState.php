<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrobiologicalCheckPhaseState extends Model
{
    use HasFactory;

    protected $fillable = [
        'microbiological_check_id',
        'phase',
        'signed_by_user_id',
        'signed_at',
        'reopened_by_user_id',
        'reopened_at',
        'reopening_reason',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'reopened_at' => 'datetime',
    ];

    public function check(): BelongsTo
    {
        return $this->belongsTo(MicrobiologicalCheck::class, 'microbiological_check_id');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id');
    }
}