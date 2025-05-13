<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'team_id'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function team() // Assuming you have a Team model for multi-tenancy
    {
        return $this->belongsTo(Team::class);
    }
}
