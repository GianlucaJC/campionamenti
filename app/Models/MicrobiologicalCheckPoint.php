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
        'exposure_ended_at',
        'is_operational',
        'product_lot',
        'cfu_count',
        'aerobic_plate_cfu',
        'aerobic_cfu_per_ml',
        'first_cfu_count',
        'second_cfu_count',
        'first_growth_result',
        'second_growth_result',
        'coliform_result',
        'coliform_plate_cfu',
        'coliform_confirmed_cfu',
        'coliform_cfu_per_100ml',
        'pseudomonas_result',
        'pseudomonas_plate_cfu',
        'pseudomonas_confirmed_cfu',
        'pseudomonas_cfu_per_100ml',
        'enterococci_result',
        'enterococci_plate_cfu',
        'enterococci_confirmed_cfu',
        'enterococci_cfu_per_100ml',
        'ph_value',
        'appearance_result',
        'final_result',
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
