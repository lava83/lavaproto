<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 14.01.16
 * Time: 10:29
 */

namespace Lava83\LavaProto\Core\Plugins;

use Carbon\Carbon;
use Lava83\LavaProto\Models\Plugin;
use Lava83\LavaProto\Models\PluginSubscribe;

/**
 * Class PluginBootstrap
 * @package Lava83\LavaProto\Core\Plugins
 */
class PluginBootstrap
{

    /**
     * @var string
     */
    protected $version = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $info = [];

    /**
     * @var Plugin
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $plugin_config_key;


    /**
     * @var string
     */
    protected $plugin_config_key_prefix;

    /**
     * @var PluginCollection
     */
    protected $pluginCollection;

    /**
     * @var string
     */
    protected $namespace;

    /**
     *
     * the events to call with this plugin
     *
     * @var array
     */
    protected $subscribes = null;
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $app;

    /**
     *
     * the constructor
     *
     * @param $name
     * @param null $info
     */
    public function __construct($name, $info = null)
    {

        $this->app = app();
        $this->name = $name;
        $this->namespace = substr(get_class($this), 0, strrpos(get_class($this), '\\'));

        $namespace_arr = explode("\\", $this->namespace);
        $this->source = $namespace_arr[count($namespace_arr) - 2];

        $this->plugin_config_key = 'plugin.' . strtolower($name);
        $this->plugin_config_key_prefix = $this->plugin_config_key . '.';
        config($this->getInfo());
        config([$this->plugin_config_key_prefix . 'capabilities' => $this->getCapabilities()]);
        if (is_array($info)) {
            $this->addInfos($info);
        }
    }

    /**
     *
     * returns the info of the plugin
     *
     * @see getVersion
     * @see getLabel
     *
     * @return array
     */
    public function getInfo()
    {
        return [
            $this->plugin_config_key_prefix . 'version' => $this->getVersion(),
            $this->plugin_config_key_prefix . 'label' => $this->getLabel()
        ];
    }

    /**
     *
     * returns the version of the plugin
     *
     * @return null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * get the label of plugin if is set
     * else where the name of the plugin
     *
     * @see getLabel
     *
     * @return string
     */
    public function getLabel()
    {
        return isset($this->info->label) ? $this->info->label : $this->getName();
    }

    /**
     *
     * get the name of plugin
     *
     * @return string
     */
    final public function getName()
    {
        return $this->name;
    }

    /**
     *
     * returns the cpabilities of the plugin
     *
     * @return array
     */
    public function getCapabilities()
    {
        return array(
            'install' => true,
            'enable' => true
        );
    }

    /**
     *
     * adds many infos to the plugin configuration
     *
     * @param array $info
     */
    public function addInfos(array $info = [])
    {
        foreach ($info as $key => $value) {
            $this->addInfo($key, $value);
        }
    }

    /**
     *
     * add's a info to the configuration
     *
     * @param $key
     * @param $value
     */
    public function addInfo($key, $value)
    {
        config([$this->plugin_config_key_prefix . $key => $value]);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     *
     * get the plugin collection with all plugins
     *
     * @return PluginCollection
     */
    public function getPluginCollection()
    {
        return $this->pluginCollection;
    }

    /**
     *
     * set the plugin collection
     *
     * @param PluginCollection $collection
     * @return PluginBootstrap
     */
    public function setPluginCollection(PluginCollection $collection)
    {
        $this->pluginCollection = $collection;
        return $this;
    }

    /**
     *
     * returns the complete plugin info array
     *
     * @return array
     */
    public function getPluginInfo()
    {

        $arr = config($this->plugin_config_key);
        $arr = array_merge($arr, [
            'class' => get_called_class()
        ]);
        return $arr;
        //return config($this->_plugin_config_key);
    }


    /**
     * deactivate the plugin in database
     */
    public function deactivate()
    {
        $this->model->fill([
            'active' => false,
            'activation_date' => null
        ]);
        $this->model->save();
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->model->installed;
    }

    /**
     * activate the plugin in database
     */
    public function activate()
    {
        $this->model->fill([
            'active' => true,
            'activation_date' => new Carbon
        ]);
        $this->model->save();
    }

    /**
     * install the plugin in database
     */
    public function install()
    {
        $this->model->fill([
            'installed' => true,
            'installation_date' => new Carbon
        ]);
        $this->model->save();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->model->active;
    }

    /**
     * deinstall the plugin in database
     */
    public function deinstall()
    {

        /** @var $subsc PluginSubscribe */

        $this->model->fill([
            'installed' => false,
            'installation_date' => null
        ]);
        $this->model->save();
        if ($subscribes = $this->model->subscribes()->get()) {
            foreach ($subscribes as $subsc) {
                $subsc->delete();
            }
        }
    }

    /**
     * @return Plugin
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Plugin $model
     */
    public function setModel(Plugin $model)
    {
        $this->model = $model;
        return $this;
    }

    public function subscribeEvent($event, $listener, $position = null)
    {
        $plugin_subscribe_data = [
            'subscribe' => $event,
            'listener' => $listener,
            'position' => (is_null($position)) ? 0 : $position
        ];

        $this->model->subscribes()->create($plugin_subscribe_data);
    }

    public function getSubscribes()
    {
        if ($this->subscribes == null) {
            $this->subscribes = $this->model->subscribes()->get();
        }
        return $this->subscribes;
    }

    /**
     * Magic caller
     *
     * @param   string $name
     * @param   array $args
     */
    public function __call($name, $args = null)
    {
        if (strpos($name, 'get') === 0) {
            $name = strtolower(substr($name, 3));
        }
        $info = $this->getInfo();
        $key = $this->plugin_config_key_prefix . $name;
        if (isset($info[$key])) {
            return $info[$key];
        }
        return null;
    }

    /**
     *
     * gets the plugin path
     *
     * @return string
     */
    public function getPath()
    {
        if (empty($this->path)) {
            $ret = '';
            $reflection = new \ReflectionClass($this);

            if ($fileName = $reflection->getFileName()) {
                $ret = dirname($fileName) . DIRECTORY_SEPARATOR;
            }

            $this->path = $ret;
        }

        return $this->path;
    }

    public function addTemplatePath($path, $namespace = null)
    {
        $this->app['view']->prependLocation($path);
        if ($namespace == null) {
            $namespace = $this->getName();
        }
        $this->app['lava83.twig.loader.filesystem']->addPath($path, $namespace);
    }
}
