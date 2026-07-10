<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringDepartment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monitoring_section_id',
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    /**
     * @return BelongsTo<MonitoringSection, MonitoringDepartment>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(MonitoringSection::class, 'monitoring_section_id');
    }

    /**
     * @return HasMany<SamplingPoint>
     */
    public function samplingPoints(): HasMany
    {
        return $this->hasMany(SamplingPoint::class, 'monitoring_department_id');
    }
}
