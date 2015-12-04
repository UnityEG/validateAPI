<?php

namespace App\EssentialEntities\GeneralHelperTools;

use Carbon\Carbon;
use Image;

class GeneralHelperTools {

    /**
     * Return formated voucher code like "123 - 456 - 789"
     * @param integer $voucher_code
     * @return string
     */
    public static function formatVoucherCode( $voucher_code ) {
        $str = substr( $voucher_code, 0, 3 ) . ' - ' . substr( $voucher_code, 3, 3 ) . ' - ' . substr( $voucher_code, 6, 3 );
        return $str;
    }
    
    /**
     * Get formated currency like "$4,134.26"
     * @param double $value
     * @return string
     */
    public static function formatCurrency( $value ) {
        $str = "$" . number_format( $value, 2, '.', ',' );
        return $str;
    }
    
    /**
     * Return with Formated datetime object default time zone is "Pacific/Auchland" and default format is "d/m/y H:i:s"
     * @param string $utc_date_time
     * @param string $local_time_zone time zone to deal with
     * @return string Formated datetime
     */
    public static function formatDateTime( $utc_date_time, $local_time_zone = 'Pacific/Auckland' ) {
        return (0 < strtotime($utc_date_time))? self::fDT( $utc_date_time, 'd/m/Y H:i:s', $local_time_zone ) : '0000-00-00 00:00:00';
    }
    
    /**
     * Return formatted date from UTC to different time zone by default "Pacific/Auckland" and format 'd/m/Y'
     * @param string $utc_date_time
     * @param string $local_time_zone time zone to convert UTC time into it default is "Pacific/Auckland"
     * @return string
     */
    public static function formatDate( $utc_date_time, $local_time_zone = 'Pacific/Auckland' ) {
//        todo return with empty string if $utc_date_time is invalid time
        return self::fDT( $utc_date_time, 'd/m/Y', $local_time_zone );
    }

//    todo Modify formatTime method to change g with self and document method
    public static function formatTime( $utc_date_time, $local_time_zone = 'Pacific/Auckland' ) {
        //todo return with empty string if $utc_date_time is invalid time
        return g::fDT( $utc_date_time, 'H:i:s', $local_time_zone );
    }

    public static function utcDateTime( $local_date_time, $format = 'd/m/Y', $local_time_zone = 'Pacific/Auckland' ) {
        //
        return Carbon::createFromFormat( $format, $local_date_time, $local_time_zone )->setTimezone( 'UTC' ); //->startOfDay();
    }

    private static function fDT( $utc_date_time, $format, $local_time_zone ) {
        // formatDateTime
        $local = Carbon::parse( $utc_date_time, 'UTC' )->timezone( $local_time_zone );
        $str   = $local->format( $format );
        return $str;
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
    public static function writeOnImage( $img, $textData, $x, $y, $w, $h ) {
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
     * Get QRcode image according to the code
     * @param integer $code
     * @return stream
     */
    public static function QRcode( $code ) {
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
     * Create Virtaul Voucher Image accordin to the data in the array and return with the voucher image path
     * @param array $data
     * @return string
     */
    public static function voucher( array $data ) {
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
        $logo = Image::make( $m_logo_filename )->resize( 200, 200, function ($constraint) {
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
        $QRcode = Image::make( self::QRcode( $qr_code ) )->resize( 200, 200, function ($constraint) {
            $constraint->aspectRatio();
        } );
        // Fill up the blank spaces with transparent color
        $QRcode->resizeCanvas( 200, 200, 'center', false, array( 255, 255, 255, .7 ) );
        // insert QRcode into Voucher image
        $img->insert( $QRcode, 'top-left', 40, 260 );
        // =============================================================================
        // Merchant name
        $textData      = array(
            'text' => $merchant_business_name,
            'size' => 50,
            'font' => $fontArialB,
        );
        $img           = self::writeOnImage( $img, $textData, 270, 20, 710, 80 );
        // =============================================================================
        // Merchant info
        $merchant_info = $merchant_business_address1 . '  ' . $merchant_business_phone;
        $textData      = array(
            'text' => $merchant_info,
            'size' => 20,
            'font' => $fontArial,
        );
        $img           = self::writeOnImage( $img, $textData, 270, 100, 710, 40 );
        // =============================================================================
        // Voucher title
        $textData      = array(
            'text' => strtoupper( $voucher_title ),
            'size' => 50,
            'font' => $fontArialB,
        );
        $img           = self::writeOnImage( $img, $textData, 270, 150, 710, 60 );
        // =============================================================================
        // Voucher title
        $textData      = array(
            'text' => 'Amount: ' . self::formatCurrency( $voucher_value ),
            'size' => 33,
            'font' => $fontArialB,
        );
        $img           = self::writeOnImage( $img, $textData, 270, 210, 710, 55 );
        // =============================================================================
        // Voucher Dates
        $dates         = 'Date Issued: ' . self::formatDateTime( $delivery_date ) . '     ' .
                'Expiry Date: ' . self::formatDateTime( $expiry_date );
        $textData      = array(
            'text' => $dates,
            'size' => 21,
            'font' => $fontArial,
        );
        $img           = self::writeOnImage( $img, $textData, 270, 265, 710, 45 );
        // =============================================================================
        // Voucher Code
        $textData      = array(
            'text' => self::formatVoucherCode( $qr_code ),
            'size' => 60,
            'font' => $fontArialB,
        );
        $img           = self::writeOnImage( $img, $textData, 270, 310, 710, 60 );
        // =============================================================================
        // Terms of use
        $textData      = array(
            'text'   => wordwrap( $TermsOfUse, 60, "\n", true ),
            'size'   => 15,
            'font'   => $fontArial,
            'align'  => 'left',
            'valign' => 'bottom',
        );
        $img           = self::writeOnImage( $img, $textData, 270, 380, 530, 85 );
        // =============================================================================
        // Render img as HTML img tag
        // encode logo to data:image/png;base64,
//        $img->encode('data-url');
//        echo '<img style="" src="' . $img . '" alt="' . $qr_code . '" />';
        // =============================================================================
        // Save img to file and return filename
        $img->encode( 'png' );
        $filename      = config( 'validateconf.default_virtual_vouchers_images_path').md5( $qr_code ) . '.png' ;
        $img->save( $filename );
        return $filename;
    }

    /**
     * Search for keys in array recursively and return with value or false
     * @param array $array
     * @param string $keySearch
     * @return mixed 
     */
    public function arrayKeySearchRecursively(array $array, $keySearch)
    {
//        todo Fix get value if equal to 0
//        todo solving the problem of deep array with adding another loop to walk throug non array values in the main array
        foreach ($array as $key => $item) {
            if ($key === $keySearch) {
                if ( $array[$keySearch] === FALSE || $array[$keySearch] === 0 || $array[$keySearch] === "0") {
                    return (FALSE === $array[$keySearch]) ? "false" : "0";
                }else{
                    return $array[$keySearch];
                }
            }else {
                if (is_array($item) && ($result = $this->arrayKeySearchRecursively($item, $keySearch))) {
                   return $result;
                }//if (is_array($item) && ($result = $this->findKey($item, $keySearch)))
            }//if ($key === $keySearch)
        }
        return false;
    }
}
