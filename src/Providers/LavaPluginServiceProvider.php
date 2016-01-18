<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 12:12
 */

namespace Lava83\LavaProto\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Lava83\LavaProto\Core\Plugins\PluginManager;
use Lava83\LavaProto\Facades\PluginManagerFacade;
use Lava83\LavaProto\Filesystem\Filesystem;

class LavaPluginServiceProvider extends ServiceProvider
{

    /**
     * Boot the framework services
     *
     * @return void
     */
    public function boot() {
        $this->_publishConfigs();
        $this->_publishMigrations();
    }

    public function register()
    {
        $this->_registerFacades();
        $this->_extendFileSystem();
    }

    /**
     * publishes all config files for this service provider
     */
    protected function _publishConfigs() {
        $this->publishes([
            __DIR__.'/../../config/lava83-plugin-manager.php' => config_path('lava83-plugin-manager.php'),
        ]);
    }

    /**
     * publishes all migrations for this service provider
     */
    protected function _publishMigrations() {
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations')
        ]);
    }

    protected function _registerFacades() {
        $this->app[PluginManager::class] = $this->app->share(function(){
            return new PluginManager(config('lava83-plugin-manager.path'), config('lava83-plugin-manager.namespaces'));
        });
        $this->app->booting(function () {
            $oLoader = AliasLoader::getInstance();
            $oLoader->alias('PluginManager', PluginManagerFacade::class);
        });
    }

    /**
     * set our class Lava83\LavaProto\Filesystem\Filesystem as default Filesystem
     *
     * @see Lava83\LavaProto\Filesystem\Filesystem
     */
    protected function _extendFileSystem() {

        $this->app->singleton('files', function () {
            return new Filesystem;
        });


    }

}