<?php
namespace App\Http\Models;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegionTest extends \TestCase
{
    use DatabaseTransactions;
    
    /**
     * Instance of Region Model calss
     * @var \App\Http\Models\Region
     */
    private $RegionModelObject;
    
    public function setUp() {
        parent::setUp();
        $this->RegionModelObject = new Region();
    }
    
    /**
     * Test users Relationship method between Region Model and User Model
     * @test users method
     */
    public function testUsers(){
        $result = $this->RegionModelObject->users();
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $result);
    }
    
    /**
     * Test business Relationship method between Region Model and Business Model
     * @test
     */
    public function testBusiness(){
        $result = $this->RegionModelObject->business();
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $result);
    }
    
    /**
     * Test getTransformedCollection method.
     * @test
     * @return void
     */
    public function testGetTransformedCollection()
    {
        $result = $this->RegionModelObject->getTransformedCollection();
        $this->assertArrayHasKey("data", $result, "Invalid Response");
    }
    
    /**
     * Test getBeforeStandard method
     * @test
     */
    public function testGetBeforeStandard(){
        $region_model_fake = factory( \App\Http\Models\Region::class)->make();
        $result = $region_model_fake->getBeforeStandard();
        $this->assertArrayHasKey("id", $result, "Invalid Response");
    }
    
    /**
     * Test getHtmlCollection method
     * @test
     */
    public function testGetHtmlCollection(){
        $result = $this->RegionModelObject->getHtmlCollection();
        $this->assertContains('<select>', $result);
    }
}
