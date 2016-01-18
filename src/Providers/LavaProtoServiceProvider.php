<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 07.01.16
 * Time: 11:29
 */

namespace Lava83\LavaProto\Providers;

use Illuminate\Support\ServiceProvider;
use Lava83\LavaProto\Exceptions\LogicException;

class LavaProtoServiceProvider extends ServiceProvider
{
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
        $this->app->register(LavaSmartyServiceProvider::class);
        $this->app->register(LavaPluginServiceProvider::class);
        $this->app->register(LavaConsoleServiceProvider::class);
    }


}