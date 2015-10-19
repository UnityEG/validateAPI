<?php

namespace App\Http\Models;

use App\EssentialEntities\Transformers\UseTermTransformer;
use Illuminate\Database\Eloquent\Model;

class UseTerm extends Model {

    protected $table   = 'use_terms';
    
    /**
     * Relationship method between UseTerm Model and Voucher Model (many to many)
     * @return object
     */
    public function vouchersParmeters( ) {
        return $this->belongsToMany('App\Http\Models\VoucherParameter', 'voucher_parameters_use_terms_rel', 'use_term_id', 'voucher_parameter_id');
    }
    
    /**
     * Get Standard Json API format for single object
     * @return array
     */
    public function getStandardJsonFormat( ) {
        return (new UseTermTransformer())->transform($this->prepareUseTermGreedyData());
    }
    
    /**
     * Get Before Standard Json API format to form a collection of Json objects
     * @return array
     */
    public function getBeforeStandardArray( ) {
        return (new UseTermTransformer())->beforeStandard($this->prepareUseTermGreedyData());
    }

    /**
     * Prepare UseTerm data with its relationships in a greedy way
     * @return array
     */
    private function prepareUseTermGreedyData() {
        $use_term_greedy_array = $this->toArray();
        return $use_term_greedy_array;
    }

}
