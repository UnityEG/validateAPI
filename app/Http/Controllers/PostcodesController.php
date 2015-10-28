<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PostcodesController extends Controller
{
    /**
     * Show all Postcode records
     * @return array
     */
    public function index(){
        return (new \App\EssentialEntities\Transformers\PostcodeTransformer())->transformCollection(\App\Http\Models\Postcode::all()->toArray());
    }
}
