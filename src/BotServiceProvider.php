<?php

namespace Jt\DingBot;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;

class AmapServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dingbot.php' => config_path('dingbot.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dingbot.php',
            'dingbot'
        );

        $this->registerBot();
    }

    protected function registerBot()
    {
        $this->app->bind(Bot::class, function ($app) {
            $bot = new Bot($this->getDefaultToken());

            if ($app->bound(DispatcherContract::class)) {
                $bot->setEventDispatcher(
                    $this->app[DispatcherContract::class]
                );
            }
            return $bot;
        });

        $this->app->alias('dingbot', Bot::class);
    }

    protected function getDefaultToken()
    {
        $default = config('dingbot.default');
        if ($default) {
            return config("dingbot.bots.$default.token");
        }
        return config("dingbot.bots")[0]['token'];
    }

    public function provides(): array
    {
        return ['dingbot'];
    }
}
