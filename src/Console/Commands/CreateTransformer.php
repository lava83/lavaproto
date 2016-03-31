<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 29.03.16
 * Time: 12:28
 */

namespace Lava83\LavaProto\Console\Commands;

use Prettus\Repository\Generators\Commands\TransformerCommand;

class CreateTransformer extends TransformerCommand
{
    protected $name = 'lava83:make:transformer';
}
