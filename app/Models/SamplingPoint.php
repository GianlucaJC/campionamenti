<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SamplingPoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monitoring_section_id',
        'legacy_code',
        'title',
        'area_label',
        'sample_kind',
        'default_volume_liters',
        'requires_operational_status',
        'requires_product_lot',
        'sort_order',
        'is_active',
    ];

    /**
     * @return BelongsTo<MonitoringSection, SamplingPoint>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(MonitoringSection::class, 'monitoring_section_id');
    }

    /**
     * @return HasMany<MicrobiologicalCheckPoint>
     */
    public function checkPoints(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheckPoint::class);
    }
}
