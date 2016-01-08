<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 07.01.16
 * Time: 11:29
 */

namespace Lava83\LavaProto;

use Illuminate\Filesystem\Filesystem;
use Lava83\LavaProto\Exceptions\LogicException;
use Lava83\LavaProto\View\FileViewFinder;
use Lava83\LavaProto\View\View;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;
use Ytake\LaravelSmarty\SmartyCompileServiceProvider;
use Ytake\LaravelSmarty\SmartyConsoleServiceProvider;
use Ytake\LaravelSmarty\SmartyServiceProvider;

class LavaProtoServiceProvider extends ServiceProvider
{
    /**
     * Boot the framework services
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the framework services
     *
     * @throws LogicException
     * @return void
     */
    public function register()
    {

        if(in_array(config('cache.default'), ['file', 'database'])) {
            throw new LogicException('We use tagable caches. PLease dont use file or database as driver!');
        }
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