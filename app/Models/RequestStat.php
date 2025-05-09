<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestStat extends Model
{
    /** @use HasFactory<\Database\Factories\RequestStatFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    const UPDATED_AT = null;
}
