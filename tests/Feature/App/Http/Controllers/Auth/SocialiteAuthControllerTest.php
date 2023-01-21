<?php

    namespace Tests\Feature\App\Http\Controllers\Auth;

    use App\Http\Controllers\Auth\SocialAuthController;
    use Database\Factories\UserFactory;
    use DomainException;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Testing\TestResponse;
    use Laravel\Socialite\Contracts\User as SocialiteUser;
    use Laravel\Socialite\Facades\Socialite;
    use Mockery\MockInterface;
    use Tests\TestCase;

    use function action;
    use function route;
    use function str;

    class SocialiteAuthControllerTest extends TestCase
    {
        use RefreshDatabase;

        private function mockSocialiteCallback(string|int $githubId): MockInterface
        {
            $user = $this->mock(SocialiteUser::class, function (MockInterface $m) use ($githubId) {
                $m->shouldReceive('getId')
                    ->once()
                    ->andReturn($githubId);

                $m->shouldReceive('getName')
                    ->once()
                    ->andReturn(str()->random(8));

                $m->shouldReceive('getEmail')
                    ->once()
                    ->andReturn('test@mail.ru');
            });

            Socialite::shouldReceive('driver->user')
                ->once()
                ->andReturn($user);

            return $user;
        }

        private function callbackRequest(): TestResponse
        {
            return $this->get(
                action(
                    [SocialAuthController::class, 'callback'],
                    ['driver' => 'github']
                )
            );
        }

        /**
         * @test
         */
        public function it_github_callback_create_user_success(): void
        {
            $githubId = str()->random(10);

            $this->assertDatabaseMissing('users', [
                'github_id' => $githubId
            ]);

            $this->mockSocialiteCallback($githubId);

            $this->callbackRequest()
                ->assertRedirect(route('home'));

            $this->assertAuthenticated();

            $this->assertDatabaseHas('users', [
                'github_id' => $githubId
            ]);
        }

        /**
         * @test
         */
        public function it_driver_not_found_exception(): void
        {
            $this->expectException(DomainException::class);

            $this
                ->withoutExceptionHandling()
                ->get(
                    action(
                        [SocialAuthController::class, 'redirect'],
                        ['driver' => 'vk']
                    )
                );

            $this
                ->withoutExceptionHandling()
                ->get(
                    action(
                        [SocialAuthController::class, 'callback'],
                        ['driver' => 'vk']
                    )
                );
        }

        /**
         * @test
         */
        public function it_authenticated_by_existing_user(): void
        {
            $githubId = str()->random(10);

            UserFactory::new()->create([
                'github_id' => $githubId
            ]);

            $this->assertDatabaseHas('users', [
                'github_id' => $githubId
            ]);

            $this->mockSocialiteCallback($githubId);

            $this->callbackRequest()
                ->assertRedirect(route('home'));

            $this->assertAuthenticated();
        }
    }
