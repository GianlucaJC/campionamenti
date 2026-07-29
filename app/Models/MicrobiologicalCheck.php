<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicrobiologicalCheck extends Model
{
    use HasFactory, SoftDeletes;

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
        'sampling_completed_signature',
        'first_reading_completed_signature',
        'second_reading_completed_signature',
        'sampling_completed_by_user_id',
        'first_reading_completed_by_user_id',
        'second_reading_completed_by_user_id',
        'sampling_reopened_by_user_id',
        'sampling_reopened_at',
        'sampling_reopening_reason',
        'first_reading_reopened_by_user_id',
        'first_reading_reopened_at',
        'first_reading_reopening_reason',
        'second_reading_reopened_by_user_id',
        'second_reading_reopened_at',
        'second_reading_reopening_reason',
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
        'deleted_by_user_id',
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
     * @return BelongsTo<User, MicrobiologicalCheck>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }

    /**
     * @return HasMany<MicrobiologicalCheckPoint>
     */
    public function pointResults(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheckPoint::class);
    }

    /**
     * @return HasMany<MicrobiologicalCheckPhaseLog>
     */
    public function phaseLogs(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheckPhaseLog::class)
            ->orderByDesc('logged_at');
    }

    public function phaseStates(): HasMany
    {
        return $this->hasMany(MicrobiologicalCheckPhaseState::class);
    }
}
