<?php

namespace App\Http\Models;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UseTermTest extends \TestCase
{
    use DatabaseTransactions;
    
    /**
     * Instance of UseTerm Model class
     * @var \App\Http\Models\UseTerm
     */
    private $UseTerm;
    
    public function setUp(  ) {
        parent::setUp();
        $this->UseTerm = new UseTerm();
    }
    
    /**
     * Test voucherParameters Relationship method between UseTerm Model and VoucherParameter Model
     */
    public function testVoucherParameters()
    {
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsToMany', $this->UseTerm->vouchersParmeters());
    }
    
    /**
     * Test getTransformedCollection method
     */
    public function testGetTransformedCollection( ) {
        $this->assertArrayHasKey("data", $this->UseTerm->getTransformedCollection());
    }
    
    /**
     * Test getTransformedArray method
     */
    public function testGetTransformedArray( ) {
        $this->assertArrayHasKey("data", factory(UseTerm::class)->make(['id'=>1])->getTransformedArray());
    }
    
    /**
     * Test getBeforeTransform method
     */
    public function testGetBeforeTransform( ) {
        $this->assertArrayHasKey("id", factory(UseTerm::class)->make(['id'=>1])->getBeforeTransform());
    }
    
    /**
     * Test createNewUseTerm method
     */
    public function testCreateNewUseTerm( ) {
        $raw_data = [
            "name" => "use term",
            "list_order" => "1"
        ];
        $this->assertArrayHasKey("data", $this->UseTerm->createNewUseTerm($raw_data));
    }
    
    /**
     * Test updateUseTerm
     */
    public function testUpdateUseTerm( ) {
        $raw_data = [
            "name" => "use term edited",
            "list_order" => "2"
        ];
        $this->assertArrayHasKey("data", factory(UseTerm::class)->make(['id'=>1])->updateUseTerm($raw_data));
    }
}
