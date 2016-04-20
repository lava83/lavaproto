<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 14:37
 */

namespace Lava83\LavaProto\Console\Commands;

use Symfony\Component\Console\Helper\Table;
use Illuminate\Console\Command;
use Lava83\LavaProto\Core\Plugins\PluginBootstrap;

/**
 * Class PluginsListCommand
 * @package Lava83\LavaProto\Console
 * @author Stefan Riedel<sr@srit83.de>
 */
class PluginList extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:plugin-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Is showing a list of all avalaible plugins.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        /** @var $plugin PluginBootstrap */
        \PluginManager::sync();
        if ($collection = \PluginManager::getPluginCollection() and count($collection) > 0) {
            /** @var Table $table */
            $table = new Table($this->getOutput());
            $table->setHeaders(['name', 'version', 'description', 'active', 'installed']);
            $rows = [];

            foreach ($collection as $plugin) {
                $rows[] = [
                    $plugin->getName(),
                    $plugin->getVersion(),
                    $plugin->getDescription(),
                    ($plugin->isActive()) ? 'yes' : 'no',
                    ($plugin->isInstalled()) ? 'yes' : 'no'
                ];
            }
            $table->setRows($rows);
            $table->render();
        } else {
            $line = <<<EVO
<info>no plugins avalaible</info>
EVO;
            $this->line($line);
        }


        /*$this->line(<<<EOF
The <info>%command.name%</info> command lists all commands:

    <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:
EOF
);*/


        //$this->line('Alle Plugins.asdkjashdkash');
    }
}
