<?php

    namespace Tests\Feature\App\Http\Controllers\Auth;

    use App\Http\Controllers\Auth\SignInController;
    use App\Http\Requests\SignInFormRequest;
    use Database\Factories\UserFactory;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    use function action;
    use function bcrypt;
    use function route;
    use function str;

    class SignInControllerTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * @test
         */
        public function it_page_success(): void
        {
            $this->get(action([SignInController::class, 'page']))
                ->assertOk()
                ->assertSee('Вход в аккаунт')
                ->assertViewIs('auth.login');
        }


        /**
         * @test
         */
        public function it_handle_success(): void
        {
            $password = '12345678';

            $user = UserFactory::new()->create([
                'email' => 'test@mail.ru',
                'password' => bcrypt($password)
            ]);

            $request = SignInFormRequest::factory()->create([
                'email' => $user->email,
                'password' => $password
            ]);

            $response = $this->post(action([SignInController::class, 'handle']), $request);

            $response->assertValid()
                ->assertRedirect(route('home'));

            $this->assertAuthenticatedAs($user);
        }

        /**
         * @test
         */
        public function it_handle_fail(): void
        {
            $request = SignInFormRequest::factory()->create([
                'email' => 'notfound@mail.ru',
                'password' => str()->random(8)
            ]);

            $this->post(action([SignInController::class, 'handle']), $request)
                ->assertInvalid('email');

            $this->assertGuest();
        }

        /**
         * @test
         */

        public function it_logout_success(): void
        {
            $user = UserFactory::new()->create([
                'email' => 'test@bk.ru',
            ]);

            $this->actingAs($user)
                ->delete(action([SignInController::class, 'logOut']));

            $this->assertGuest();
        }

        /**
         * @test
         */
        public function it_logout_guest_middleware_fail(): void
        {
            $this->delete(action([SignInController::class, 'logOut']))
                ->assertRedirect(route('home'));
        }
    }
