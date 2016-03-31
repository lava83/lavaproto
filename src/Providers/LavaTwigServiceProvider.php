<?php
/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 03.02.16
 * Time: 18:52
 */

namespace Lava83\LavaProto\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Lava83\LavaProto\Core\Twig\Loader\Filesystem;
use Lava83\LavaProto\Core\Twig\TokenParser\ExtendsParent;
use Lava83\LavaProto\View\FileViewFinder;
use Lava83\LavaProto\View\View;
use TwigBridge\Facade\Twig;

class LavaTwigServiceProvider extends ServiceProvider
{

    protected $aliases = [
        'Twig' => Twig::class,
    ];

    public function register()
    {
        $this->registerViewFactory();
        $this->registerTwigBridge();
        $this->registerTwigExtensions();


    }

    protected function registerTwigExtensions()
    {
        \Twig::addTokenParser(new ExtendsParent());
        $this->app->bindIf('lava83.twig.loader.filesystem', function () {
            return new Filesystem($this->app['config']['view.paths']);
        }, true);
        \Twig::getLoader()->addLoader($this->app['lava83.twig.loader.filesystem']);

    }

    /**
     * set our class Lava83\LavaProto\View\FileViewFinder as default FileViewFinder
     * and set Lava83\LavaProto\View\View as default View
     *
     * @see Lava83\LavaProto\View\View
     * @see Lava83\LavaProto\View\FileViewFinder
     */
    protected function registerViewFactory()
    {

        /**
         * @var \Illuminate\Contracts\Foundation\Application
         */
        $app = $this->app;

        /**
         * FileViewFinder
         */
        $app->extend('view.finder', function () use ($app) {
            $paths = $app['config']['view.paths'];

            return new FileViewFinder($app['files'], $paths);
        });

        /**
         * View Factory
         */
        $app->extend('view', function () use ($app) {
            $resolver = $app['view.engine.resolver'];
            $finder = $app['view.finder'];
            $env = new View($resolver, $finder, $app['events']);
            $env->setContainer($app);
            $env->share('app', $app);
            return $env;
        });
    }

    public function registerTwigBridge()
    {
        $this->app->register(\TwigBridge\ServiceProvider::class);
        AliasLoader::getInstance($this->aliases);
    }
}
