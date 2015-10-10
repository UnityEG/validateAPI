<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLogo extends Model
{
    protected $table = 'business_logos';
    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'extension'
    ];
    
    /**
     * Relationship between BusinessLogo Model and Business Model (many to one)
     * @return object
     */
    public function business( ) {
        return $this->belongsTo( 'App\Http\Models\Business', 'business_id', 'id' );
    }
    
//    todo create Relationship method with User Model (many to one)
    
//    todo create getStandardJsonFormat method
//    todo create getBeforeStandardArray method
//    todo create prepareBusinessLogoGreedyData
}
