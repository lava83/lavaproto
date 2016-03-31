<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 19.01.16
 * Time: 09:53
 */

namespace Lava83\LavaProto\Core\Events;

use Lava83\LavaProto\Core\Plugins\PluginBootstrap;

abstract class Event
{

    /**
     * @var PluginBootstrap
     */
    protected $pluginBootstrap;

    public function __construct(PluginBootstrap $pluginBootstrap = null)
    {
        if ($pluginBootstrap) {
            $this->setPluginBootstrap($pluginBootstrap);
        }
    }

    public function setPluginBootstrap(PluginBootstrap $pluginBootstrap)
    {
        $this->pluginBootstrap = $pluginBootstrap;
        return $this;
    }

    public function getPluginBootstrap()
    {
        return $this->pluginBootstrap;
    }
}
