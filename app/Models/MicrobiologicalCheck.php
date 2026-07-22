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
        'facility_name',
        'sampled_on',
        'sampled_time',
        'incubation_started_on',
        'first_reading_on',
        'second_reading_on',
        'operator_name',
        'incubation_started_signature',
        'incubation_finished_signature',
        'cq_operator_name',
        'product_batch',
        'media_lot',
        'swab_lot',
        'membrane_lot',
        'bottle_sterilization_lot',
        'r2a_agar_lot',
        'r2a_agar_expires_on',
        'r2a_incubator_code',
        'r2a_incubation_started_on',
        'r2a_incubation_finished_on',
        'coliform_agar_lot',
        'coliform_agar_expires_on',
        'coliform_incubator_code',
        'coliform_incubation_started_on',
        'coliform_incubation_finished_on',
        'pseudomonas_cn_lot',
        'pseudomonas_cn_expires_on',
        'pseudomonas_incubator_code',
        'pseudomonas_incubation_started_on',
        'pseudomonas_incubation_finished_on',
        'slanetz_bartley_lot',
        'slanetz_bartley_expires_on',
        'enterococci_incubator_code',
        'enterococci_incubation_started_on',
        'enterococci_incubation_finished_on',
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
