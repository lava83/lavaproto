<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 11.01.16
 * Time: 16:53
 */

namespace Lava83\LavaProto\Core\Plugins;

use Lava83\LavaProto\Exceptions\PluginManagerException;
use Lava83\LavaProto\Models\Plugin;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class PluginManager
 * @package Lava83\LavaProto\Core\Plugins
 */
class PluginManager
{
    /**
     *
     * plugin paths
     *
     * @var array
     */
    protected $path = null;

    /**
     *
     * plugin namespaces
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * @var PluginCollection
     */
    protected $pluginCollection;

    /**
     * @param array $path
     * @param array $namespaces
     */
    public function __construct($path = null, $namespaces = [])
    {
        $this->setPath($path);
        $this->addNamespaces($namespaces);
        $this->init();
    }

    /**
     *
     * add many namespaces
     *
     * @param array $namespaces
     * @return PluginManager
     */
    public function addNamespaces($namespaces)
    {
        if ($namespaces) {
            $this->namespaces = array_merge($this->namespaces, $namespaces);
        }
        return $this;
    }

    /**
     * initialize the plugin manager
     */
    public function init()
    {
        if (!empty($this->path) && !empty($this->namespaces)) {
            $this->getPluginCollection();
        }
    }

    /**
     * @return PluginCollection
     */
    public function getPluginCollection()
    {
        if (empty($this->pluginCollection)) {
            $this->pluginCollection = new PluginCollection();
            $plugin_model = new Plugin;
            if (\Schema::hasTable($plugin_model->getTable()) and $plugins = $plugin_model->all()) {
                foreach ($plugins as $pl) {
                    $plugin_path = $this->path . DIRECTORY_SEPARATOR . $pl->source . DIRECTORY_SEPARATOR . $pl->name .
                        DIRECTORY_SEPARATOR . 'Bootstrap.php';
                    $plugin = $this->getPlugin($plugin_path);
                    $plugin->setModel($pl);
                    $this->pluginCollection->registerPlugin($plugin);
                }
            }
        }
        return $this->pluginCollection;
    }

    /**
     * @return array
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param array $path
     * @return PluginManager
     */
    public function setPath($path)
    {

        if (\File::exists($path) && \File::isDirectory($path)) {
            $this->path = $path;
        }
        return $this;
    }

    /**
     * @return array
     * @return void
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @param array $namespaces
     * @return PluginManager
     */
    public function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
        return $this;
    }

    /**
     *
     * add a single namespace
     *
     * @param $namespace
     * @return PluginManager
     */
    public function addNamespace($namespace)
    {
        $this->namespaces[] = $namespace;
        return $this;
    }

    /**
     * @return PluginCollection
     * @throws PluginManagerException
     */
    public function getCompleteCollection()
    {
        $collection = new PluginCollection();

        foreach ($this->namespaces as $namespace) {
            //foreach ($this->_path as $path) {
            $plugin_dir = $this->path . DIRECTORY_SEPARATOR . $namespace;
            if (\File::exists($plugin_dir) && \File::isDirectory($plugin_dir)) {
                $namespace_files = \File::allFiles($plugin_dir);
                foreach ($namespace_files as $file) {
                    /** @var $file SplFileInfo */
                    if ($file->getBasename() === 'Bootstrap.php') {
                        $bootstrap_path = $file->getPath() . DIRECTORY_SEPARATOR . $file->getBasename();
                        $plugin = $this->getPlugin($bootstrap_path);
                        $collection->registerPlugin($plugin);
                    }
                }
            }
            //}
        }

        return $collection;
    }

    /**
     * @param $plugin_path
     * @return PluginBootstrap
     * @throws PluginManagerException
     */
    public function getPlugin($plugin_path)
    {
        list($namespace, $cls) = \File::getClassInfo($plugin_path);
        if ($namespace == null) {
            throw new PluginManagerException(sprintf(
                "The plugin: '%s' doesn't have a namespace but the initializer expects a namespace in the class",
                $plugin_path
            ));
        }

        $namespace_arr = explode("\\", $namespace);
        $name = end($namespace_arr);

        $cls_name = $namespace . "\\" . $cls;

        /** @var $object PluginBootstrap */
        $object = new $cls_name($name);
        return $object;
    }

    /**
     * to synchronise plugins only available in plugin directory
     * but not in database
     *
     * @return PluginManager
     */
    public function sync()
    {
        $complete_collection = $this->getCompleteCollection();
        foreach ($complete_collection as $plugin) {
            /** @var $plugin PluginBootstrap */
            /** @var  $model Plugin */


            $plugin_data = [
                'namespace' => $plugin->getNamespace(),
                'source' => $plugin->getSource(),
                'name' => $plugin->getName(),
                'version' => $plugin->getVersion(),
                'author' => $plugin->getAuthor(),
                'copyright' => $plugin->getCopyright(),
                'license' => $plugin->getLicense(),
                'description' => $plugin->getDescription(),
                'link' => $plugin->getLink(),

            ];
            if (!($model = Plugin::where('namespace', $plugin->getNamespace())->first())) {
                $model = Plugin::create($plugin_data);
            } else {
                $model->update($plugin_data);
            }
            $plugin->setModel($model);
            $this->pluginCollection->registerPlugin($plugin);
        }
        return $this;
    }
}
