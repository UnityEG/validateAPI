<?php

namespace App\Http\Controllers\VouchersControllers;

use GeneralHelperTools;
use App\Http\Controllers\ApiController;
use App\Http\Models\Voucher;
use App\Http\Models\VoucherParameter;
use Carbon\Carbon;
use DB;
use JWTAuth;

class VouchersController extends ApiController {

    public function __construct(){
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of all purchased vouchers.
     *
     * @return array
     */
    public function index(Voucher $voucher_model) {
        return $voucher_model->getStandardJsonCollection();
    }

    /**
     * Store a newly Voucher.
     *
     * @param  array $purchased_voucher_to_create
     * @return \App\Http\Models\Voucher
     */
    public function store( array $purchased_voucher_to_create ) {
        $voucher_parameter_object = VoucherParameter::find($purchased_voucher_to_create['voucher_parameter_id']);
        $current_user_object = JWTAuth::parseToken()->authenticate();
        $purchased_voucher_to_create['user_id'] = $current_user_object->id;
        $purchased_voucher_to_create['is_mail_sent'] = 0;
        $purchased_voucher_to_create['recipient_email'] = (!empty($purchased_voucher_to_create['recipient_email'])) ? $purchased_voucher_to_create['recipient_email'] : $current_user_object->email;
        $purchased_voucher_to_create['status'] = 'valid';
        ('gift' == $voucher_parameter_object->voucher_type) ?: $purchased_voucher_to_create['value'] = $voucher_parameter_object->value;
        $purchased_voucher_to_create['balance'] = $purchased_voucher_to_create['value'];
        $purchased_voucher_to_create['code'] = self::generateVoucherCode($voucher_parameter_object->voucher_type);
        // Convert local time to UTC time in order to save it in DB
        $purchased_voucher_to_create['delivery_date'] = (!empty($purchased_voucher_to_create['delivery_date']) && !$purchased_voucher_to_create['is_instore']) ? GeneralHelperTools::utcDateTime($purchased_voucher_to_create['delivery_date'] . ' 00:00:00', 'd/m/Y H:i:s') : Carbon::today();
//        expiry date -1 second
            $purchased_voucher_to_create['expiry_date'] = $voucher_parameter_object->valid_until->subSeconds(1);
        DB::beginTransaction();
        if($purchased_voucher = Voucher::create($purchased_voucher_to_create)){
            $voucher_parameter_update_data = [
                'is_purchased'       => TRUE,
                'purchased_quantity' => $voucher_parameter_object->purchased_quantity + 1
            ];
            (!$voucher_parameter_object->is_limited_quantity) ?  : $voucher_parameter_update_data['stock_quantity'] = $voucher_parameter_object->stock_quantity - 1;
            $voucher_parameter_object->update($voucher_parameter_update_data);
            DB::commit();
            return $purchased_voucher;
        }//if(Voucher::create($purchased_voucher_to_create))
        else{
            DB::rollBack();
            return $this->setStatusCode(500)->respondWithError( 'Internal Error');
        }
    }

    /**
     * Display the specified purchased voucher.
     *
     * @param  int  $id
     * @return array
     */
    public function show( $id ) {
        return Voucher::findOrFail((int)$id)->getStandardJsonFormat();
    }
    
    /**
     * List all vouchers purchased by specific customer using customer's ID
     * @param integer $customer_id
     * @param Voucher $voucher_model
     * @return array
     */
    public function listAllVouchersPurchasedByCustomer($customer_id, Voucher $voucher_model){
        $response["data"] = [];
        foreach ( $voucher_model->where('user_id', (int)$customer_id)->get() as $voucher_object) {
            $response["data"][] = $voucher_object->getBeforeStandardArray();
        }
        return $response;
    }
    
//    Helper Methods
    
    /**
     * Generate Voucher code and each voucher type start with unique number ( gift=>3, concession=>4, deal=>5, birthday=>6, discount=>7)
     * @param string $voucher_param_type
     * @return integer
     */
    private function generateVoucherCode($voucher_param_type) {
        switch ( $voucher_param_type ) {
            case 'gift':
                $code = 3;
                break;
            case 'concession':
                $code = 4;
                break;
            case 'deal':
                $code = 5;
                break;
            case 'birthday':
                $code = 6;
                break;
            case 'discount':
                $code = 7;
                break;
        }//switch ( $voucher_param_type )
        $code .= mt_rand(00000001, 99999999);
        return ((Voucher::where('code', $code)->exists()) || (9 > strlen($code))) ? $this->generateVoucherCode( $voucher_param_type ) : $code;
    }
}
