<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = "lu_industries";
    
    /**
     * Relationship between Industry Model and Business Model (one to many)
     * @return object
     */
    public function business( ) {
        return $this->hasMany('App\Http\Models\Business', 'industry_id', 'id');
    }
}
