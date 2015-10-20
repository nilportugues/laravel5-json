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
use NilPortugues\Api\Mapping\Mapping;
use NilPortugues\Laravel5\JsonSerializer\Mapper\Mapper;
use ReflectionClass;

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
                    return new JsonSerializer(new JsonTransformer(self::parseRoutes(new Mapper($mapping))));
                });
            });
    }

    /**
     * @param Mapper $mapper
     *
     * @return Mapper
     */
    private static function parseRoutes(Mapper $mapper)
    {
        foreach ($mapper->getClassMap() as &$mapping) {

            $mappingClass = new \ReflectionClass($mapping);

            self::setUrlWithReflection($mapping, $mappingClass, 'resourceUrlPattern');
            self::setUrlWithReflection($mapping, $mappingClass, 'selfUrl');
            $mappingProperty = $mappingClass->getProperty('otherUrls');
            $mappingProperty->setAccessible(true);

            $otherUrls = (array) $mappingProperty->getValue($mapping);
            if (!empty($otherUrls)) {
                foreach ($otherUrls as &$url) {
                    $url = urldecode(route($url));
                }
            }
            $mappingProperty->setValue($mapping, $otherUrls);
        }

        return $mapper;
    }

    /**
     * @param Mapping         $mapping
     * @param ReflectionClass $mappingClass
     * @param string          $property
     */
    private static function setUrlWithReflection(Mapping $mapping, ReflectionClass $mappingClass, $property)
    {
        $mappingProperty = $mappingClass->getProperty($property);
        $mappingProperty->setAccessible(true);
        $value = $mappingProperty->getValue($mapping);

        if (!empty($value)) {
            $value = urldecode(route($value));
            $mappingProperty->setValue($mapping, $value);
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
