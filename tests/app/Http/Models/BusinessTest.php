<?php
namespace App\Http\Models;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinessTest extends \TestCase
{
    use DatabaseTransactions;
    
    /**
     * Instance of Business Model
     * @var Business
     */
    private $BusinessModelObject;
    
    public function setUp() {
        parent::setUp();
        $this->BusinessModelObject = new Business();
    }
    
    /**
     * Test users Relationship method between Business Model and User Model
     * @test
     */
    public function testUsers(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsToMany', $this->BusinessModelObject->users());
    }
    
    /**
     * Test voucherParameters Relationship method between Business Model and VoucherParameter Model
     * @test
     */
    public function testVoucherParameters(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $this->BusinessModelObject->voucherParameter());
    }
    
    /**
     * Test voucherValidationLogs Relationship method between Business Model and VoucherValidationLog Model
     * @test
     */
    public function testVoucherValidationLogs(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $this->BusinessModelObject->voucherValidationLogs());
    }
    
    /**
     * Test businessLogos Relationship method between Business Model and BusinessLogo Model
     * @test
     */
    public function testBusinessLogos(){
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $this->BusinessModelObject->businessLogos());
    }
    
    /**
     * Test businessTypes Relationship method between Business Model and BusinessType Model
     * @test
     */
    public function testBusinessTypes(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsToMany', $this->BusinessModelObject->businessTypes());
    }
    
    /**
     * Test city Relationship method between Business Model and City Model
     * @test
     */
    public function testCity(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->BusinessModelObject->city());
    }
    
    /**
     * Test region Relationship method between Business Model and Region Model
     * @test
     */
    public function testRegion(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->BusinessModelObject->region());
    }
    
    /**
     * Test town Relationship method between Business Model and Town Model
     * @test
     */
    public function testTown(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->BusinessModelObject->town());
    }
    
    /**
     * Test postcode Relationship mehod between Business Model and Postcode Model
     * @test
     */
    public function testPostcode(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->BusinessModelObject->postcode());
    }
    
    /**
     * Test industry Relationship method between Business Model and Industry Model
     */
    public function testIndustry(){
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $this->BusinessModelObject->industry());
    }
    
    /**
     * Test getActiveLogo method 
     * @test
     */
    public function testGetActiveLogo(){
        $fake_business_object = factory(Business::class)->make(['id'=>1, 'created_at'=>NULL, 'updated_at'=>NULL]);
        $fake_logo_object = factory( \App\Http\Models\BusinessLogo::class)->create(['business_id'=>$fake_business_object->id]);
        $fake_business_object->logo_id = $fake_logo_object->id;
        $this->assertInstanceOf('\App\Http\Models\BusinessLogo', $fake_business_object->getActiveLogo());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetStandardJsonFormat()
    {
        $this->assertArrayHasKey("data", factory(Business::class)->make(['id'=>1])->getStandardJsonFormat());
    }
    
    /**
     * Test getStandardJsonCollection method
     * @test
     */
    public function testGetStandardJsonCollection(){
        $this->assertArrayHasKey("data", $this->BusinessModelObject->getStandardJsonCollection());
    }
    
    /**
     * Test getStandardJsonCollection method with condition parameter
     */
    public function testSearchActiveBusinessByBusinessName( ) {
        $this->assertArrayHasKey("data", $this->BusinessModelObject->searchActiveBusinessByBusinessName('M'));
    }
    
    /**
     * Test getBeforeStandardArray method
     * @test
     */
    public function testGetBeforeStandardArray(){
        $this->assertArrayHasKey("id", factory(Business::class)->make(['id'=>1]));
    }
    
    /**
     * Test createNewBusiness method
     * @test
     */
    public function testCreateNewBusiness(){
        $raw_data = [
            "data" => [
                "business_name"       => "Mondelez foods8",
                "trading_name"        => "borio8",
                "bank_account_number" => "1234567891257489",
                "address1"            => "21,Aliquam erat volutpat",
                "address2"            => "10,Vestibulum dapibus nunc",
                "phone"               => "03987423",
                "website"             => "http://mondelez.com.eg",
                "business_email"      => "info@mondelez.com.eg",
                "contact_name"        => "ramy",
                "contact_mobile"      => "093883723",
                "available_hours_mon" => "From 09:00 AM To 10:00 PM",
                "available_hours_tue" => "From 09:00 AM To 10:00 PM",
                "available_hours_wed" => "From 09:00 AM To 10:00 PM",
                "available_hours_thu" => "From 09:00 AM To 10:00 PM",
                "available_hours_fri" => "From 09:00 AM To 10:00 PM",
                "available_hours_sat" => "From 09:00 AM To 10:00 PM",
                "available_hours_sun" => "From 09:00 AM To 10:00 PM",
                "relations"           => [
                    "city"           => [
                        "data" => [
                            "city_id" => "1"
                        ]
                    ],
                    "region"         => [
                        "data" => [
                            "region_id" => "1"
                        ]
                    ],
                    "town"           => [
                        "data" => [
                            "town_id" => "1"
                        ]
                    ],
                    "postcode"       => [
                        "data" => [
                            "postcode_id" => "1"
                        ]
                    ],
                    "industry"       => [
                        "data" => [
                            "industry_id" => "1"
                        ]
                    ],
                    "business_types" => [
                        "data" => [
                            "business_type_ids" => ["1", "2"]
                        ]
                    ]
                ]
            ]
        ];
        \JWTAuth::shouldReceive('parseToken->authenticate')->andReturn(  factory(\App\User::class)->make());
        $this->assertArrayHasKey("data", $this->BusinessModelObject->createNewBusiness($raw_data));
    }
    
    /**
     * Test updateBusiness method
     * @test
     */
    public function testUpdateBusiness(){
        $business_model_fake = factory(Business::class)->create();
        $raw_data = [
            "data" => [
                "business_name"       => "Mondelez foods8",
                "trading_name"        => "borio8",
                "bank_account_number" => "1234567891257489",
                "address1"            => "21,Aliquam erat volutpat",
                "address2"            => "10,Vestibulum dapibus nunc",
                "phone"               => "03987423",
                "website"             => "http://mondelez.com.eg",
                "business_email"      => "info@mondelez.com.eg",
                "contact_name"        => "ramy",
                "contact_mobile"      => "093883723",
                "available_hours_mon" => "From 09:00 AM To 10:00 PM",
                "available_hours_tue" => "From 09:00 AM To 10:00 PM",
                "available_hours_wed" => "From 09:00 AM To 10:00 PM",
                "available_hours_thu" => "From 09:00 AM To 10:00 PM",
                "available_hours_fri" => "From 09:00 AM To 10:00 PM",
                "available_hours_sat" => "From 09:00 AM To 10:00 PM",
                "available_hours_sun" => "From 09:00 AM To 10:00 PM",
                'is_active' => "0",
                'is_featured' => '1',
                'is_display' => '1',
                "relations"           => [
                    "logo"           => [
                        "data" => [
                            "logo_id" => "1"
                        ]
                    ],
                    "city"           => [
                        "data" => [
                            "city_id" => "1"
                        ]
                    ],
                    "region"         => [
                        "data" => [
                            "region_id" => "1"
                        ]
                    ],
                    "town"           => [
                        "data" => [
                            "town_id" => "1"
                        ]
                    ],
                    "postcode"       => [
                        "data" => [
                            "postcode_id" => "1"
                        ]
                    ],
                    "industry"       => [
                        "data" => [
                            "industry_id" => "1"
                        ]
                    ],
                    "business_types" => [
                        "data" => [
                            "business_type_ids" => ["1", "2"]
                        ]
                    ]
                ]
            ]
        ];
        $this->assertArrayHasKey("data", $business_model_fake->updateBusiness($raw_data));
    }
}
