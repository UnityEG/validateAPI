<?php

namespace App\Http\Controllers;

use App\EssentialEntities\Transformers\RegionTransformer;
use App\Http\Controllers\Controller;
use App\Http\Models\Region;

class RegionsController extends Controller
{
    /**
     * Show all Region records
     * @return array
     */
    public function index(){
        return (new RegionTransformer())->transformCollection( Region::all()->toArray());
    }
}
