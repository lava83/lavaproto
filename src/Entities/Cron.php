<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 20.04.16
 * Time: 11:48
 */

namespace Lava83\LavaProto\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Cron extends Model implements Transformable
{
    use TransformableTrait;

    protected $dates = [
        'next', 'start', 'end', 'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name',
        'action',
        'data',
        'next',
        'start',
        'end',
        'interval',
        'active',
        'informemail',
        'plugin_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Cron $model) {
            if (is_null($model->next)) {
                $now = new Carbon;
                $beforeOneYear = Carbon::create()->subYear(1);
                $model->next = $now;
                $model->end = $beforeOneYear;
            }
        });
    }

    public function start()
    {
        $this->start = new Carbon;
        $this->save();
    }

    public function finish($data = [])
    {
        $this->fill([
            'start' => null,
            'end' => new Carbon,
            'data' => json_encode($data, JSON_HEX_TAG),
            'next' => Carbon::create()->addSeconds($this->interval)
        ]);
        $this->save();
    }

    public function finishFailed($data = [])
    {
        $this->fill([
            'start' => null,
            'end' => new Carbon,
            'data' => json_encode($data, JSON_HEX_TAG),
            'active' => false
        ]);
        $this->save();
    }
}
