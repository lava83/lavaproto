<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 15.01.16
 * Time: 09:37
 */

namespace Lava83\LavaProto\Repositories;


use Lava83\LavaProto\Models\Plugin;

class PluginRepository extends Repository
{

    public function model()
    {
        return Plugin::class;
    }
}