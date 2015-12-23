<?php

namespace App\Http\Controllers\VouchersControllers;

use App\Http\Controllers\ApiController;

class UseTermsController extends ApiController
{

    /**
     * Instance of UseTerm Model class
     * @var \App\Http\Models\UseTerm
     */
    private $UseTerm;

    public function __construct( \App\Http\Models\UseTerm $use_term) {
//        $this->middleware('jwt.auth', ['except'=>['index']]);
        $this->UseTerm = $use_term;
    }
    
    /**
     * List All records
     * @param \App\Http\Models\UseTerm $useterm_model
     * @return array
     */
    public function index( ) {
        return $this->UseTerm->getTransformedCollection();
    }
    
    /**
     * Show single record
     * @param integer $id
     * @return array
     */
    public function show( $id) {
        $use_term = $this->UseTerm->find((int)$id);
        return (is_object( $use_term )) ? $use_term->getTransformedArray() : $this->respondNotFound();
    }
    
    /**
     * Store new record
     * @param \App\Http\Requests\UseTermRequests\StoreUseTermRequest $request
     * @return array
     */
    public function store( \App\Http\Requests\Vouchers\UseTermRequests\StoreUseTermRequest $request) {
        $created_use_term_transformed = $this->UseTerm->createNewUseTerm($request->json('data'));
        return (is_array( $created_use_term_transformed )) ? $created_use_term_transformed : $this->respondInternalError('Error while creating new Term of Use');
    }
    
    /**
     * Update existing record
     * @param integer $id
     * @param \App\Http\Requests\UseTermRequests\UpdateUseTermRequest $request
     * @return array
     */
    public function update( $id, \App\Http\Requests\Vouchers\UseTermRequests\UpdateUseTermRequest $request) {
        $use_term = $this->UseTerm->find((int)$id);
        if ( !is_object( $use_term ) ) {
            return $this->respondNotFound();
        }//if ( !is_object( $use_term ) )
        $updated_transformed_use_term = $use_term->updateUseTerm($request->json('data'));
        return ($updated_transformed_use_term) ? $updated_transformed_use_term : $this->respondInternalError( 'Error while Updaing Term of Use');
    }
}
