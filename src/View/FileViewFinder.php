<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 08.01.16
 * Time: 11:59
 */

namespace Lava83\LavaProto\View;

use \Illuminate\View\FileViewFinder as BaseFileViewFinder;

class FileViewFinder extends BaseFileViewFinder
{

    /**
     *
     * prepend a view location
     *
     * @param $location
     * @return FileViewFinder
     */
    public function prependLocation($location)
    {
        array_unshift($this->paths, $location);
        return $this;
    }
}
