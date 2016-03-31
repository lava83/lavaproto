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
class PluginInstall extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lava83:plugin-install {plugin}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lava83:plugin-install';

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
        $plugin_name = $this->argument('plugin');
        $error_line = <<<EOF
Sorry but the plugin: '{$plugin_name}' doenst exists!
EOF;
        $confirm_line = <<<EOF
The plugin: '{$plugin_name}' doenst exists do you whish to sync plugins? [y|N]
EOF;
        $success_line = <<<EOF
<comment>{$plugin_name} installed success</comment>
EOF;
        $this->pluginManager->init();
        if ($plugin = $this->pluginManager->getPluginCollection()->get($plugin_name)) {
            $plugin->install();
            $this->line($success_line);
        } else {
            if ($this->confirm($confirm_line, 'y')) {
                $this->pluginManager->sync();
                if ($plugin = $this->pluginManager->getPluginCollection()->get($plugin_name)) {
                    $plugin->install();
                    $this->line($success_line);
                } else {
                    $this->error($error_line);
                }
            }
        }


    }
}
