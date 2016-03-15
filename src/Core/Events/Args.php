<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 15.03.16
 * Time: 17:21
 */

namespace Lava83\LavaProto\Core\Events;


use Lava83\LavaProto\Exceptions\EventException;

class Args
{

    protected $_args = [];

    public function __construct(array $args = [])
    {
        foreach($args as $key => $value) {
            $this->_args[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    function __call($name, $arguments)
    {

        if(strpos($name, 'get') !== false) {
            $argname = strtolower(substr($name, 3));
            if(isset($this->_args[$argname])) {
                return $this->_args[$argname];
            } else {
                throw new EventException(sprintf('The argument: "%s" doesnt exists', $argname));
            }
        }

    }


}