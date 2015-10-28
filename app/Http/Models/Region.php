<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = "lu_nz_regions";
    
    /**
     * Relationship between Region Model and User Model (one to many)
     * @return object
     */
    public function users( ) {
        return $this->hasMany('App\User', 'region_id', 'id');
    }
    
    /**
     * Relationship between Region Model and Business Model (one to many)
     * @return object
     */
    public function business( ) {
        return $this->hasMany('App\Http\Models\Business', 'region_id', 'id');
    }
}