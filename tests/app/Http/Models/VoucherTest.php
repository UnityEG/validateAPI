<?php
namespace App\Http\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use TestCase;

class VoucherTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * Instance of Voucher Model
     * @var Voucher
     */
    private $VoucherModel;

    public function setUp() {
        parent::setUp();
        $this->VoucherModel = new Voucher();
    }

    /**
     * Test user Relationship method between Voucher Model and User Model
     * @test
     * @return void
     */
    public function testUser()
    {
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->VoucherModel->user());
    }

    /**
     * Test voucherParameter Relationship method between Voucher Model and VoucherParameter Model
     * @test
     * @return void
     */
    public function testVoucherParameter()
    {
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->VoucherModel->voucherParameter());
    }

    /**
     * Test voucherValidationLogs Relationship method between Voucher Model and VoucherValidationLog Model
     * @test
     * @return void
     */
    public function testVoucherValidationLogs()
    {
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $this->VoucherModel->voucherValidationLogs());
    }

    /**
     * Test order Relationship method between Voucher Model and VoucherValidationLog Model
     * @test
     * @return void
     */
    public function testOrder()
    {
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->VoucherModel->order());
    }
    
    /**
     * Test getStandrdJsonCollection method
     * @test getStandardJsonCollection
     */
    public function testGetStandardJsonCollection( ) {
        $this->assertArrayHasKey("data", $this->VoucherModel->getStandardJsonCollection());
    }
    
    /**
     * Test getStandrdJsonCollection method
     * @test getStandardJsonFormat
     */
    public function testGetStandardJsonFormat( ) {
        $result = factory( \App\Http\Models\Voucher::class)->create()->getStandardJsonFormat();
        $this->assertArrayHasKey("data", $result);
        $this->assertArrayHasKey("id", $result['data']);
    }
    
    /**
     * Test getBeforeStandrdArray method
     * @test getBeforeStandrdArray
     */
    public function testGetBeforeStandardArray( ) {
        $this->assertArrayHasKey("id", factory( \App\Http\Models\Voucher::class)->create()->getBeforeStandardArray());
    }
    
    /**
     * Test getVirtualVoucherData
     */
    public function testGetVirtualVoucherData( ) {
        $expected_keys = [
            'm_logo_filename',
            'qr_code',
            'delivery_date',
            'expiry_date',
            'voucher_value',
            'merchant_business_name',
            'voucher_title',
            'TermsOfUse',
            'merchant_business_address1',
            'business_suburb',
            'merchant_business_phone',
            'merchant_business_website',
            'recipient_email',
            'customer_name',
            'customer_email',
        ];
        $result = factory(Voucher::class)->create()->getVirtualVoucherData();
        foreach ( $expected_keys as $key) {
            $this->assertArrayHasKey($key, $result);
        }
    }
}
