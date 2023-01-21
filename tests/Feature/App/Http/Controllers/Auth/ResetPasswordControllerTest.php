<?php

    namespace Tests\Feature\App\Http\Controllers\Auth;

    use App\Http\Controllers\Auth\ResetPasswordController;
    use App\Http\Controllers\Auth\SignInController;
    use Database\Factories\UserFactory;
    use Domain\Auth\Models\User;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Password;
    use Tests\TestCase;

    use function action;

    class ResetPasswordControllerTest extends TestCase
    {
        use RefreshDatabase;

        private string $token;

        private User $user;

        protected function setUp(): void
        {
            parent::setUp();

            $this->user = UserFactory::new()->create();
            $this->token = Password::createToken($this->user);
        }

        /**
         * @test
         */
        public function it_page_success(): void
        {
            $this->get(action([ResetPasswordController::class, 'page'], ['token' => $this->token]))
                ->assertOk()
                ->assertViewIs('auth.reset-password');
        }

        /**
         * @test
         */
        public function it_handle(): void
        {
            $password = '12345678';
            $password_confirmation = '12345678';

            Password::shouldReceive('reset')
                ->once()
                ->withSomeOfArgs([
                    'email' => $this->user->email,
                    'password' => $password,
                    'password_confirmation' => $password_confirmation,
                    'token' => $this->token
                ])
                ->andReturn(Password::PASSWORD_RESET);

            $response = $this->post(action([ResetPasswordController::class, 'handle']), [
                'email' => $this->user->email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
                'token' => $this->token
            ]);

            $response->assertRedirect(action([SignInController::class, 'page']));
        }
    }