<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $table = "lu_nz_postcodes";
    
    /**
     * Relationship between Postcode Model and User Model (one to many)
     * @return object
     */
    public function users( ) {
        return $this->hasMany('App\User', 'postcode_id', 'id');
    }
    
    /**
     * Relationship between Postcode Model and Business Model (one to many)
     * @return object
     */
    public function business( ) {
        return $this->hasMany('App\Http\Models\Business', 'postcode_id', 'id');
    }
}
