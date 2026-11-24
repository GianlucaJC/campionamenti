<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SamplingOperator extends Model
{
    public const UNIFORMS = [
        'Divisa bianca',
        'Tuta bianca',
        'Tuta blu',
        'Camice',
    ];

    protected $fillable = ['name'];
}
