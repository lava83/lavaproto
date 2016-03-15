<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 15.03.16
 * Time: 15:18
 */

namespace Lava83\LavaProto\Presenters;


use Lava83\LavaProto\Transformers\UserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserPresenter
 * @package Lava83\LavaProto\Presenters
 */
class UserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}