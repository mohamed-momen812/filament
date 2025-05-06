<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'country_id',
        'city_id',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}