<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'department_id',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'zip_code',
        'date_of_birth',
        'date_of_hireds',
        'first_name',
        'last_name',
        'middle_name',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function team() // Assuming you have a Team model for multi-tenancy
    {
        return $this->belongsTo(Team::class);
    }
}
