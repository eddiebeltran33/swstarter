<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryStat extends Model
{
    /** @use HasFactory<\Database\Factories\QueryStatFactory> */
    use HasFactory;

    protected $guarded = ['id'];
    const UPDATED_AT = null;
}
