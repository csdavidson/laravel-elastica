<?php
namespace CsDavidson\LaravelElastica\ServiceProviders;

use CsDavidson\LaravelElastica\ElasticSearchConstants;
use Illuminate\Support\ServiceProvider;

/**
 * Class ElasticaServiceProvider
 * @package CsDavidson\LaravelElastica\ServiceProviders
 */
class ElasticaServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        // Bind the underlying class to Laravel's container
        $this->app->bind(ElasticSearchConstants::FACADE_ACCESSOR, \CsDavidson\LaravelElastica\ElasticaWrapper::class);

        // Merge the Facade alias into the app config
        $this->mergeConfigFrom(__DIR__.'/../config/app.aliases.php', 'app.aliases');
    }
}
