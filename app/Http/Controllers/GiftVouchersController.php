<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use App\User;
use DB;
//use App\Merchant;
use App\Http\Models\GiftVoucher;
use App\aaa\Transformers\GiftVoucherTransformer;
//
use App\Http\Models\GiftVoucherValidation;
use App\aaa\Transformers\GiftVoucherValidationTransformer;

class GiftVouchersController extends ApiController {

    protected $GiftVoucherTransformer;
    protected $GiftVoucherValidationTransformer;

    public function __construct(GiftVoucherTransformer $GiftVoucherTransformer,
            GiftVoucherValidationTransformer $GiftVoucherValidationTransformer) {
        // Apply the jwt.auth middleware to all methods in this controller
//        $this->middleware('jwt.auth');
        // 
        $this->GiftVoucherTransformer = $GiftVoucherTransformer;
        $this->GiftVoucherValidationTransformer = $GiftVoucherValidationTransformer;
//        $this->middleware('auth.basic');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
        $GiftVouchers = GiftVoucher::All();
//        return $GiftVouchers;
        return $this->respond([
                    'data' => $this->GiftVoucherTransformer->transformCollection($GiftVouchers->toArray()), // $GiftVouchers->all() equals to $GiftVouchers->toArray()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
        return 'بسم الله';
        $GiftVoucher = GiftVoucher::find($id);
        //
        return $this->display($GiftVoucher);
    }

    protected function display($GiftVoucher) {
        //
        if (!$GiftVoucher) {
            // 'GiftVoucher does not exist'
            return $this->respondNotFound('Gift Voucher does not exist');
        }
        //
        // get Voucher Validations Log =========================================
        $GiftVoucherValidationGroup = GiftVoucherValidation::where('giftvoucher_id', '=', $GiftVoucher->id)->get();
        // transform
        $ValidationsLog = $this->GiftVoucherValidationTransformer
                            ->transformCollection($GiftVoucherValidationGroup->toArray());
        // set data array ======================================================
        $respondArray = ['data' => $this->GiftVoucherTransformer->transform($GiftVoucher->toArray())];
        // add Validations Log to data array
        $respondArray['data']['validations_log'] = $ValidationsLog;
        //
        return $this->respond($respondArray);
    }

    public function check($GiftVoucherCode) {
        //
        $GiftVoucher = GiftVoucher::where('qr_code', '=', $GiftVoucherCode)->first();
        //
        return $this->display($GiftVoucher);
        /*
          // Authorization Check
          if (!g::has($this->controllerTitle . ' - Check'))
          return g::back();
          //
          $input = Input::all(); // all() 

          $rules = array('qr_code' => 'required|numeric|digits:9');
          $messages = array(
          'required' => 'Voucher Code is required.',
          'numeric' => 'Voucher Code must be a number.',
          'digits' => 'Voucher Code must be 9 digits.',
          );
          $validation = Validator::make($input, $rules, $messages);
          if (!$validation->passes()) {
          return Redirect::route($this->route . '.search')
          ->withInput()
          ->withErrors($validation)
          ->with('message', 'There were some errors.');
          }

          $item = GiftVoucher::where('qr_code', '=', $input['qr_code'])->first();
          if (is_null($item)) {
          Session::flash('message', 'There is no Gift Voucher with this code: ' . $input['qr_code']);
          return Redirect::route($this->route . '.search');
          }
          //
          // Get Voucher log =====================================================
          $group = GiftVoucherValidation::where('giftvoucher_id', '=', $item->id)->get();
          return View::make($this->route . '.show', compact('item', 'group'));
          //        return View::make($this->route . '.show', compact('item'));
         */
    }

    public function findByMerchant($UserId) {
        //  refactor select string, it used in findByMerchant and findByMerchantAndStatus
        //
        $GiftVouchers = DB::select('select '
                        . 'gv.* '
                        . 'from '
                        . 'giftvoucher AS gv, gift_vouchers_parameters AS gvp, merchants AS m, users AS u '
                        . 'where '
                        . 'u.id = ? '
                        . 'AND gv.gift_vouchers_parameters_id = gvp.id '
                        . 'AND gvp.MerchantID = m.id '
                        . 'AND m.user_id = u.id '
                        , array($UserId));
        //
        $GiftVouchers = json_decode(json_encode($GiftVouchers), true);
        // if not found
        if (!$GiftVouchers || count($GiftVouchers) == 0) {
            // 'GiftVoucher does not exist'
            return $this->respondNotFound('Gift Voucher does not exist');
        }
        //
        return $this->respond([
                    'data' => $this->GiftVoucherTransformer->transformCollection($GiftVouchers), // $GiftVouchers->all() equals to $GiftVouchers->toArray()
        ]);
    }

    public function findByMerchantAndStatus($UserId, $StatusId) {
        //  refactor select string, it used in findByMerchant and findByMerchantAndStatus
        //
        $GiftVouchers = DB::select('select '
                        . 'gv.* '
                        . 'from '
                        . 'giftvoucher AS gv, gift_vouchers_parameters AS gvp, merchants AS m, users AS u '
                        . 'where '
                        . 'u.id = ? '
                        . 'AND gv.status = ? '
                        . 'AND gv.gift_vouchers_parameters_id = gvp.id '
                        . 'AND gvp.MerchantID = m.id '
                        . 'AND m.user_id = u.id '
                        , array($UserId, $StatusId));
        //
        $GiftVouchers = json_decode(json_encode($GiftVouchers), true);
        // if not found
        if (!$GiftVouchers || count($GiftVouchers) == 0) {
            // 'GiftVoucher does not exist'
            return $this->respondNotFound('Gift Voucher does not exist');
        }
        //
        return $this->respond([
                    'data' => $this->GiftVoucherTransformer->transformCollection($GiftVouchers), // $GiftVouchers->all() equals to $GiftVouchers->toArray()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
