<?php
namespace App;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends \TestCase
{
    /**
     *
     * @var \App\User
     */
    private $UserModel;

    public function setUp( ) {
        parent::setUp();
        $this->UserModel = new \App\User();
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchUserByName()
    {
        $result = $this->UserModel->searchUserByName('monica');
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey("id", $result["data"][0]);
    }
}
