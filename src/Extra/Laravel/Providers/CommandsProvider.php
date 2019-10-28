<?php
/**
 * Author: 沧澜
 * Date: 2019-10-25
 */

namespace CalJect\Productivity\Extra\Laravel\Providers;

use CalJect\Productivity\Extra\Laravel\Consoles\Commands\EnvConfigCommand;
use CalJect\Productivity\Extra\Laravel\Consoles\Commands\ModelCommentCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Class CommandsProvider
 * @package CalJect\Productivity\Extra\Laravel\Providers
 */
class CommandsProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelCommentCommand::class,
                EnvConfigCommand::class,
            ]);
        }
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    
    
    }
    
}