<?php

namespace App\Http\Controllers;

use App\Http\Models\VoucherImage;
use App\Http\Requests\Vouchers\VoucherImages\StoreVoucherImageRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;

class VoucherImagesController extends ApiController
{
    /**
     * Default path to voucher images
     * @var string
     */
    public $defaultVoucherImagesPath = 'voucher/images/default';


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage and save gift voucher image in default path.
     *
     * @param  StoreVoucherImageRequest  $request
     * @return Response
     */
    public function storeGiftImage( StoreVoucherImageRequest $request)
    {
        $gift_voucher_image_object = $request->file('voucher_image');
        $name = $this->generateImageName();
        $resized_image = Image::make($gift_voucher_image_object)->resize(310, 195);
        $path = public_path( $this->defaultVoucherImagesPath. '/' . $name . '.png' );
        if ( $resized_image->save( $path) ) {
            if($stored_voucher_image_object = VoucherImage::create(['name'=>$name])){
                return $this->respond(["data"=>["name"=>$stored_voucher_image_object->name]]);
            }//if(VoucherImage::create(['name'=>$name]))
        }//if ( $resized_image->save( $path) )
        else{
            return $this->respondWithError("Internal Error");
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
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
