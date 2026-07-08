<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringSection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    /**
     * @return HasMany<SamplingPoint>
     */
    public function samplingPoints(): HasMany
    {
        return $this->hasMany(SamplingPoint::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<MicrobiologicalCheck>
     */
    public function checks(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheck::class);
    }
}
