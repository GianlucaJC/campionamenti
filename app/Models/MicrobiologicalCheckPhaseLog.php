<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrobiologicalCheckPhaseLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'microbiological_check_id',
        'phase',
        'action',
        'reason',
        'performed_by_user_id',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<MicrobiologicalCheck, MicrobiologicalCheckPhaseLog>
     */
    public function check(): BelongsTo
    {
        return $this->belongsTo(MicrobiologicalCheck::class, 'microbiological_check_id');
    }

    /**
     * @return BelongsTo<User, MicrobiologicalCheckPhaseLog>
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }
}