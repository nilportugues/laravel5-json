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

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use NilPortugues\Api\Json\JsonTransformer;
use NilPortugues\Laravel5\JsonSerializer\Mapper\Mapper;

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
        $this->mergeConfigFrom(__DIR__.self::PATH, 'jsonapi');
        $this->app->singleton(\NilPortugues\Laravel5\JsonSerializer\JsonSerializer::class, function ($app) {
                $mapping = $app['config']->get('jsonapi');
                $key = md5(json_encode($mapping));

                return Cache::rememberForever($key, function () use ($mapping) {
                    self::parseNamedRoutes($mapping);

                    return new JsonSerializer(new JsonTransformer(new Mapper($mapping)));
                });
            });
    }

    /**
     * @param array $mapping
     *
     * @return mixed
     */
    private static function parseNamedRoutes(array &$mapping)
    {
        foreach ($mapping as &$map) {
            self::parseUrls($map);
        }
    }

    /**
     * @param array $map
     */
    private static function parseUrls(&$map)
    {
        if (!empty($map['urls'])) {
            foreach ($map['urls'] as &$namedUrl) {
                $namedUrl = urldecode(route($namedUrl));
            }
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['json'];
    }
}
