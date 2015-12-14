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
}
