<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'address',
        'salary',
        'company_id',
        'user_id'
    ];
}
