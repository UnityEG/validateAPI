<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $table = "lu_nz_towns";
    
    /**
     * Relationship between Town Model and User Model (one to many)
     * @return object
     */
    public function users( ) {
        return $this->hasMany('App\User', 'town_id', 'id');
    }
}
