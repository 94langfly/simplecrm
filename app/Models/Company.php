<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $dates = ['created_at', 'updated_at'];
    protected $table = "company";
    
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
    
    public function employee()
    {
        return $this->hasMany(Employee::class, 'company_id', 'id');
    }
}
