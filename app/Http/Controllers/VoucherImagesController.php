<?php

namespace App\Http\Controllers;

use App\Http\Models\VoucherImage;
use App\Http\Requests\Vouchers\VoucherImages\StoreVoucherImageRequest;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use \Symfony\Component\HttpFoundation\File\UploadedFile;
use App\EssentialEntities\Transformers\VoucherImageTransformer;

class VoucherImagesController extends ApiController
{
    
    /**
     * Default path to voucher images
     * @var string
     */
    public $defaultVoucherImagesPath;
    
    /**
     * VoucherImageTransformer instance
     * @var object
     */
    private $voucherImageTransformer;
    
    public function __construct( 
            VoucherImageTransformer $voucher_image_transformer 
            ) {
//        todo apply JWTAuth middleware on all methods in this controller
        $this->voucherImageTransformer = $voucher_image_transformer;
        $this->defaultVoucherImagesPath = config('validateconf.default_voucher_images_path');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $all_image_objects = VoucherImage::all()->toArray();
        return $this->respond($this->voucherImageTransformer->transformCollection( $all_image_objects));
    }

    /**
     * Store a newly created resource in storage and save gift voucher image in default path.
     *
     * @param  \App\Http\Requests\Vouchers\VoucherImages\StoreVoucherImageRequest  $request
     * @return Response
     */
    public function storeGiftImage( StoreVoucherImageRequest $request)
    {
        return $this->storeImage($request->file('voucher_image'), 'gift');
    }
    
    /**
     * store default Deal voucher image
     * @param StoreVoucherImageRequest $request
     * @return Json response
     */
    public function storeDealImage( StoreVoucherImageRequest $request) {
        return $this->storeImage($request->file('voucher_image'), 'deal');
    }

    /**
     * Display all Gift voucher images.
     *
     * @return array
     */
    public function showGiftImages()
    {
        $gift_image_objects = VoucherImage::where('type', 'gift')->get()->toArray();
        return $this->respond($this->voucherImageTransformer->transformCollection( $gift_image_objects));
    }
    
    /**
     * Display all Deal voucher images
     * @return array
     */
    public function showDealImages( ) {
        $deal_image_objects = VoucherImage::where('type', 'deal')->get()->toArray();
        return $this->respond($this->voucherImageTransformer->transformCollection( $deal_image_objects));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Json
     */
    public function destroy($id)
    {
        try{
            $voucher_image_object = VoucherImage::findOrFail($id);
            $path = public_path( $this->defaultVoucherImagesPath).'/'.$voucher_image_object->name.'.png';
            if(unlink($path)){
                if($voucher_image_object->delete()){
                    return $this->respond(["data"=>["message"=>"Image has been deleted successfully"]]);
                }//if($voucher_image_object->delete())
                else{
                    return $this->respondWithError("Internal Error");
                }
            }//if(unlink($path))
            else{
                return $this->respondWithError("Internal Error");
            }
        } catch (\Exception $ex) {
            return $this->respondNotFound($ex->getMessage());
        }
    }
    
//    Helpers
    
    /**
     * General storing image for all types
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file_object
     * @param string $type
     * @return Json response
     */
    private function storeImage(UploadedFile $file_object, $type) {
        $name = $this->generateImageName();
        $resized_image = Image::make($file_object)->resize(310, 195);
        $path = public_path( $this->defaultVoucherImagesPath. '/' . $name . '.png' );
        if ( $resized_image->save( $path) ) {
            if($stored_voucher_image_object = VoucherImage::create(['name'=>$name, 'type'=>$type])){
                return $this->respond($this->voucherImageTransformer->transform( $stored_voucher_image_object->toArray()));
            }//if(VoucherImage::create(['name'=>$name]))
        }//if ( $resized_image->save( $path) )
        else{
            return $this->respondWithError("Internal Error");
        }
    }
    
    /**
     * Generate Image Name as 8 random digits
     * @return string
     */
    private function generateImageName() {
        $filename = mt_rand( 10000001, 99999999 ); // better than rand()
        // call the same function if the $filename exists already
        if ( VoucherImage::where( 'name', $filename )->exists() ) {
            return $this->generateImageName();
        }
        if ( strlen( $filename ) != 8 ) {
            return $this->generateImageName();
        }
        return $filename;
    }
}
