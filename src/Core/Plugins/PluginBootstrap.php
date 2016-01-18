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

/**
 * Class PluginBootstrap
 * @package Lava83\LavaProto\Core\Plugins
 */
class PluginBootstrap
{

    /**
     * @var string
     */
    protected $_version = null;

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var array
     */
    protected $_info = [];

    /**
     * @var Plugin
     */
    protected $_model = null;

    /**
     * @var string
     */
    protected $_plugin_config_key;


    /**
     * @var string
     */
    protected $_plugin_config_key_prefix;

    /**
     * @var PluginCollection
     */
    protected $_collection;

    /**
     * @var string
     */
    protected $_namespace;

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * @var string
     */
    protected $_source;

    /**
     *
     * get the plugin collection with all plugins
     *
     * @return PluginCollection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     *
     * set the plugin collection
     *
     * @param PluginCollection $collection
     * @return PluginBootstrap
     */
    public function setCollection(PluginCollection $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     *
     * the constructor
     *
     * @param $name
     * @param null $info
     */
    public function __construct($name, $info = null) {

        $this->_name = $name;
        $this->_namespace = substr(get_class($this), 0, strrpos(get_class($this), '\\'));

        $namespace_arr = explode("\\", $this->_namespace);
        $this->_source = $namespace_arr[count($namespace_arr) - 2];

        $this->_plugin_config_key = 'plugin.' . strtolower($name);
        $this->_plugin_config_key_prefix = $this->_plugin_config_key . '.';
        config($this->getInfo());
        config([$this->_plugin_config_key_prefix . 'capabilities' => $this->getCapabilities()]);
        if(is_array($info)) {
            $this->addInfos($info);
        }
    }

    /**
     *
     * returns the complete plugin info array
     *
     * @return array
     */
    public function getPluginInfo() {

        $arr = config($this->_plugin_config_key);
        $arr = array_merge($arr, [
            'class' => get_called_class()
        ]);
        return $arr;
        //return config($this->_plugin_config_key);
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
            $this->_plugin_config_key_prefix . 'version' => $this->getVersion(),
            $this->_plugin_config_key_prefix . 'label' => $this->getLabel()
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
        return $this->_version;
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
    final public function getName() {
        return $this->_name;
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
     * add's a info to the configuration
     *
     * @param $key
     * @param $value
     */
    public function addInfo($key, $value) {
        config([$this->_plugin_config_key_prefix . $key => $value]);
    }

    /**
     *
     * adds many infos to the plugin configuration
     *
     * @param array $info
     */
    public function addInfos(array $info = []) {
        foreach($info as $key => $value) {
            $this->addInfo($key, $value);
        }
    }

    /**
     * deactivate the plugin
     */
    public function deactivate() {
        $this->_deactivate();
    }

    /**
     * activates the plugin
     *
     * if plugin dont installed the plugin will activate at first
     *
     */
    public function activate() {
        if($this->isInstalled()) {
            $this->_activate();
        } else {
            \DB::transaction(function () {
                $this->_install();
                $this->_activate();
            });
        }
    }

    /**
     * install the plugin
     */
    public function install() {
        $this->_install();
    }

    /**
     * deinstall the plugin
     */
    public function deinstall() {
        $this->_deinstall();
    }

    /**
     * @return Plugin
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param Plugin $model
     */
    public function setModel(Plugin $model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->_model->active;
    }

    /**
     * @return bool
     */
    public function isInstalled() {
        return $this->_model->installed;
    }

    public function subscribeEvent($event, $listener, $position = null) {
        $this->_subscribeEvent($event, $listener, $position);
    }

    /**
     * Magic caller
     *
     * @param   string $name
     * @param   array $args
     */
    public function __call($name, $args=null) {
        if(strpos($name, 'get') === 0) {
            $name = strtolower(substr($name, 3));
        }
        $info = $this->getInfo();
        $key = $this->_plugin_config_key_prefix . $name;
        if(isset($info[$key])) {
            return $info[$key];
        }
        return null;
    }

    /**
     * deactivate the plugin in database
     */
    protected function _deactivate() {
        $this->_model->fill([
            'active' => false,
            'activation_date' => null
        ]);
        $this->_model->save();
    }

    /**
     * activate the plugin in database
     */
    protected function _activate() {
        $this->_model->fill([
            'active' => true,
            'activation_date' => new Carbon
        ]);
        $this->_model->save();
    }

    /**
     * install the plugin in database
     */
    protected function _install() {
        $this->_model->fill([
            'installed' => true,
            'installation_date' => new Carbon
        ]);
        $this->_model->save();
    }

    /**
     * deinstall the plugin in database
     */
    protected function _deinstall() {
        $this->_model->fill([
            'installed' => false,
            'installation_date' => null
        ]);
        $this->_model->save();
    }

    protected function _subscribeEvent($event, $listener, $position = null) {
        $plugin_subscribe_data = [
            'subscribe' => $event,
            'listener' => $listener,
            'position' => $position
        ];

        $this->_model->subscribes()->create($plugin_subscribe_data);
    }

}