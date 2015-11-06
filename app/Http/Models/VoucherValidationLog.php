<?php

namespace App\Http\Models;

use UserTransformer;
use VoucherValidationLogTransformer;
use BusinessTransformer;
use Illuminate\Database\Eloquent\Model;

class VoucherValidationLog extends Model {

    /**
     * table name in the database
     * @var string
     */
    protected $table = 'voucher_validation_log';
    
    /**
     * Fillable columns in voucher_validation_log table
     * @var array
     */
    protected $fillable = [
        'voucher_id',
        'business_id',
        'user_id',
        'date',
        'value',
        'balance',
        'log'
    ];

    /**
     * Relationship between VoucherValidationLog Model and Voucher Model (many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voucher() {
        return $this->belongsTo( 'App\Http\Models\Voucher', 'voucher_id', 'id' );
    }

    /**
     * Relationship between VoucherValidationLog Model and Business Model (many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business() {
        return $this->belongsTo( 'App\Http\Models\Business', 'business_id', 'id' );
    }

    /**
     * Relationship between VoucherValidationLog Model and User Model (many to one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo( 'App\User', 'user_id', 'id' );
    }
    
    /**
     * Get array to build Standard Json response for single object
     * @return array
     */
    public function getStandardJsonFormat(){
        return VoucherValidationLogTransformer::transform($this->prepareVoucherValidationLogGreedyData());
    }
    
    /**
     * Get array to be used in building standard Json response for multi objects
     * @return array
     */
    public function getBeforeStandardArray(){
        return VoucherValidationLogTransformer::beforeStandard($this->prepareVoucherValidationLogGreedyData());
    }

    /**
     * Prepare data to be used in creating Standard Json response
     * @return array
     */
    private function prepareVoucherValidationLogGreedyData() {
        $voucher_validation_log_greedy_array = $this->load(['voucher', 'user', 'business'])->toArray();
        (empty($voucher_validation_log_greedy_array['voucher'])) ?  : $voucher_validation_log_greedy_array['voucher'] = \VoucherTransformer::transform($voucher_validation_log_greedy_array['voucher']);
        (empty($voucher_validation_log_greedy_array['user'])) ?  : $voucher_validation_log_greedy_array['user'] = UserTransformer::transform($voucher_validation_log_greedy_array['user']);
        (empty($voucher_validation_log_greedy_array['business'])) ?  : $voucher_validation_log_greedy_array['business'] = BusinessTransformer::transform($voucher_validation_log_greedy_array['business']);
        return $voucher_validation_log_greedy_array;
    }
    
//    todo create getStandardJsonCollection method instead of getBeforeStandardArray to get Standard Json collection of objects
}
