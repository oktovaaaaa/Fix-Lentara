<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumateraHistory extends Model
{
    protected $fillable = [
        'tribe',
        'period',
        'title',
        'body',
        'more_link',
        'sort_order',
    ];
}
