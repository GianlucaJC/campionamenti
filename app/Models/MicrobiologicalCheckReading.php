<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrobiologicalCheckReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'microbiological_check_point_id',
        'reading_number',
        'cfu_count',
        'growth_result',
    ];

    public function pointResult(): BelongsTo
    {
        return $this->belongsTo(MicrobiologicalCheckPoint::class, 'microbiological_check_point_id');
    }
}