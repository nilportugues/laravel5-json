<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/15/15
 * Time: 5:45 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Laravel5\JsonSerializer;

use Illuminate\Support\ServiceProvider;

class Laravel5JsonSerializerServiceProvider extends ServiceProvider
{
    const PATH = '/../../../config/json.php';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([__DIR__.self::PATH => config('json.php')]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.self::PATH, 'json_mapping');
        $this->app->singleton(\NilPortugues\Serializer\Serializer::class, function ($app) {
            return JsonSerializer::instance($app['config']->get('json_mapping'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['json_mapping'];
    }
}
