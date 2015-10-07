<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $table = "business_types";
    protected $fillable = ['type'];
    
    /**
     * Relationship between BusinessType Model and Business Model (many to many)
     * @return object
     */
    public function business( ) {
        return $this->belongsToMany('App\Http\Models\Business', 'business_business_types_rel', 'business_type_id', 'business_id');
    }
}
