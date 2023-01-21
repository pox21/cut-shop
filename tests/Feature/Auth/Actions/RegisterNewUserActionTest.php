<?php

    namespace Tests\Feature\Auth\Actions;

    use Domain\Auth\Contracts\RegisterNewUserContract;
    use Domain\Auth\DTOs\NewUserDTO;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class RegisterNewUserActionTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * @test
         */
        public function it_success_user_created(): void
        {
            $this->assertDatabaseMissing('users', [
                'email' => 'test@mail.ru'
            ]);

            $action = app(RegisterNewUserContract::class);

            $action(NewUserDTO::make('test', 'test@mail.ru', '12345678'));

            $this->assertDatabaseHas('users', [
                'email' => 'test@mail.ru'
            ]);
        }
    }
