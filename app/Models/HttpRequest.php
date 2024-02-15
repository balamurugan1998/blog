<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HttpRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    // protected $table = "requests";
    
    protected $fillable = [
        'id',
        'user_id',
        'url', 
        'method',
        'ip',
    ];
}
