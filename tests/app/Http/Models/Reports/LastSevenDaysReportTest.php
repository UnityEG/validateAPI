<?php
namespace App\Http\Models\Reports;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LastSevenDaysReportTest extends \TestCase
{
    use DatabaseTransactions;
    
    private $LastSevenDaysReport;

    public function setUp() {
        parent::setUp();
        $this->LastSevenDaysReport = new \App\Http\Models\Reports\LastSevenDaysReport;
    }

    /**
     * Test getLastSevenDaysGiftVoucherValuesForBusiness.
     * @test 
     * @return void
     */
    public function testGetLastSevenDaysGiftVoucherValuesForBusiness()
    {
        //Fake Business
        $fake_business = factory( \App\Http\Models\Business::class)->create();
        
        //Fake Voucher Paramteres
        $fake_voucher_parameter1 = factory( \App\Http\Models\VoucherParameter::class)->create(['business_id'=>$fake_business->id]);
        $fake_voucher_parameter2 = factory( \App\Http\Models\VoucherParameter::class)->create(['business_id'=>$fake_business->id]);
        
        //Fake Vouchers
        $fake_voucher1_of_vp1 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter1->id, 'value'=>50, 'created_at'=>"2015-12-09 10:00:00"]);
        $fake_voucher1_of_vp2 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter2->id, 'value'=>100, 'created_at'=>"2015-12-09 09:00:00"]);
        
        $fake_voucher2_of_vp1 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter1->id, 'value'=>200, 'created_at'=>"2015-12-08 10:00:00"]);
        $fake_voucher2_of_vp2 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter2->id, 'value'=>300, 'created_at'=>"2015-12-08 09:00:00"]);
        
        
        $result = $this->LastSevenDaysReport->getLastSevenDaysGiftVoucherValuesForBusiness($fake_business->id, "2015-12-10");
        $this->assertArrayHasKey("average", $result);
        $this->assertArrayHasKey("total_value", $result);
    }
    
    /**
     * Test getLastSevenDaysGiftVoucherValuesForBusiness respond with Business not found error message
     */
    public function testGetLastSevenDaysGiftVoucherValuesForBusinessRespondWithBusinessNotFoundError(){
        $this->assertSame('Business not found', $this->LastSevenDaysReport->getLastSevenDaysGiftVoucherValuesForBusiness( 9000, "2015-12-19"));
    }
    
    /**
     * Test getLastSevenDaysGiftVoucherValuesForBusiness respond with No Vouchers to check error message
     */
    public function testGetLastSevenDaysGiftVoucherValuesForBusinessRespondWithNoVouchersToCheckError(){
        $this->assertSame("No Vouchers to check", $this->LastSevenDaysReport->getLastSevenDaysGiftVoucherValuesForBusiness(factory(\App\Http\Models\Business::class)->create()->id, "2015-12-19"));
    }
    
    /**
     * Test getLastSevenDaysDealVoucherValuesForBusiness.
     * @test 
     * @return void
     */
    public function testGetLastSevenDaysDealVoucherValuesForBusiness()
    {
        //Fake Business
        $fake_business = factory( \App\Http\Models\Business::class)->create();
        
        //Fake Voucher Paramteres
        $fake_voucher_parameter1 = factory( \App\Http\Models\VoucherParameter::class)->create(['business_id'=>$fake_business->id, 'voucher_type'=>'deal']);
        $fake_voucher_parameter2 = factory( \App\Http\Models\VoucherParameter::class)->create(['business_id'=>$fake_business->id, 'voucher_type'=>'deal']);
        
        //Fake Vouchers
        $fake_voucher1_of_vp1 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter1->id, 'value'=>25, 'created_at'=>"2015-12-09 10:00:00"]);
        $fake_voucher1_of_vp2 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter2->id, 'value'=>75, 'created_at'=>"2015-12-09 09:00:00"]);
        
        $fake_voucher2_of_vp1 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter1->id, 'value'=>150, 'created_at'=>"2015-12-08 10:00:00"]);
        $fake_voucher2_of_vp2 = factory(\App\Http\Models\Voucher::class)->create(['voucher_parameter_id'=>$fake_voucher_parameter2->id, 'value'=>50, 'created_at'=>"2015-12-08 09:00:00"]);
        
        
        $result = $this->LastSevenDaysReport->getLastSevenDaysDealVoucherValuesForBusiness($fake_business->id, "2015-12-10");
        $this->assertArrayHasKey("average", $result);
        $this->assertArrayHasKey("total_value", $result);
    }
    
//    todo create testGetLastSevenDaysDealVoucherValuesForBusinessRespondWithError
}
