<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt 
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 20.04.16
 * Time: 11:51
 */

namespace Lava83\LavaProto\Repositories;
use Lava83\LavaProto\Core\Repositories\Eloquent;
use Lava83\LavaProto\Criterias\CronToRun;
use Lava83\LavaProto\Entities\Cron;
use Prettus\Repository\Criteria\RequestCriteria;

class CronRepostitoryEloquent extends Eloquent implements CronRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Cron::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findAllToRun()
    {
        $this->pushCriteria(CronToRun::class);
        return $this->all();

    }


}