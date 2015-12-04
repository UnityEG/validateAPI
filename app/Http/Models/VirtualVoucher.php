<?php

namespace App\Http\Models;

use \Image;

class VirtualVoucher
{
    /**
     * Default Virtual Vouchers directory path
     * @var string
     */
    public $VirtualVouchersPath;
    
    public function __construct(  ) {
        $this->VirtualVouchersPath = config('validateconf.default_virtual_vouchers_images_path');
    }
    
    /**
     * Find voucher path
     * @param integer $code
     * @return string
     */
    public function find( $code) {
        $path = $this->VirtualVouchersPath.md5($code).".png";
        if(!file_exists( $path)){
            $voucher_object = Voucher::where('code', (int)$code)->first();
            return (is_object( $voucher_object )) ? $this->create( $voucher_object) : FALSE;
        }
        return $path;
    }
    
    /**
     * Get prepared data for email to be sent
     * @param Voucher $purchased_voucher_object
     * @return array
     */
    public function create(Voucher $purchased_voucher_object) {
        return $this->generateVirtualVoucher($purchased_voucher_object->getVirtualVoucherData());
    }
    
    /**
     * Create Virtaul Voucher Image accordin to the data in the array and return with the voucher image path
     * @param array $data
     * @return string
     */
    private function generateVirtualVoucher( array $data ) {
//        todo fix missing g class in voucher method inside GeneralHelperTools class
        // Get variable names and values from data array
        extract( $data );
        // Set fonts 
        $fontArial         = 'voucher/fonts/arial.ttf';
        $fontArialB        = 'voucher/fonts/arialbd.ttf';
        // =============================================================================
        // Voucher image template 
        $template_filename = 'voucher/images/voucher_bg.png';
        $img               = Image::make( $template_filename )->resize( 1000, 500, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize(); // otherwise images smaller than 200x200 will be stretched.
        } );
        // =============================================================================
        // Merchant logo 
        $logo = Image::make( $data['m_logo_filename'] )->resize( 200, 200, function ($constraint) {
            $constraint->aspectRatio();
            //$constraint->upsize(); // otherwise images smaller than 200x200 will be stretched.
        } );
        //
        // Fill up the blank spaces with transparent color
        $logo->resizeCanvas( 200, 200, 'center', false, array( 255, 255, 255, .7 ) );
        //
        // insert logo into Voucher image
        $img->insert( $logo, 'top-left', 40, 40 );
        //
        // =============================================================================
        // Voucher QRcode 
        $QRcode = Image::make( $this->qrcode( $data['qr_code'] ) )->resize( 200, 200, function ($constraint) {
            $constraint->aspectRatio();
        } );
        // Fill up the blank spaces with transparent color
        $QRcode->resizeCanvas( 200, 200, 'center', false, array( 255, 255, 255, .7 ) );
        // insert QRcode into Voucher image
        $img->insert( $QRcode, 'top-left', 40, 260 );
        // =============================================================================
        // Merchant name
        $textData      = array(
            'text' => $data['merchant_business_name'],
            'size' => 50,
            'font' => $fontArialB,
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 20, 710, 80 );
        // =============================================================================
        // Merchant info
        $merchant_info = $data['merchant_business_address1'] . '  ' . $data['merchant_business_phone'];
        $textData      = array(
            'text' => $merchant_info,
            'size' => 20,
            'font' => $fontArial,
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 100, 710, 40 );
        // =============================================================================
        // Voucher title
        $textData      = array(
            'text' => strtoupper( $data['voucher_title'] ),
            'size' => 50,
            'font' => $fontArialB,
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 150, 710, 60 );
        // =============================================================================
        // Voucher title
        $textData      = array(
            'text' => 'Amount: ' . \GeneralHelperTools::formatCurrency( $data['voucher_value'] ),
            'size' => 33,
            'font' => $fontArialB,
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 210, 710, 55 );
        // =============================================================================
        // Voucher Dates
        $dates         = 'Date Issued: ' . \GeneralHelperTools::formatDate( $data['delivery_date'] ) . '     ' .'Expiry Date: ' . \GeneralHelperTools::formatDate( $data['expiry_date'] );
        $textData      = array(
            'text' => $dates,
            'size' => 21,
            'font' => $fontArial,
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 265, 710, 45 );
        // =============================================================================
        // Voucher Code
        $textData      = array(
            'text' => $this->formatVoucherCode( $data['qr_code'] ),
            'size' => 60,
            'font' => $fontArialB,
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 310, 710, 60 );
        // =============================================================================
        // Terms of use
        $textData      = array(
            'text'   => wordwrap( $data['TermsOfUse'], 60, "\n", true ),
            'size'   => 15,
            'font'   => $fontArial,
            'align'  => 'left',
            'valign' => 'bottom',
        );
        $img           = $this->writeOnImage( $img, $textData, 270, 380, 530, 85 );
        // =============================================================================
        // Render img as HTML img tag
        // encode logo to data:image/png;base64,
//        $img->encode('data-url');
//        echo '<img style="" src="' . $img . '" alt="' . $qr_code . '" />';
        // =============================================================================
        // Save img to file and return filename
        $img->encode( 'png' );
        $filename      = $this->VirtualVouchersPath.md5( $data['qr_code'] ) . '.png' ;
        $img->save( $filename );
        return $filename;
    }
    
    /**
     * Get QRcode image according to the code
     * @param integer $code
     * @return stream
     */
    private function qrcode( $code ) {
        include_once(public_path( 'voucher/phpqrcode/qrlib.php' ));
        $image_name = md5( $code ) . '.png';
        $filename   = public_path( $image_name );
        // Generate PNG file
        \QRcode::png( $code, $filename, QR_ECLEVEL_H, 4 );
        if ( file_exists( $filename ) ) {
            // Create image resource from $filename
            $image     = imagecreatefrompng( $filename );
            // Begin capturing the byte stream
            ob_start();
            // generate the byte stream
            imagepng( $image );
            // and finally retrieve the byte stream
            $imagedata = ob_get_clean();
            // Testing
            //$img_tag = '<img src="data:image/png;base64,' . base64_encode($imagedata) . '" alt="' . $code . '" />';
            //return $img_tag;
            //
            // for Security delete image file from public folder
            unlink( $filename );
            //
            return $imagedata;
        } else {
            return FALSE;
        }//if ( file_exists( $filename ) )
    }
    
    /**
     * Write text on virtual voucher image
     * @param \Intervention\Image\Image $img
     * @param array $textData
     * @param integer $x x coordinate
     * @param integer $y y coordinate
     * @param integer $w width
     * @param integer $h height
     * @return \Intervention\Image\Image
     */
    private function writeOnImage( $img, $textData, $x, $y, $w, $h ) {
        // create a new empty image resource with transparent background
        $text = Image::canvas( $w, $h );
        // =====================================================================
        // Set Values
        (isset( $textData[ 'align' ] )) ? : $textData['align'] = 'center';
        (isset( $textData[ 'valign' ] )) ? : $textData['valign'] = 'middle';
        switch ( $textData[ 'align' ] ) {
            case 'left':
                $text_x = 0;
                break;
            case 'right':
                $text_x = $w - 5;
                break;
            default: // 'center'
                $text_x = $w / 2;
                break;
        }//switch ( $textData[ 'align' ] )
        switch ( $textData[ 'valign' ] ) {
            case 'top':
                $text_y = 0;
                break;
            case 'bottom':
                $text_y = $h - 5;
                break;
            default: // 'middle'
                $text_y = $h / 2;
                break;
        }//switch ( $textData[ 'valign' ] )
        // =====================================================================
        // Add text to canvas
        $text->text( $textData[ 'text' ], $text_x, $text_y, function($font) use ($textData) {
            $font->file( $textData[ 'font' ] );
            $font->size( $textData[ 'size' ] );
            $font->color( '#000000' );
            $font->align( $textData[ 'align' ] );
            $font->valign( $textData[ 'valign' ] );
            $font->angle( 0 );
        } );
        // insert text image into Voucher image
        $img->insert( $text, 'top-left', $x, $y );
        return $img;
    }
    
    /**
     * Return formated voucher code like "123 - 456 - 789"
     * @param integer $voucher_code
     * @return string
     */
    private function formatVoucherCode( $voucher_code ) {
        $str = substr( $voucher_code, 0, 3 ) . ' - ' . substr( $voucher_code, 3, 3 ) . ' - ' . substr( $voucher_code, 6, 3 );
        return $str;
    }
}
