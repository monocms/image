<?php

namespace HiCMS\Image\Providers;

use HiCMS\Image\ImageManager;
use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootstrapImageCache();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        // merge default config
        $this->mergeConfigFrom( __DIR__.'/../../config/imagecache.php', 'imagecache' );

        // create image
        $app['image'] = $app->share(function ($app) {
            return new ImageManager($app['config']->get('imagecache'));
        });

        $app->alias('image', 'HiCMS\Image\ImageManager');
    }

    /**
     * Bootstrap imagecache
     *
     * @return void
     */
    private function bootstrapImageCache()
    {
        $app = $this->app;
        $config = __DIR__.'/../../config/imagecache.php';

        $this->publishes(array(
            $config => config_path('imagecache.php')
        ));

        // merge default config
        $this->mergeConfigFrom(
            $config,
            'imagecache'
        );

        // imagecache route
        if (is_string(config('imagecache.route'))) {

            $filename_pattern = '[ \w\\.\\/\\-\\@]+';

            $app['router']->get(config('imagecache.route').'/{size}/{filename}', array(
                'uses' => 'HiCMS\Image\ImageCacheController@getResponse',
                'as' => config('imagecache.as')
            ))->where(array('filename' => $filename_pattern));
        }
    }
}
