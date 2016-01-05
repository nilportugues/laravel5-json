<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/15/15
 * Time: 5:45 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Laravel5\Json;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use NilPortugues\Laravel5\Json\Providers\Laravel51Provider;
use NilPortugues\Laravel5\Json\Providers\Laravel52Provider;

class Laravel5JsonServiceProvider extends ServiceProvider
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
        $this->mergeConfigFrom(__DIR__.self::PATH, 'json');

        $version = Application::VERSION;

        switch ($version) {
            case false !== strpos($version, '5.0.'):
            case false !== strpos($version, '5.1.'):
                $provider = new Laravel51Provider();
                break;
            case false !== strpos($version, '5.2.'):
                $provider = new Laravel52Provider();
                break;
            default:
                throw new \RuntimeException(
                    sprintf('Laravel version %s is not supported. Please use the 5.1 for the time being', $version)
                );
                break;
        }

        $this->app->singleton(JsonSerializer::class, $provider->provider());
    }
}
