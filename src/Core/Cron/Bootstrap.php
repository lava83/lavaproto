<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 20.04.16
 * Time: 12:52
 */

namespace Lava83\LavaProto\Core\Cron;


use Symfony\Component\Console\Output\Output;

abstract class Bootstrap
{

    /**
     * @var Output
     */
    protected $output;

    /**
     * @param Output $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    abstract public function run();

}