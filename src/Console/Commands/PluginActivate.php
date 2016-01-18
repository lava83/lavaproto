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
class PluginActivate extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lava83:plugin-activate {plugin}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:plugin-activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a plugin by name';


    /**
     * @var PluginManager
     */
    protected $_pluginmanager;

    public function __construct(PluginManager $pluginManager)
    {
        parent::__construct();
        $this->_pluginmanager = $pluginManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->_pluginmanager->init();
        $plugin = $this->_pluginmanager->getCollection()->get($this->argument('plugin'));
        $plugin->activate();
        $line = <<<EOF
<comment>{$plugin->getName()} activated success</comment>
EOF;
        $this->line($line);
    }

}