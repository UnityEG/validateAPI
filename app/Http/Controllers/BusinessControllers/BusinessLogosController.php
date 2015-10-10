<?php

namespace App\Http\Controllers\BusinessControllers;

use Illuminate\Http\Request;
use App\Http\Requests\Business\StoreBusinessLogoRequest;
use App\Http\Controllers\ApiController;
use App\Http\Models\BusinessLogo;
use Intervention\Image\Facades\Image;

class BusinessLogosController extends ApiController
{
    /**
     * Default path of business logos
     * @var string
     */
    public $DefaultBusinessLogosPath;
    
    /**
     * Instance of BusinessLogo Model
     * @var object
     */
    private $BusinessLogoModel;
    
    /**
     * Instance of Intervention Imgae Facade
     * @var object
     */
    private $ImageFacade;


    public function __construct(  
            BusinessLogo $business_logo_model,
            Image $image_facade
    ) {
        $this->DefaultBusinessLogosPath = public_path('images/business/logos/');
        $this->BusinessLogoModel = $business_logo_model;
        $this->ImageFacade = $image_facade;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBusinessLogoRequest $request)
    {
        $image_name = $this->generateImageName();
        $imgae_path = $this->DefaultBusinessLogosPath.$image_name.'.png';
        $resized_image = Image::make($request->file('business_logo'))->resize(310, 195);
        if ( $resized_image->save($imgae_path) ) {
//            todo add business_id and current authenticated user_id to the array to save them in the business_logos table
            $store_data = ["name"=>$image_name, "user_id"=>'2', "business_id"=>'1'];
            $created_business_object = $this->BusinessLogoModel->create($store_data);
            return (is_object( $created_business_object )) ? $created_business_object->getStandardJsonFormat() : $this->respondInternalError();
        }
        return $this->respondInternalError();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //todo delete business logo image
    }
    
//    Helpers
    private function generateImageName( ) {
        $filename = mt_rand(10000001, 99999999);
        (!$this->BusinessLogoModel->where('name', $filename)->exists()) ?  : $this->generateImageName();
        return (8 > strlen( $filename)) ? $this->generateImageName() : $filename;
    }
}
