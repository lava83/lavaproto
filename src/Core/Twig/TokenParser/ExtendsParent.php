<?php
/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 03.02.16
 * Time: 14:00
 */

namespace Lava83\LavaProto\Core\Twig\TokenParser;

use Lava83\LavaProto\Exceptions\TwigTokenException;
use Symfony\Component\Routing\Tests\Loader\DirectoryLoaderTest;
use Twig_Token;

class ExtendsParent extends \Twig_TokenParser
{

    static $_usedPaths = [];

    /*public function __construct()
    {
        if(empty(static::$_paths)) {
            //to iterate the array correct i reverse it
            static::$_paths = array_reverse(app()['lava83.twig.loader.filesystem']->getAllPaths());
        }
    }*/

    public function getTag()
    {
        return 'extendsparent';
    }

    public function parse(Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $template_expr = $expr->getNode('left');
        $namespace_expr = $expr->getNode('right');

        $template = $template_expr->getAttribute('value');
        $namespace = $namespace_expr->getAttribute('value');
        foreach (array_reverse(app()['lava83.twig.loader.filesystem']->getAllPaths()) as $path_namespace => $path) {
            if (count($path) > 1) {
                throw new TwigTokenException(sprintf('Extendsparent token parser supports only one paths. You have %d paths declared.', count($path)));
            }
            $path = $path[0];
            static::$_usedPaths[$namespace] = $path;

            $complete_template_path = $path . DIRECTORY_SEPARATOR . $template;
            if(!file_exists($complete_template_path)) {
                continue;
            }

            if ($namespace == $path_namespace || array_key_exists($path_namespace, static::$_usedPaths)) {
                continue;
            }
            $template_expr->setAttribute('value', '@' . $path_namespace . '/' . $template);
            break;
        }

        $this->parser->setParent($template_expr);


        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
    }
}