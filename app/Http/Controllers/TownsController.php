<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class TownsController extends Controller
{
    /**
     * Show all Town records
     * @return array
     */
    public function index(){
        return (new \App\EssentialEntities\Transformers\TownTransformer())->transformCollection(\App\Http\Models\Town::all()->toArray());
    }
}
