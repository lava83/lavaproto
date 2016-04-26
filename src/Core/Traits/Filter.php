<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 26.04.16
 * Time: 13:19
 */

namespace Lava83\LavaProto\Core\Traits;


trait Filter
{
    public function setAttribute($key, $value)
    {
        if (isset($this->filter) && isset($this->filter[$key])) {
           foreach($this->filter[$key] as $filter)
           {
               $value = $filter($value);
           }
        }
        parent::setAttribute($key, $value);
    }

}