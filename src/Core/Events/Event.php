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
    protected $_plugin;

    public function __construct(PluginBootstrap $pluginBootstrap = null) {
        if($pluginBootstrap) {
            $this->setPlugin($pluginBootstrap);
        }
    }

    public function setPlugin(PluginBootstrap $pluginBootstrap) {
        $this->_plugin = $pluginBootstrap;
        return $this;
    }

    public function getPlugin() {
        return $this->_plugin;
    }

}