<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 14:37
 */

namespace Lava83\LavaProto\Console\Commands;

use Illuminate\Foundation\Application;
use Lava83\LavaProto\Core\Command\Command;
use Lava83\LavaProto\Core\Cron\Bootstrap;
use Lava83\LavaProto\Entities\Cron;
use Lava83\LavaProto\Repositories\CronRepository;
use Symfony\Component\Console\Helper\Table;

/**
 * Class PluginsListCommand
 * @package Lava83\LavaProto\Console
 * @author Stefan Riedel<sr@srit83.de>
 */
class CronRun extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:cron-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all cronjobs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        /** @var CronRepository $cron */
        $cron = app(CronRepository::class);
        if ($cronCollection = $cron->findAllToRun() and count($cronCollection) > 0) {
            foreach ($cronCollection as $item) {
                /** @var Cron $item */
                $item->start();

                $clsName = $item->action;
                try {
                    /** @var Bootstrap $cronCls */
                    $cronCls = app($clsName);
                } catch (\ReflectionException $e) {
                    if (class_exists($clsName)) {
                        //not bind so we initiate the class php like without dependency injection
                        $cronCls = new $clsName;
                    } else {
                        $error = "Class: {$clsName} not exists";
                        $this->showLine("<error>$error</error>");
                        $item->finishFailed(['error' => $error]);
                        continue;
                    }
                }
                if ($cronCls instanceof Bootstrap) {
                    $this->showLine("<info>Run: {$item->name}</info>");
                    $cronCls->setOutput($this->getOutput());
                    $ret = $cronCls->run();
                    $item->finish(['return' => $ret]);
                }
            }
        }
    }
}
