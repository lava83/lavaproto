<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 18.01.16
 * Time: 10:42
 */

namespace Lava83\LavaProto\Console\Commands;

use Illuminate\Console\Command;
use Lava83\LavaProto\Core\Plugins\PluginManager;

/**
 * Class PluginActivate
 * @package Lava83\LavaProto\Console\Commands
 */
class PluginDeinstall extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lava83:plugin-deinstall {plugin}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:plugin-deinstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a plugin by name';


    /**
     * @var PluginManager
     */
    protected $pluginManager;

    public function __construct(PluginManager $pluginManager)
    {
        parent::__construct();
        $this->pluginManager = $pluginManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->pluginManager->init();
        $plugin = $this->pluginManager->getPluginCollection()->get($this->argument('plugin'));
        if ($plugin->isActive()) {
            $plugin->deactivate();
        }
        $plugin->deinstall();
        $line = <<<EOF
<comment>{$plugin->getName()} deinstalled success</comment>
EOF;
        $this->line($line);
    }
}
