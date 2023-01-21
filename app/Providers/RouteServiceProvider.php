<?php

    namespace App\Providers;

    use App\Routing\AppRegistrar;
    use App\Routing\AuthRegistrar;
    use App\Routing\CatalogRegistrar;
    use App\Routing\ProductRegistrar;
    use http\Exception\RuntimeException;
    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Contracts\Routing\Registrar;
    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\RateLimiter;
    use Symfony\Component\HttpFoundation\Response;

    class RouteServiceProvider extends ServiceProvider
    {
        /**
         * The path to the "home" route for your application.
         *
         * Typically, users are redirected here after authentication.
         *
         * @var string
         */
        public const HOME = '/';

        protected array $registrars = [
            AppRegistrar::class,
            AuthRegistrar::class,
            CatalogRegistrar::class,
            ProductRegistrar::class
        ];

        public function boot(): void
        {
            $this->configureRateLimiting();

            $this->routes(function (Registrar $router) {
                $this->mapRoutes($router, $this->registrars);
            });

//            $this->routes(function () {
//                Route::middleware('api')
//                    ->prefix('api')
//                    ->group(base_path('routes/api.php'));
//
//                Route::middleware('web')
//                    ->group(base_path('routes/web.php'));
//            });
        }

        protected function configureRateLimiting(): void
        {
            RateLimiter::for('global', function (Request $request) {
                return Limit::perMinute(500)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response('take it easy', Response::HTTP_TOO_MANY_REQUESTS, $headers);
                    });
            });

            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('auth', function (Request $request) {
                return Limit::perMinute(20)->by($request->ip());
            });
        }

        protected function mapRoutes(Registrar $router, array $registrars): void
        {
            foreach ($registrars as $registrar) {
                if (!class_exists($registrar)) {
                    throw new RuntimeException(
                        sprintf(
                            'Cannot map routes \'%s\', it is not a valid routes class',
                            $registrar
                        )
                    );
                }

                (new $registrar)->map($router);
            }
        }
    }
