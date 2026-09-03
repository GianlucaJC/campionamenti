<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SamplingSession extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'environment', 'sub_environment', 'header', 'created_by_user_id'];

    protected $casts = ['header' => 'array'];
}