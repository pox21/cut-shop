<?php

    namespace Tests\Feature\App\Http\Controllers\Auth;

    use App\Http\Controllers\Auth\ForgotPasswordController;
    use Database\Factories\UserFactory;
    use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Notification;
    use Tests\TestCase;

    use function action;

    class ForgotPasswordControllerTest extends TestCase
    {
        use RefreshDatabase;

        private function testingCredentials(): array
        {
            return [
                'email' => 'test@mail.ru'
            ];
        }

        /**
         * @test
         */

        public function it_page_success(): void
        {
            $this->get(action([ForgotPasswordController::class, 'page']))
                ->assertOk()
                ->assertSee('Забыли пароль')
                ->assertViewIs('auth.forgot-password');
        }

        /**
         * @test
         */
        public function it_handle_success(): void
        {
            $user = UserFactory::new()->create($this->testingCredentials());

            $this->post(action([ForgotPasswordController::class, 'handle']), $this->testingCredentials())
                ->assertRedirect();

            Notification::assertSentTo($user, ResetPasswordNotification::class);
        }

        /**
         * @test
         */
        public function it_handle_fail(): void
        {
            $this->assertDatabaseMissing('users', $this->testingCredentials());

            $this->post(action([ForgotPasswordController::class, 'handle']), $this->testingCredentials())
                ->assertInvalid(['email']);

            Notification::assertNothingSent();
        }

    }
