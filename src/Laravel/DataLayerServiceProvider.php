<?php
namespace Straylightagency\DataLayer\Laravel;

use Straylightagency\DataLayer\DataLayerManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider.
 * Register the DataLayer helper class as a singleton into Laravel
 *
 * @package Straylightagency\DataLayer
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class DataLayerServiceProvider extends BaseServiceProvider
{
    /**
     * Register the DataLayer
     */
    public function register():void
    {
        $this->app->singleton('datalayer', fn ( $app ) =>
            DataLayerManager::newUsingLaravelSession( $app['session'], config('datalayer.gtm_id') )
        );
    }

    /**
     * Bootstrap
     */
    public function boot(): void
    {
        $this->publishes( [
            __DIR__ . '/config.php' => config_path('datalayer.php'),
        ] );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides():array
    {
        return [ DataLayerManager::class ];
    }
}
