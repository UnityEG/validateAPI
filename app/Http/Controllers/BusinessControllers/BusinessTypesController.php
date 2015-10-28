<?php

namespace App\Http\Controllers\BusinessControllers;

use App\EssentialEntities\Transformers\BusinessTypesTransformer;
use App\Http\Controllers\Controller;
use App\Http\Models\BusinessType;

class BusinessTypesController extends Controller
{
    /**
     * Show all BusinessType records
     * @return array
     */
    public function index( ) {
        return (new BusinessTypesTransformer())->transformCollection(BusinessType::all()->toArray());
    }
}
