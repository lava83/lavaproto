<?php
/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 04.02.16
 * Time: 12:09
 */

namespace Lava83\LavaProto\Core\Twig\Loader;

class Filesystem extends \Twig_Loader_Filesystem
{

    /**
     * @return array
     */
    public function getAllPaths()
    {
        return $this->paths;
    }
}
