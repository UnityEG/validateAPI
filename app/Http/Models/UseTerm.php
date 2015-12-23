<?php

namespace App\Http\Models;

use UseTermTransformer;
use Illuminate\Database\Eloquent\Model;

class UseTerm extends Model {

    protected $table   = 'use_terms';
    
    protected $fillable = [
        'name',
        'list_order'
    ];


    /**
     * Relationship method between UseTerm Model and Voucher Model (many to many)
     * @return object
     */
    public function vouchersParmeters( ) {
        return $this->belongsToMany('App\Http\Models\VoucherParameter', 'voucher_parameters_use_terms_rel', 'use_term_id', 'voucher_parameter_id');
    }
    
    /**
     * Get Standard Json API collection format for muti use term objects
     * @return array
     */
    public static function getTransformedCollection( ) {
        $instance = new static;
        $response["data"] = [];
        foreach ( $instance->get() as $use_term_object) {
            $response['data'][] = $use_term_object->getBeforeTransform();
        }
        return $response;
    }
    
    /**
     * Get Standard Json API format for single object
     * @return array
     */
    public function getTransformedArray( ) {
        return UseTermTransformer::transform($this->prepareUseTermGreedyData());
    }
    
    /**
     * Get Before Standard Json API format to form a collection of Json objects
     * @return array
     */
    public function getBeforeTransform( ) {
        return UseTermTransformer::beforeStandard($this->prepareUseTermGreedyData());
    }
    
    /**
     * Create new Use Term
     * @param type $raw_data
     * @return boolean | array
     */
    public function createNewUseTerm( $raw_data) {
        \DB::beginTransaction();
        $created_use_term = $this->create($this->commonStoreUpdate($raw_data));
        if ( is_object( $created_use_term ) ) {
            \DB::commit();
            return $created_use_term->getTransformedArray();
        }
        \DB::rollBack();
        return FALSE;
    }
    
    /**
     * Update existing object
     * @param array $raw_data
     * @return boolean | array
     */
    public function updateUseTerm( array $raw_data) {
        \DB::beginTransaction();
        if ( $this->update( $this->commonStoreUpdate($raw_data)) ) {
            \DB::commit();
            return $this->getTransformedArray();
        }
        \DB::rollBack();
        return FALSE;
    }

    /**
     * Prepare UseTerm data with its relationships in a greedy way
     * @return array
     */
    private function prepareUseTermGreedyData() {
        $use_term_greedy_array = $this->toArray();
        return $use_term_greedy_array;
    }

    /**
     * Prepare Data for storing and updating process
     * @param array $raw_data
     * @return array
     */
    private function commonStoreUpdate( array $raw_data) {
        $modified_array = [];
        (!$name = array_deep_search( $raw_data, 'name')) ?  : $modified_array['name'] = (string)$name;
        (!$list_order = array_deep_search( $raw_data, 'list_order')) ?  : $modified_array['list_order'] = (int)$list_order;
        return $modified_array;
    }
}
