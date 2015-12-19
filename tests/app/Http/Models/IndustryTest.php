<?php
namespace App\Http\Models;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndustryTest extends \TestCase
{
    use DatabaseTransactions;
    
    /**
     * Instance of Industry
     * @var \App\Http\Models\Industry
     */
    private $IndustryModelObject;

    public function setUp() {
        parent::setUp();
        $this->IndustryModelObject = new Industry();
    }
    
    /**
     * Test business Relationship method between Industry Model and Business Model
     * @test
     */
    public function testBusiness()
    {
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $this->IndustryModelObject->business());
    }
    
    /**
     * Test getTransformedCollection method 
     * @test
     */
    public function testGetTransformedCollection( ) {
        $this->assertArrayHasKey("data", $this->IndustryModelObject->getTransformedCollection());
    }
    
    /**
     * Test getTransformedArray method 
     * @test
     */
    public function testGetTransformedArray( ) {
        $this->assertArrayHasKey("data", factory(Industry::class)->create()->getTransformedArray());
    }
    
    /**
     * Test getBeforeStandard method
     * @test
     */
    public function testGetBeforeStandard( ) {
        $this->assertArrayHasKey("id", factory(Industry::class)->create()->getBeforeStandard());
    }
    
    /**
     * Test createNewBusiness method
     * @test
     */
    public function testCreateNewIndustry(){
        $raw_data = [
            "data" => [
                "industry"       => "cut",
            ]
        ];
        $this->assertArrayHasKey("data", $this->IndustryModelObject->createNewIndustry($raw_data));
    }
    
    /**
     * Test updateBusiness method
     * @test
     */
    public function testUpdateIndustry(){
        $industry_model_fake = factory(Industry::class)->create();
        $raw_data = [
            "data" => [
                "industry"       => "Fix",
            ]
        ];
        $this->assertArrayHasKey("data", $industry_model_fake->updateIndustry($raw_data));
    }
}
