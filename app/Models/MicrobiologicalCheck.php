<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicrobiologicalCheck extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monitoring_section_id',
        'sampled_on',
        'incubation_started_on',
        'first_reading_on',
        'second_reading_on',
        'operator_name',
        'cq_operator_name',
        'media_lot',
        'swab_lot',
        'notes',
        'created_by_user_id',
    ];

    /**
     * @return BelongsTo<MonitoringSection, MicrobiologicalCheck>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(MonitoringSection::class, 'monitoring_section_id');
    }

    /**
     * @return BelongsTo<User, MicrobiologicalCheck>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<MicrobiologicalCheckPoint>
     */
    public function pointResults(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheckPoint::class);
    }
}
