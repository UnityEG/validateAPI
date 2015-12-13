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
    
    /**
     * Get Transformed Collection of Region objects
     * @return array
     */
    public function getTransformedCollection(){
        $instance = new static;
        $response["data"] = [];
        foreach ( $instance->get() as $region_object) {
            $response["data"][] = $region_object->getBeforeStandard();
        }//foreach ( $instance->get() as $region_object)
        return $response;
    }
    
    /**
     * Get Before Standard Array for single Region object
     * @return array
     */
    public function getBeforeStandard(){
        return \RegionTransformer::beforeStandard($this->prepareRegionGreedyData());
    }
    
    /**
     * Get HTML response for Region objects
     * @return string
     */
    public function getHtmlCollection(){
        $instance = new static;
        $response = '<select>';
        foreach ( $instance->get() as $region_object) {
            $response .= "<option value={$region_object->id}>{$region_object->region}</option>";
        }//foreach ( $instance->get() as $region_object)
        $response .= '</select>';
        return $response;
    }
    
    /**
     * Prepare Greedy data for Region object and its relationships
     * @return array
     */
    private function prepareRegionGreedyData(){
        $region_greedy_array = $this->toArray();
        return $region_greedy_array;
    }
}
