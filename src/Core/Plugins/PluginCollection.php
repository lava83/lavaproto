<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 14.01.16
 * Time: 15:06
 */

namespace Lava83\LavaProto\Core\Plugins;
use Lava83\LavaProto\Exceptions\PluginManagerException;


/**
 * Class PluginCollection
 * @package Lava83\LavaProto\Core\Plugins
 */
class PluginCollection implements \IteratorAggregate
{

    /**
     * @var \ArrayObject
     */
    protected $_plugins;

    /**
     * initialize the plugin collection
     */
    public function __construct() {
        $this->_plugins = new \ArrayObject();
    }

    /**
     *
     * get's the plugin list
     *
     * @return \ArrayObject
     */
    public function getIterator()
    {
        return $this->_plugins;
    }

    /**
     *
     * register a plugin and register this collection to the
     * given plugin
     *
     * @param PluginBootstrap $plugin
     * @return PluginManager
     */
    public function registerPlugin(PluginBootstrap $plugin)
    {
        $plugin->setCollection($this);
        $this->_plugins[$plugin->getName()] = $plugin;
        return $this;
    }

    /**
     * @param $name
     * @param bool|false $throw_exception
     * @return PluginBootstrap|null
     * @throws PluginManagerException
     */
    public function get($name, $throw_exception = false) {
        if (!$this->_plugins->offsetExists($name)) {
            $this->load($name, $throw_exception);
        }
        if ($this->_plugins->offsetExists($name)) {
            return $this->_plugins->offsetGet($name);
        } else {
            return null;
        }
    }

    public function load($name, $throw_exception = true)
    {
        if ($throw_exception && !$this->_plugins->offsetExists($name)) {
            throw new PluginManagerException(sprintf("Plugin %s not found failure", $name));
        }
        return $this;
    }

    public function __call($name, $args = null)
    {
        return $this->get($name, true);
    }

    public function reset()
    {
        $this->_plugins->exchangeArray(array());
        return $this;
    }

    public function count() {
        return $this->_plugins->count();
    }

}