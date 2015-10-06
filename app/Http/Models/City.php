<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "lu_nz_cities";
    
    /**
     * Relationship between City Model and User Model (one to many)
     * @return object
     */
    public function users( ) {
        return $this->hasMany('App\Http\Models\User', 'city_id', 'id');
    }
}
