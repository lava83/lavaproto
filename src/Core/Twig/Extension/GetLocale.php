<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 01.08.16
 * Time: 11:10
 */

namespace Lava83\LavaProto\Core\Twig\Extension;


class GetLocale extends \Twig_Extension
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'App_Twig_Extension_GetLocale';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('locale', array($this, 'locale')),
        );
    }

    public function locale()
    {
        return app()->getLocale();
    }

}
