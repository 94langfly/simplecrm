<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['created_at', 'updated_at'];
    protected $table = "roles";
    
    protected $fillable = [
        'name',
    ];
}
