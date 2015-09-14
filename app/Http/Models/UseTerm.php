<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UseTerm extends Model {

    protected $table   = 'use_terms';
    protected $guarded = array( 'id' );
    
    /**
     * Relationship method between UseTerm Model and Voucher Model (many to many)
     * @return object
     */
    public function vouchers( ) {
        return $this->belongsToMany('App\Http\Models\VoucherParameter', 'voucher_parameters_use_terms_rel', 'use_term_id', 'voucher_parameter_id');
    }

}
