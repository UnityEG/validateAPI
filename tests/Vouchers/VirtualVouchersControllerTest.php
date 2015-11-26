<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VirtualVouchersControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowVirtualVoucherImage()
    {
        $virtual_voucher_object = new \App\Http\Controllers\VouchersControllers\VirtualVouchersController();
        $result = $virtual_voucher_object->showVirtualVoucherImage('574649665');
        $this->assertStringStartsWith('http://', $result);
    }
    
    public function testShowVirtualVoucherImageWithInvalidCode( ) {
        $virtual_voucher_object = new \App\Http\Controllers\VouchersControllers\VirtualVouchersController();
        $result = $virtual_voucher_object->showVirtualVoucherImage('123548');
        $this->assertEquals('Invalid code or not found Image', $result);
    }
}
