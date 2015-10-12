<?php

namespace App\Http\Models;

use App\EssentialEntities\Transformers\BusinessLogoTransformer;
use App\EssentialEntities\Transformers\BusinessTransformer;
use App\EssentialEntities\Transformers\UserTransformer;
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
    
    /**
     * Relationship between BusinessLogo Model and User Model (many to one)
     * @return object
     */
    public function user( ) {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
//    Helpers
    
    /**
     * Get standard Json API format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        return (new BusinessLogoTransformer())->transform($this->prepareBusinessLogoGreedyData());
    }
    
    /**
     * Get before standard Json API format for using in building array of Json objects
     * @return array
     */
    public function getBeforeStandardArray( ) {
        return (new BusinessLogoTransformer())->beforeStandard( $this->prepareBusinessLogoGreedyData());
    }
    
    /**
     * Prepare Business Logo Data with all its relationships data in greedy way with standard format
     * @return array
     */
    private function prepareBusinessLogoGreedyData( ) {
        $business_logo_array = $this->load('business', 'user')->toArray();
        (empty($business_logo_array['business'])) ?  : $business_logo_array['business'] = (new BusinessTransformer())->transform( $business_logo_array['business']);
        (empty($business_logo_array['user'])) ?  : $business_logo_array['user'] = (new UserTransformer())->transform( $business_logo_array['user']);
        return $business_logo_array;
    }
}
