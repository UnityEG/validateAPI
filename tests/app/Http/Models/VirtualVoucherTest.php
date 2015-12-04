<?php
namespace App\Http\Models;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VirtualVoucherTest extends \TestCase
{
    private $VirtualVoucherModelObject;
    public function setUp( ) {
        parent::setUp();
        $this->VirtualVoucherModelObject = new VirtualVoucher();
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFind()
    {
        $result = $this->VirtualVoucherModelObject->find(356546076);
        $this->assertNotEquals(FALSE, $result);
    }
    
    public function testFindRespondWithFalse( ) {
        $result = $this->VirtualVoucherModelObject->find(123);
        $this->assertEquals(False, $result);
    }
    
    public function testCreate( ) {
        $voucher_model = \App\Http\Models\Voucher::findOrFail(1);
        $result = $this->VirtualVoucherModelObject->create($voucher_model);
        $filename =$this->VirtualVoucherModelObject->VirtualVouchersPath.md5($voucher_model->code).".png";
        $this->assertFileExists($filename, $result);
    }
    
//    public function testGenerateVirtualVoucher( ) {
//        $voucher_parameter_mock = \Mockery::mock('Model', '\App\Http\Models\VoucherParameter');
//    }
}
