<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 30.03.16
 * Time: 17:41
 */

namespace Lava83\LavaProto\Core\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends Twig_Extension
{

    public function getName()
    {
        return 'LavaProto_Core_Twig_Extension_Translator';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $translator = app(LaravelTranslator::class);
        return [
            new Twig_SimpleFunction('trans', [$translator, 'trans']),
            new Twig_SimpleFunction('trans_choice', [$translator, 'transChoice']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $translator = app(LaravelTranslator::class);
        return [
            new Twig_SimpleFilter(
                'trans',
                [$translator, 'trans'],
                [
                    'pre_escape' => 'html',
                    'is_safe'    => ['html'],
                ]
            ),
            new Twig_SimpleFilter(
                'trans_choice',
                [$translator, 'transChoice'],
                [
                    'pre_escape' => 'html',
                    'is_safe'    => ['html'],
                ]
            ),
        ];
    }
}
