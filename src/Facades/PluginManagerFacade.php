<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 11.01.16
 * Time: 16:53
 */

namespace Lava83\LavaProto\Facades;

use Illuminate\Support\Facades\Facade;
use Lava83\LavaProto\Core\Plugins\PluginManager;

class PluginManagerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PluginManager::class;
    }
}
