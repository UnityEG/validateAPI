<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Models\Region;

class RegionsController extends Controller
{
    /**
     * Instance of Region Model class
     * @var Region
     */
    private $RegionModel;

    public function __construct(Region $region_model){
        $this->RegionModel = $region_model;
    }
    
    /**
     * Show all Region records
     * @return array
     */
    public function index(){
        return $this->RegionModel->getTransformedCollection();
    }
    
    public function renderHtmlCollection(){
        return $this->RegionModel->getHtmlCollection();
    }
}
