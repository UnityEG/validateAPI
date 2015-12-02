<?php

namespace App\Http\Controllers\BusinessControllers;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery as m;

class BusinessControllerTest extends \TestCase {

    private $BusinessControllerObject;
    public $BusinessModelMock;
    public $RequestMock;
    public $SampleInput = [
        "business_name"       => "Mondelez foods7",
        "trading_name"        => "borio7",
        "address1"            => "21,Aliquam erat volutpat",
        "address2"            => "10,Vestibulum dapibus nunc",
        "phone"               => "03987423",
        "website"             => "http://mondelez.com.eg",
        "business_email"      => "info@mondelez.com.eg",
        "contact_name"        => "ramy",
        "contact_mobile"      => "093883723",
        'available_hours_mon' => "From 09:00 AM To 10:00 PM",
        'available_hours_tue' => "From 09:00 AM To 10:00 PM",
        'available_hours_wed' => "From 09:00 AM To 10:00 PM",
        'available_hours_thu' => "From 09:00 AM To 10:00 PM",
        'available_hours_fri' => "From 09:00 AM To 10:00 PM",
        'available_hours_sat' => "From 09:00 AM To 10:00 PM",
        'available_hours_sun' => "From 09:00 AM To 10:00 PM",
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
                    "town_id" => "1",
                ]
            ],
            "postcode"       => [
                "data" => [
                    "postcode_id" => "1",
                ]
            ],
            "industry"       => [
                "data" => [
                    "industry_id" => "1",
                ]
            ],
            "business_types" => [
                "data" => [
                    "business_type_ids" => [
                        0 => "1"
                    ]
                ]
            ]
        ]
    ];

    public function setUp() {
        parent::setUp();
        $this->BusinessModelMock        = m::mock( 'Eloquent', 'App\Http\Models\Business' );
        $this->RequestMock              = m::mock( 'App\Http\Requests\Business\StoreBusinessRequest' );
        $general_helper_tools           = new \App\EssentialEntities\GeneralHelperTools\GeneralHelperTools();
        $this->BusinessControllerObject = new \App\Http\Controllers\BusinessController( $this->BusinessModelMock, $general_helper_tools );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowDisplayBusiness() {
        $business_model_mock = m::mock( 'App\Http\Models\Business' );
        $business_model_mock->shouldReceive( 'where' )->andReturnSelf();
        $business_model_mock->shouldReceive( 'first' )->andReturnSelf();
        $business_model_mock->shouldReceive( 'getStandardJsonFormat' )->andReturn( ["data" => [ ] ] );
        $result              = $this->BusinessControllerObject->showDisplayBusiness( '1', $business_model_mock );
        $this->assertArrayHasKey( "data", $result );
    }

    public function testStore() {
        $sample_input  = [
            "business_name"  => "Mondelez foods7",
            "trading_name"   => "borio7",
            "address1"       => "21,Aliquam erat volutpat",
            "address2"       => "10,Vestibulum dapibus nunc",
            "phone"          => "03987423",
            "website"        => "http://mondelez.com.eg",
            "business_email" => "info@mondelez.com.eg",
            "contact_name"   => "ramy",
            "contact_mobile" => "093883723",
            "relations"      => [
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
                        "town_id" => "1",
                    ]
                ],
                "postcode"       => [
                    "data" => [
                        "postcode_id" => "1",
                    ]
                ],
                "industry"       => [
                    "data" => [
                        "industry_id" => "1",
                    ]
                ],
                "business_types" => [
                    "data" => [
                        "business_type_ids" => [
                            0 => "1"
                        ]
                    ]
                ]
            ]
        ];
        $expected_data = [
            "data" => [
                "id"               => "37",
                "facebook_page_id" => "",
                "is_new"           => true,
                "is_active"        => false,
                "business_name"    => "Mondelez foods7",
                "trading_name"     => "borio7",
                "address1"         => "21,Aliquam erat volutpat",
                "address2"         => "10,Vestibulum dapibus nunc",
                "phone"            => "03987423",
                "website"          => "http://mondelez.com.eg",
                "business_email"   => "info@mondelez.com.eg",
                "contact_name"     => "ramy",
                "contact_mobile"   => "093883723",
                "is_featured"      => false,
                "is_display"       => true,
                "relations"        => [
                    "city"           => [
                        "data" => [
                            "id"      => "1",
                            "nz_city" => "Auckland"
                        ]
                    ],
                    "region"         => [
                        "data" => [
                            "id"     => "1",
                            "region" => "WHANGAREI"
                        ]
                    ],
                    "town"           => [
                        "data" => [
                            "id"      => "1",
                            "nz_town" => "Ahipara"
                        ]
                    ],
                    "postcode"       => [
                        "data" => [
                            "id"       => "1",
                            "postcode" => "0110"
                        ]
                    ],
                    "industry"       => [
                        "data" => [
                            "id"       => "1",
                            "industry" => "Accommodation"
                        ]
                    ],
                    "business_logos" => [
                        "data" => [
                            "logo_id" => "0"
                        ]
                    ],
                    "business_types" => [
                        "data" => [
                            0 => [
                                "id"   => "1",
                                "type" => "merchant"
                            ]
                        ]
                    ],
                    "users"          => [
                        "data" => [
                            0 => [
                                "id"               => "2",
                                "facebook_user_id" => "0",
                                "is_active"        => true,
                                "email"            => "owner@validate.co.nz",
                                "title"            => "Mrs",
                                "first_name"       => "monica",
                                "last_name"        => "doe",
                                "gender"           => "female",
                                "dob"              => "1992-08-14 00:00:00",
                                "address1"         => "Donec venenatis vulputate lorem",
                                "address2"         => "Curabitur suscipit suscipit tellus",
                                "phone"            => "01512348",
                                "mobile"           => "025388393",
                                "is_notify_deal"   => true,
                                "relations"        => [
                                    "city"        => [
                                        "data" => [
                                            "city_id" => "2"
                                        ]
                                    ],
                                    "region"      => [
                                        "data" => [
                                            "region_id" => "2"
                                        ]
                                    ],
                                    "town"        => [
                                        "data" => [
                                            "town_id" => "2"
                                        ]
                                    ],
                                    "postcode"    => [
                                        "data" => [
                                            "postcode_id" => "2"
                                        ]
                                    ],
                                    "user_groups" => [
                                        "data" => [ ]
                                    ],
                                    "business"    => [
                                        "data" => [ ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $user          = new \stdClass();
        $user->id      = 1;
        $request_mock  = m::mock( 'App\Http\Requests\Business\StoreBusinessRequest' );
        $request_mock->shouldReceive( 'json' )->once()->andReturn( $sample_input );
        \DB::shouldReceive( 'beginTransaction' )->once();
        $this->BusinessModelMock->shouldReceive( 'create' )->once()->andReturnSelf();
        $this->BusinessModelMock->shouldReceive( 'businessTypes->attach' )->andReturnSelf();
        \JWTAuth::shouldReceive( 'parseToken->authenticate' )->andReturn( $user );
//        \JWTAuth::shouldReceive( 'authenticate' );
        $this->BusinessModelMock->shouldReceive( 'users->attach' )->andReturnSelf();
//        $this->BusinessModelMock->shouldReceive( 'attach' )->andReturnSelf();
        \DB::shouldReceive( "commit" );
        $this->BusinessModelMock->shouldReceive( 'getStandardJsonFormat' )->andReturn( $expected_data );
        $result        = $this->BusinessControllerObject->store( $request_mock );
        $this->assertEquals( $result, $expected_data, "there is a problem in store method" );
    }

    public function testShowDisplayBusinessError() {
        $this->RequestMock->shouldReceive( 'json' )->andReturn( $this->SampleInput );
        \DB::shouldReceive( 'beginTransaction' );
        $this->BusinessModelMock->shouldReceive( 'create' )->andReturnNull();
        \DB::shouldReceive( 'rollBack' );
        $result = $this->BusinessControllerObject->store( $this->RequestMock );
        $this->assertInstanceOf( 'Illuminate\Http\JsonResponse', $result, "Error response is not right from store method" );
    }

    public function testPrepareDataForStoringHelper() {
        $expected_data    = [
            "city_id"             => 1,
            "region_id"           => 1,
            "town_id"             => 1,
            "postcode_id"         => 1,
            "industry_id"         => 1,
            "business_type_ids"   => [
                0 => 1
            ],
            "business_name"       => "Mondelez foods7",
            "trading_name"        => "borio7",
            "address1"            => "21,Aliquam erat volutpat",
            "address2"            => "10,Vestibulum dapibus nunc",
            "phone"               => "03987423",
            "website"             => "http://mondelez.com.eg",
            "business_email"      => "info@mondelez.com.eg",
            "contact_name"        => "ramy",
            "contact_mobile"      => "093883723",
            'available_hours_mon' => "From 09:00 AM To 10:00 PM",
            'available_hours_tue' => "From 09:00 AM To 10:00 PM",
            'available_hours_wed' => "From 09:00 AM To 10:00 PM",
            'available_hours_thu' => "From 09:00 AM To 10:00 PM",
            'available_hours_fri' => "From 09:00 AM To 10:00 PM",
            'available_hours_sat' => "From 09:00 AM To 10:00 PM",
            'available_hours_sun' => "From 09:00 AM To 10:00 PM",
            "is_new"              => true,
            "is_active"           => false,
            "is_featured"         => false,
            "is_display"          => true,
        ];
        $reflection_class = new \ReflectionClass( $this->BusinessControllerObject );
        $method           = $reflection_class->getMethod( 'prepareDataForStoringHelper' );
        $method->setAccessible( TRUE );
        $result           = $method->invokeArgs( $this->BusinessControllerObject, [$this->SampleInput ] );
        $this->assertEquals( $result, $expected_data );
    }

    /**
     * test listCreateRequest method
     * @test
     */
    public function testListCreateRequest() {
        $expected_result = ["data" => [[] ] ];
        $this->BusinessModelMock->shouldReceive( 'where->get' )->andReturn( [$this->BusinessModelMock ] );
        $this->BusinessModelMock->shouldReceive( 'getBeforeStandardArray' )->andReturn( [ ] );
        $result          = $this->BusinessControllerObject->listCreateRequest( $this->BusinessModelMock );
        $this->assertEquals( $result, $expected_result, 'Problem with response' );
    }
    
    /**
     * test acceptCreateRequest method
     * @test
     */
//    public function testAcceptCreateRequest(  ) {
//        $request_mock = m::mock('App\Http\Requests\Business\AcceptCreateBusinessRequest');
//        $this->BusinessModelMock->shouldReceive('find')->with(1)->once()->andReturnSelf();
//        $this->BusinessModelMock->shouldReceive('setAttribute')->twice();
//        $this->BusinessModelMock->shouldReceive('save')->once()->andReturnSelf();
////        todo continue add users of busiess to appropriate user groups
//        $result = $this->BusinessControllerObject->acceptCreateRequest(1, $request_mock, $this->BusinessModelMock);
//        $this->assertEquals($result, 'expected');
//    }

}
