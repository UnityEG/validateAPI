<?php

namespace App\Http\Controllers\VouchersControllers;

use App\Http\Controllers\ApiController;

class UseTermsController extends ApiController
{
    public function index(\App\Http\Models\UseTerm $useterm_model ) {
        return $useterm_model->getStandardJsonCollection();
    }
}
