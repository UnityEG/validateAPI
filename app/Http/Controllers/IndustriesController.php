<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\ApiController;

class IndustriesController extends ApiController
{

    /**
     * Instance of Industry Model
     * @var \App\Http\Models\Industry
     */
    private $IndustryModel;

    public function __construct(\App\Http\Models\Industry $industry_model) {
        $this->IndustryModel = $industry_model;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->IndustryModel->getTransformedCollection();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreBusinessRequest  $request
     * @return mix
     */
    public function store( \App\Http\Requests\Industries\StoreIndustryRequest $request ) {
        $stored_industry = $this->IndustryModel->createNewIndustry($request->json("data"));
        return (is_array( $stored_industry ) && array_key_exists( "data", $stored_industry)) ? $stored_industry : $this->respondWithError( "Faild Creating new Industry");
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateBusinessRequest  $request
     * @param  int  $id
     * @return array
     */
    public function update( \App\Http\Requests\Industries\UpdateIndustryRequest $request, $id ) {
        $industry_object = $this->IndustryModel->find((int)$id);
        if ( !is_object( $industry_object ) ) {
            return $this->respondNotFound();
        }//if ( !is_object( $business_object ) )
        return ($updated_industry = $industry_object->updateIndustry($request->json("data"))) ? $updated_industry : $this->respondInternalError( 'Error while updating Business');
    }
}
