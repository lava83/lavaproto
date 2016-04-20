<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 20.04.16
 * Time: 14:44
 */

namespace Lava83\LavaProto\Criterias;


use Carbon\Carbon;
use Lava83\LavaProto\Entities\Cron;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class CronToRun implements CriteriaInterface
{
    /**
     * @param Cron $model
     * @param RepositoryInterface $repository
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('active', true)
            ->whereNull('start')
            ->whereDate('next', '<=', new Carbon);
        return $model;
    }

}