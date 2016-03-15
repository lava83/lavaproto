<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 14:37
 */

namespace Lava83\LavaProto\Console\Commands;


use Illuminate\Console\Command;
use Lava83\LavaProto\Core\Plugins\PluginBootstrap;

/**
 * Class PluginsListCommand
 * @package Lava83\LavaProto\Console
 * @author Stefan Riedel<sr@srit83.de>
 */
class PluginsList extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:plugins-list';

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
        if ($collection = \PluginManager::getCollection() and count($collection) > 0) {
            foreach ($collection as $plugin) {

                $active = ($plugin->isActive()) ? 'yes' : 'no';
                $installed = ($plugin->isInstalled()) ? 'yes' : 'no';
                $line = <<<EOF
<comment>{$plugin->getName()}:</comment>
    <info>version: {$plugin->getVersion()}</info>
    <info>description: {$plugin->getDescription()}</info>
    <info>active: {$active}</info>
    <info>installed: {$installed}</info>
EOF;
                $this->line($line);
            }
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