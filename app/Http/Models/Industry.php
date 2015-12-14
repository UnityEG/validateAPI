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
    public function getBeforeStandard( ) {
        return \IndustryTransformer::beforeStandard($this->prepareIndustryGreedyData());
    }

    /**
     * Prepare Industry object data for transformation
     * @return array
     */
    private function prepareIndustryGreedyData() {
        $industry_greedy_data = $this->toArray();
        return $industry_greedy_data;
    }

}
