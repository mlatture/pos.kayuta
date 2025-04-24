<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('image', function() {
            return new class {
                public function resize($file, $width = 800, $height = 600)
                {
                    $imagine = new Imagine();
                    $image = $imagine->open($file);
                    $image->resize(new Box(800, 600));
                    
                    return $image;
                }

                public function save($image, $path)
                {
                    $image->save($path);
                }
            };
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
