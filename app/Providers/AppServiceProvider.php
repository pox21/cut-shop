<?php

    namespace App\Providers;

    use App\Http\Kernel;
    use Carbon\CarbonInterval;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\ServiceProvider;
    use Services\Telegram\TelegramBotApi;
    use Services\Telegram\TelegramBotApiContract;

    class AppServiceProvider extends ServiceProvider
    {

        public function boot(): void
        {
            Model::shouldBeStrict(!app()->isProduction());

            $this->app->bind(TelegramBotApiContract::class, TelegramBotApi::class);

            if (app()->isProduction()) {
                DB::listen(function ($query) {
                    if ($query->time > 100) {
                        logger()
                            ->channel('telegram')
                            ->debug('query longer than 1s: ' . $query->sql, $query->bindings);
                    }
                });


                $kernel = app(Kernel::class);
                $kernel->whenRequestLifecycleIsLongerThan(
                    CarbonInterval::seconds(4),
                    function () {
                        logger()
                            ->channel('telegram')
                            ->debug('whenQueryingForLongerThan: ' . request()->url());
                    }
                );
            }
        }
    }
