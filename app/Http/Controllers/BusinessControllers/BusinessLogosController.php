<?php

namespace App\Http\Controllers\BusinessControllers;

use Illuminate\Http\Request;
use App\Http\Requests\Business\StoreBusinessLogoRequest;
use App\Http\Controllers\ApiController;
use App\Http\Models\BusinessLogo;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;

class BusinessLogosController extends ApiController
{
//    todo update documentations of (@var, @param and @return)
//    todo refine class by removing unused methods
//    todo apply lazy instantiation principle by applying method dependency injection
    /**
     * Default path of business logos
     * @var string
     */
    private $DefaultBusinessLogosPath;
    
    /**
     * Instance of BusinessLogo Model
     * @var object
     */
    private $BusinessLogoModel;


    public function __construct(  
            BusinessLogo $business_logo_model
    ) {
//        todo apply jwt.auth middleware to authenticate users
//        todo apply jwt.refresh middleware to refresh token every request
        $this->DefaultBusinessLogosPath = config('validateconf.default_business_logos_path');
        $this->BusinessLogoModel = $business_logo_model;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = ["data"];
        foreach ( $this->BusinessLogoModel->all() as $business_logo_object) {
            $response['data'][] = $business_logo_object->getBeforeStandardArray();
        }
        return $response;
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
        $image_path = $this->DefaultBusinessLogosPath.$image_name.'.png';
        $resized_image = Image::make($request->file('business_logo'))->resize(310, 195);
        if ( $resized_image->save($image_path) ) {
//            todo add business_id to the array to save in the business_logos table
            $store_data = [
                "name"=>$image_name, 
                "user_id"=>JWTAuth::parseToken()->authenticate()->id, 
                "business_id"=>'1'
            ];
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
        return $this->BusinessLogoModel->findOrFail((int)$id)->getStandardJsonFormat();
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
    public function destroy(  \App\Http\Requests\Business\BusinessLogos\DestroyBusinessLogoRequest $request, $id)
    {
        $business_logo_object = $this->BusinessLogoModel->findOrFail((int)$id);
//        todo check if business logo is the current active logo of the business and if so change the active logo to the next business logo of this business in the business table
        $file_path = config('validateconf.default_business_logos_path').$business_logo_object->name.'.png';
        if ( !file_exists( $file_path ) ) {
            $business_logo_object->delete();
            return $this->respond("success");
        }//if ( !file_exists( $file_path ) )
        if ( unlink($file_path) ) {
            $business_logo_object->delete();
            return $this->respond("success");
        }
        return $this->respondInternalError();
    }
    
//    Helpers
    
    /**
     * Generate unique name for every image
     * @return integer
     */
    private function generateImageName( ) {
        $filename = mt_rand(10000001, 99999999);
        (!$this->BusinessLogoModel->where('name', $filename)->exists()) ?  : $this->generateImageName();
        return (8 > strlen( $filename)) ? $this->generateImageName() : $filename;
    }
}
