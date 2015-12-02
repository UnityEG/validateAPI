<?php
namespace App\Http\Models;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinessTest extends \TestCase
{
    private $BusinessModelObject;
    
    public function setUp() {
        parent::setUp();
        $this->BusinessModelObject = new Business();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetStandardJsonFormat()
    {
//        \BusinessTransformer::shouldReceive('transform')->once()->andReturn(["data"=>[]]);
        $result = $this->BusinessModelObject->findOrFail(1)->getStandardJsonFormat();
        $this->assertArrayHasKey("data", $result);
    }
}
