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

use Illuminate\Console\Command;
use Lava83\LavaProto\Repositories\CronRepository;
use Symfony\Component\Console\Helper\Table;

/**
 * Class PluginsListCommand
 * @package Lava83\LavaProto\Console
 * @author Stefan Riedel<sr@srit83.de>
 */
class CronList extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:cron-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows all cronjobs to the application.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        /** @var CronRepository $cron */
        $cron = app(CronRepository::class);
        if ($cronCollection = $cron->all() and count($cronCollection) > 0) {
            /** @var Table $table */
            $table = new Table($this->getOutput());
            $table->setHeaders(['name', 'action', 'active', 'interval', 'next run', 'last run']);
            $rows = [];

            foreach ($cronCollection as $cron) {
                $rows[] = [
                    $cron->name,
                    $cron->action,
                    $cron->active ? 'yes' : 'no',
                    $cron->interval,
                    $cron->next,
                    $cron->end
                ];
            }
            $table->setRows($rows);
            $table->render();
        } else {
            $line = <<<EVO
<info>no crons avalaible</info>
EVO;
            $this->line($line);
        }
    }
}
