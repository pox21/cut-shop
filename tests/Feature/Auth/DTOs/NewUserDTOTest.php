<?php

    namespace Tests\Feature\Auth\DTOs;

    use App\Http\Requests\SignUpFormRequest;
    use Domain\Auth\DTOs\NewUserDTO;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class NewUserDTOTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * @test
         */
        public function it_instance_created_from_form_request(): void
        {
            $dto = NewUserDTO::fromRequest(
                new SignUpFormRequest([
                    'name' => 'test',
                    'email' => 'test@mail.ru',
                    'password' => '12345',
                ])
            );

            $this->assertInstanceOf(NewUserDTO::class, $dto);
        }
    }
