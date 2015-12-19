<?php

namespace App\Http\Models\Reports;

/**
 * Description of LastSevenDaysReport
 *
 * @author Mohamed Atef <mohamdatif@yahoo.com>
 */
class LastSevenDaysReport {
    
    /**
     * Get Statistics of values about purchased vouchers in last seven days for Gift voucher parameters
     * @param integer $business_id
     * @param string $date
     * @return array | string
     */
    public function getLastSevenDaysGiftVoucherValuesForBusiness($business_id, $date){
        $prepared_requirements = $this->getCarbonBusinessVoucherParametersCollection($business_id, $date, 'gift');
        
        return (!is_array($prepared_requirements)) ? $prepared_requirements : $this->getLastSevenDaysVoucherValueForBusiness($prepared_requirements['date_after_utc'], $prepared_requirements['voucher_parameters']);
    }
    
    /**
     * Get Statistics of values about purchased vouchers in last seven days for Deal voucher parameters
     * @param integer $business_id
     * @param string $date
     * @return array | string
     */
    public function getLastSevenDaysDealVoucherValuesForBusiness($business_id, $date){
        $prepared_requirements = $this->getCarbonBusinessVoucherParametersCollection($business_id, $date, 'deal');
        
        return (!is_array($prepared_requirements)) ? $prepared_requirements : $this->getLastSevenDaysVoucherValueForBusiness($prepared_requirements['date_after_utc'], $prepared_requirements['voucher_parameters']);
    }
    
    /**
     * Perpare Data to get Report
     * @param integer $business_id
     * @param string $date
     * @param string $voucher_type
     * @return string | array
     */
    private function getCarbonBusinessVoucherParametersCollection($business_id, $date, $voucher_type){
        $date_after_utc = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$date.' 00:00:00', 'Pacific/Auckland')->setTimezone('UTC');
        $business = \App\Http\Models\Business::find($business_id);
        if ( !is_object( $business ) ) {
            return 'Business not found';
        }//if ( !is_object( $business_object ) )
        $voucher_parameters = $business->voucherParameter()->where('voucher_type', $voucher_type)->get();
        if ( $voucher_parameters->isEmpty() ) {
            return "No Vouchers to check";
        }//if ( $voucher_parameter_objects->isEmpty() )
        return [
            "date_after_utc" => $date_after_utc,
            "business" => $business,
            "voucher_parameters" => $voucher_parameters
        ];
    }

    /**
     * Get Statistics of values about purchased vouchers in last seven days for specific type of voucher parameters
     * @param \Carbon\Carbon $date_after_utc
     * @param \Illuminate\Database\Eloquent\Collection $voucher_parameters
     * @return string | array
     */
    private function getLastSevenDaysVoucherValueForBusiness( \Carbon\Carbon $date_after_utc, \Illuminate\Database\Eloquent\Collection $voucher_parameters) {
        $date_before_utc = clone $date_after_utc;
        $date_before_utc->subDay();
        $report_result = [];
        $total_value = 0;
        for ($i=0; $i <= 6; $i++){
            $date_string = $date_after_utc->toDateString();
            $report_result[$date_string] = [];
            $day_total_value = $this->getPurchasedValueForVoucherParametersCollection( $voucher_parameters, $date_before_utc, $date_after_utc );
            $report_result[$date_string]['day_total'] = $day_total_value;
            $total_value = $total_value + $day_total_value;
            $date_after_utc->subDay();
            $date_before_utc->subDay();
        }//for ($i=0; $i <= 6; $i++)
        $report_result['total_value'] = $total_value;
        $report_result['average'] = $total_value/7;
        return $report_result;
    }
    
    /**
     * Get purchased value for a Collection of VoucherParameters
     * @param \Illuminate\Database\Eloquent\Collection $voucher_parameters
     * @param \Carbon\Carbon $date_before_utc
     * @param \Carbon\Carbon $date_after_utc
     * @return Decimal
     */
    private function getPurchasedValueForVoucherParametersCollection(\Illuminate\Database\Eloquent\Collection $voucher_parameters, \Carbon\Carbon $date_before_utc, \Carbon\Carbon $date_after_utc){
        $day_total_value = 0;
            foreach ( $voucher_parameters as $voucher_parameter) {
                $value_of_sold_vouchers_per_voucher_parameter = $this->soldVouchersValuePerDay($voucher_parameter, $date_before_utc, $date_after_utc);
                $day_total_value = $day_total_value + $value_of_sold_vouchers_per_voucher_parameter;
            }//foreach ( $voucher_parameter_objects as $voucher_parameter_object)
            return $day_total_value;
    }


    /**
     * Get Value of purchased vouchers of single Voucher Parameter at specific period of time
     * @param \App\Http\Models\VoucherParameter $voucher_parameter
     * @param \Carbon\Carbon $date_before_utc
     * @param \Carbon\Carbon $date_after_utc
     * @return integer
     */
    private function soldVouchersValuePerDay( \App\Http\Models\VoucherParameter $voucher_parameter, \Carbon\Carbon $date_before_utc, \Carbon\Carbon $date_after_utc) {
        $purchased_voucher_collection = $voucher_parameter->vouchers()->whereBetween('created_at', [$date_before_utc, $date_after_utc])->get();
        $value_of_sold_vouchers_per_voucher_parameter = 0;
        foreach ( $purchased_voucher_collection as $purchased_voucher) {
            $value_of_sold_vouchers_per_voucher_parameter = $value_of_sold_vouchers_per_voucher_parameter + $purchased_voucher->value;
        }//foreach ( $purchased_voucher_collection as $purchased_voucher)
        return $value_of_sold_vouchers_per_voucher_parameter;
    }
}
