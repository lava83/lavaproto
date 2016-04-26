<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 20.04.16
 * Time: 11:46
 */

namespace Lava83\LavaProto\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface CronRepository extends RepositoryInterface
{
    public function findAllToRun();
}
