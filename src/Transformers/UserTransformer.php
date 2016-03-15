<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 15.03.16
 * Time: 15:16
 */

namespace Lava83\LavaProto\Transformers;


use Lava83\LavaProto\Entities\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Transform the User entity
     * @param User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id'         => (int) $model->id,
            'name'       => $model->name,
            'email'      => $model->email,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}