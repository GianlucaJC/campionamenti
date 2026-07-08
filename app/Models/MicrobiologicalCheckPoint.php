<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrobiologicalCheckPoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'microbiological_check_id',
        'sampling_point_id',
        'sampled_at',
        'is_operational',
        'product_lot',
        'cfu_count',
        'notes',
    ];

    /**
     * @return BelongsTo<MicrobiologicalCheck, MicrobiologicalCheckPoint>
     */
    public function check(): BelongsTo
    {
        return $this->belongsTo(MicrobiologicalCheck::class, 'microbiological_check_id');
    }

    /**
     * @return BelongsTo<SamplingPoint, MicrobiologicalCheckPoint>
     */
    public function point(): BelongsTo
    {
        return $this->belongsTo(SamplingPoint::class, 'sampling_point_id');
    }
}
