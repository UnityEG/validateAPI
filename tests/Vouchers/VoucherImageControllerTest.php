<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\VoucherImagesController;

class VoucherImageControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $voucher_image_transformer = new \App\aaa\Transformers\VoucherImageTransformer();
        $voucher_image_controller = new VoucherImagesController(
                 $voucher_image_transformer
                );
        $result = $voucher_image_controller->index();
        $this->assertInstanceOf( 'Illuminate\Http\JsonResponse', $result );
    }
}
