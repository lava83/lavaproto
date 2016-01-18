<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 12:03
 */

namespace Lava83\LavaProto\Providers;


use Illuminate\Support\ServiceProvider;
use Ytake\LaravelSmarty\SmartyCompileServiceProvider;
use Ytake\LaravelSmarty\SmartyConsoleServiceProvider;
use Ytake\LaravelSmarty\SmartyServiceProvider;
use Lava83\LavaProto\View\FileViewFinder;
use Lava83\LavaProto\View\View;

class LavaSmartyServiceProvider extends ServiceProvider
{




    public function register()
    {
        $this->_registerSmarty();
        $this->_extendViewFactory();
    }

    /**
     * Register smarty providers
     *
     * @return void
     */
    protected function _registerSmarty() {

        $smarty_plugins_paths = config('ytake-laravel-smarty.plugins_paths');
        $smarty_plugins_paths[] = __DIR__ . '/../smarty/plugins';
        \Config::set('ytake-laravel-smarty.plugins_paths', $smarty_plugins_paths);

        $smarty_configs_paths = config('ytake-laravel-smarty.config_paths');
        $smarty_configs_paths[] = __DIR__ . '/../smarty/config';
        \Config::set('ytake-laravel-smarty.config_paths', $smarty_configs_paths);

        $this->app->register(SmartyServiceProvider::class);
        $this->app->register(SmartyConsoleServiceProvider::class);
        if($this->app->environment() !== 'local') {
            $this->app->register(SmartyCompileServiceProvider::class);
        }
    }

    /**
     * set our class Lava83\LavaProto\View\FileViewFinder as default FileViewFinder
     * and set Lava83\LavaProto\View\View as default View
     *
     * @see Lava83\LavaProto\View\View
     * @see Lava83\LavaProto\View\FileViewFinder
     */
    protected function _extendViewFactory() {

        /**
         * @var \Illuminate\Contracts\Foundation\Application
         */
        $app = $this->app;

        /**
         * FileViewFinder
         */
        $app->extend('view.finder', function() use ($app) {
            $paths = $app['config']['view.paths'];

            return new FileViewFinder($app['files'], $paths);
        });

        /**
         * View Factory
         */
        $app->extend('view', function() use ($app) {
            $resolver = $app['view.engine.resolver'];
            $finder = $app['view.finder'];
            $env = new View($resolver, $finder, $app['events']);
            $env->setContainer($app);
            $env->share('app', $app);
            return $env;
        });
    }
}