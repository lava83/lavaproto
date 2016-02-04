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
use Lava83\LavaProto\Core\Plugins\PluginBootstrap;
use Lava83\LavaProto\Core\Plugins\PluginManager;
use Lava83\LavaProto\Exceptions\PluginManagerException;
use Lava83\LavaProto\Facades\PluginManagerFacade;
use Lava83\LavaProto\Filesystem\Filesystem;

class LavaPluginServiceProvider extends ServiceProvider
{

    /**
     * @var PluginManager
     */
    protected $_pluginmanager;

    /**
     * Boot the framework services
     *
     * @return void
     */
    public function boot(PluginManager $pluginManager) {
        /** @var $plugin PluginBootstrap */

        $this->_pluginmanager = $pluginManager;
        $this->_publishConfigs();
        $this->_publishMigrations();
        $this->_listenOnEvents();

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

    /**
     * iterate the plugins in collection
     */
    protected function _listenOnEvents()
    {
        foreach ($this->_pluginmanager->getCollection() as $plugin) {
            $this->_preparePlugin($plugin);
        }
    }

    /**
     *
     * prepare the plugin if is active and subscribe a listener
     *
     * @param $plugin PluginBootstrap
     * @throws PluginManagerException
     */
    protected function _preparePlugin($plugin)
    {
        if ($plugin->isActive()) {
            $subscribes = $plugin->getSubscribes();
            if (count($subscribes) != 0) {
                foreach ($subscribes as $event) {
                    //event class is given
                    if (strpos($event->listener, '@')) {
                        $this->_listenOnEventClass($event, $plugin);
                    } else {
                        //event is listen on bootstrap file
                        $this->_listenOnBootstrapClass($plugin, $event);
                    }
                }
            }
        }
    }

    /**
     * @param $event
     * @throws PluginManagerException
     */
    protected function _listenOnEventClass($event, PluginBootstrap $plugin = null)
    {
        list($cls_name, $method_name) = explode('@', $event->listener);
        if ($cls_name && $method_name && class_exists($cls_name)) {
            $object = new $cls_name($plugin);
            if (method_exists($object, $method_name)) {
                //$object->{$method_name}();
                \Event::listen($event->subscribe, [$object, $method_name]);
            } else {
                throw new PluginManagerException(sprintf("Method: '%s' doesnt exists.", $method_name));
            }
        } elseif (!class_exists($cls_name)) {
            throw new PluginManagerException(sprintf("Class: '%s' doesnt exists.", $cls_name));
        }
    }

    /**
     * @param $plugin
     * @param $event
     */
    protected function _listenOnBootstrapClass($plugin, $event)
    {
        \Event::listen($event->subscribe, [$plugin, $event->listener]);
    }

}