<?php

    namespace Tests\Feature\App\Http\Controllers;

    use App\Http\Controllers\ProductController;
    use Database\Factories\ProductFactory;
    use Tests\TestCase;

    class ProductControllerTest extends TestCase
    {
        /**
         * @test
         */
        public function it_success_response(): void
        {
            $product = ProductFactory::new()->createOne();
            $this->get(action(ProductController::class, $product))
                ->assertOk();
        }


    }
