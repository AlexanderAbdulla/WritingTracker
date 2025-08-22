<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'wordcount',
        'minutes_spent',
        'time_finished',
        'user_id',
    ];
}