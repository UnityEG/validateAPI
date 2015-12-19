<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = "lu_industries";
    
    protected $fillable = [
        "industry"
    ];
    
    /**
     * Relationship between Industry Model and Business Model (one to many)
     * @return object
     */
    public function business( ) {
        return $this->hasMany('App\Http\Models\Business', 'industry_id', 'id');
    }
    
    /**
     * Get Transformed Collection of Industry objects
     * @return array
     */
    public function getTransformedCollection( ) {
        $response["data"] = [];
        foreach ( (new static)->get() as $industry_object) {
            $response['data'][] = $industry_object->getBeforeStandard();
        }//foreach ( (new static)->get() as $industry_object)
        return $response;
    }
    
    /**
     * Get Before Standard Array
     * @return array
     */
    public function getTransformedArray( ) {
        return \IndustryTransformer::transform($this->prepareIndustryGreedyData());
    }
    
    /**
     * Get Before Standard Array
     * @return array
     */
    public function getBeforeStandard( ) {
        return \IndustryTransformer::beforeStandard($this->prepareIndustryGreedyData());
    }
    
    /**
     * Create New Business
     * @param array $raw_data
     * @return boolean | array
     */
    public function createNewIndustry(array $raw_data){
        $modified_data = $this->commonStoreUpdate($raw_data);
        \DB::beginTransaction();
        $created_industry = $this->create($modified_data);
        if ( is_object( $created_industry ) ) {
            \DB::commit();
            return $created_industry->getTransformedArray();
        }else{
            \DB::rollBack();
            return FALSE;
        }//if ( is_object( $created_business ) )
    }
    
    /**
     * Update existing Business
     * @param array $raw_data
     * @return boolean | array
     */
    public function updateIndustry(array $raw_data){
        $modified_data = $this->commonStoreUpdate($raw_data);
        \DB::beginTransaction();
        if ( $this->update( $modified_data) ) {
            \DB::commit();
            return $this->getTransformedArray();
        }else{
            \DB::rollBack();
            return FALSE;
        }//if($this->save( $modified_data))
    }

    /**
     * Prepare Industry object data for transformation
     * @return array
     */
    private function prepareIndustryGreedyData() {
        $industry_greedy_data = $this->toArray();
        return $industry_greedy_data;
    }

    /**
     * Prepare Data for storing and updating
     * @param array $raw_data
     * @return array 
     */
    private function commonStoreUpdate(array $raw_data){
        $modified_data = [];
        (!$industry = array_deep_search( $raw_data, 'industry')) ? : $modified_data['industry'] = (string)$industry;
        return $modified_data;
    }
}
