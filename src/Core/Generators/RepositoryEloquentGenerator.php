<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 29.03.16
 * Time: 11:08
 */

namespace Lava83\LavaProto\Core\Generators;

use Prettus\Repository\Generators\Stub;

class RepositoryEloquentGenerator extends \Prettus\Repository\Generators\RepositoryEloquentGenerator
{

    public function getStub()
    {
        return (new Stub(
            __DIR__ . '/Stubs/' . $this->stub . '.stub',
            $this->getReplacements()
        )
        )->render();
    }
}
