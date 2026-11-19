<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SamplingPoint extends Model
{
    use HasFactory;

    public const WATER_ANALYSES = [
        'appearance' => 'Aspetto',
        'ph' => 'pH',
        'r2a' => 'R2A',
        'coliforms' => 'Coliformi',
        'pseudomonas' => 'Pseudomonas',
        'enterococci' => 'Enterococchi',
    ];

    public const WATER_MICROBIOLOGY = [
        'r2a' => ['plate' => 'aerobic_plate_cfu', 'confirmed' => null, 'per_ml' => 'r2a_cfu_per_ml', 'date' => 'r2a_incubation_finished_on'],
        'coliforms' => ['plate' => 'coliform_plate_cfu', 'confirmed' => 'coliform_confirmed_cfu', 'per_ml' => 'coliform_cfu_per_ml', 'date' => 'coliform_incubation_finished_on'],
        'pseudomonas' => ['plate' => 'pseudomonas_plate_cfu', 'confirmed' => 'pseudomonas_confirmed_cfu', 'per_ml' => 'pseudomonas_cfu_per_ml', 'date' => 'pseudomonas_incubation_finished_on'],
        'enterococci' => ['plate' => 'enterococci_plate_cfu', 'confirmed' => 'enterococci_confirmed_cfu', 'per_ml' => 'enterococci_cfu_per_ml', 'date' => 'enterococci_incubation_finished_on'],
    ];

    public function waterAnalyses(): array
    {
        return $this->water_analyses ?? array_keys(self::WATER_ANALYSES);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monitoring_section_id',
        'monitoring_department_id',
        'legacy_code',
        'title',
        'area_label',
        'sample_kind',
        'default_volume_liters',
        'default_exposure_hours',
        'requires_operational_status',
        'requires_product_lot',
        'water_analyses',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'water_analyses' => 'array',
    ];

    /**
     * @return BelongsTo<MonitoringSection, SamplingPoint>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(MonitoringSection::class, 'monitoring_section_id');
    }

    /**
     * @return BelongsTo<MonitoringDepartment, SamplingPoint>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(MonitoringDepartment::class, 'monitoring_department_id');
    }

    /**
     * @return HasMany<MicrobiologicalCheckPoint>
     */
    public function checkPoints(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheckPoint::class);
    }
}
