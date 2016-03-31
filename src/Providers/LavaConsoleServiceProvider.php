<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 14:10
 */

namespace Lava83\LavaProto\Providers;


use Illuminate\Support\ServiceProvider;
use Lava83\LavaProto\Console\Commands\CreateEntity;
use Lava83\LavaProto\Console\Commands\CreatePresenter;
use Lava83\LavaProto\Console\Commands\CreateRepository;
use Lava83\LavaProto\Console\Commands\CreateTransformer;
use Lava83\LavaProto\Console\Commands\PluginActivate;
use Lava83\LavaProto\Console\Commands\PluginDeactivate;
use Lava83\LavaProto\Console\Commands\PluginDeinstall;
use Lava83\LavaProto\Console\Commands\PluginInstall;
use Lava83\LavaProto\Console\Commands\PluginList;
use Lava83\LavaProto\Core\Plugins\PluginManager;

/**
 * Class LavaConsoleServiceProvider
 * @package Lava83\LavaProto\Providers
 */
class LavaConsoleServiceProvider extends ServiceProvider
{

    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @see _registerCommands
     */
    public function boot(){
        //to register commands
        $this->_registerCommands();
    }

    public function register(){}


    protected function _registerCommands(){
        // list plugins
        $this->app->singleton('command.lava83.lavaproto.plugins.list', function () {
            return new PluginList();
        });

        //activate plugin
        $this->app->singleton('command.lava83.lavaproto.plugins.activate', function () {
            return new PluginActivate(new PluginManager(config('lava83-plugin-manager.path'), config('lava83-plugin-manager.namespaces')));
        });

        //deactivate plugin
        $this->app->singleton('command.lava83.lavaproto.plugins.deactivate', function () {
            return new PluginDeactivate(new PluginManager(config('lava83-plugin-manager.path'), config('lava83-plugin-manager.namespaces')));
        });

        //install plugin
        $this->app->singleton('command.lava83.lavaproto.plugins.install', function () {
            return new PluginInstall(new PluginManager(config('lava83-plugin-manager.path'), config('lava83-plugin-manager.namespaces')));
        });

        //deinstall plugin
        $this->app->singleton('command.lava83.lavaproto.plugins.deinstall', function () {
            return new PluginDeinstall(new PluginManager(config('lava83-plugin-manager.path'), config('lava83-plugin-manager.namespaces')));
        });

        $this->app->singleton('command.lava83.lavaproto.make.repository', function () {
            return new CreateRepository();
        });

        $this->app->singleton('command.lava83.lavaproto.make.entity', function () {
            return new CreateEntity();
        });

        $this->app->singleton('command.lava83.lavaproto.make.presenter', function () {
            return new CreatePresenter();
        });

        $this->app->singleton('command.lava83.lavaproto.make.transformer', function () {
            return new CreateTransformer();
        });


        $this->commands([
            'command.lava83.lavaproto.plugins.list',
            'command.lava83.lavaproto.plugins.activate',
            'command.lava83.lavaproto.plugins.deactivate',
            'command.lava83.lavaproto.plugins.install',
            'command.lava83.lavaproto.plugins.deinstall',
            'command.lava83.lavaproto.make.repository',
            'command.lava83.lavaproto.make.entity',
            'command.lava83.lavaproto.make.presenter',
            'command.lava83.lavaproto.make.transformer'
        ]);
    }
}