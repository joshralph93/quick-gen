<?php

namespace QuickGen\Providers;

use QuickGen\Commands\GeneratorCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Class GeneratorServiceProvider
 *
 * @package QuickGen\Providers
 */
class GeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerCommands();

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/quick-gen.php', 'quick-gen'
        );

        $this->publishes([
            __DIR__ . '/../../config/quick-gen.php' => config_path('quick-gen.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../resources/stubs' => resource_path('stubs'),
        ], 'stubs');
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GeneratorCommand::class
            ]);
        }
    }
}