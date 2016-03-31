<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 08.01.16
 * Time: 11:59
 */

namespace Lava83\LavaProto\View;

use Illuminate\View\Factory as ViewFactory;
use Lava83\LavaProto\Exceptions\ViewException;

class View extends ViewFactory
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Illuminate\Contracts\View\View
     */
    protected $view;

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array $data
     * @param  array $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        $normal_path = $this->normalizeName($view);
        $absolut_path = $this->finder->find($normal_path);
        $this->preparePath($absolut_path);
        $this->view = parent::make($view, $data, $mergeData);
        return $this->view;
    }

    /**
     *
     * prepare the path for smarty template engine
     *
     * @param $path
     */
    protected function preparePath($path)
    {
        $extends_pos = strpos($this->path, 'extends:');
        if ($this->path && $extends_pos === 0) {
            $this->path .= '|' . $path;
        } elseif ($this->path && $extends_pos === false) {
            $this->path = 'extends:' . $this->path . '|' . $path;
        } else {
            $this->path = $path;
        }
    }

    /**
     *
     * extend a smarty template view
     *
     * @param $view
     * @return $this
     */
    public function extendTemplate($view)
    {
        $normal_path = $this->normalizeName($view);
        $absolut_path = $this->finder->find($normal_path);
        $this->preparePath($absolut_path);
        $this->view->setPath($this->path);
        return $this;
    }


    /**
     *
     * return the path of the actually view
     *
     * @return string
     * @throws ViewException
     */
    public function getPath()
    {
        if (is_null($this->view)) {
            throw new ViewException('View is not defined');
        }
        return $this->view->getPath();
    }

    /**
     *
     * wrapper of FileViewFinder::prependLocation
     *
     * @see FileViewFinder::prependLocation
     * @param $location
     */
    public function prependLocation($location, $namespace = null)
    {
        //app('smarty.view')->getSmarty()->prependDirectory($location);
        $this->finder->prependLocation($location);

    }
}
