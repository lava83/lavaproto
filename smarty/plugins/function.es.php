<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 08.01.16
 * Time: 16:45
 */


/**
 *
 * an escape modifier to escape html and script tags in html
 * can use:
 *
 * `{es v="<strong>FooBar</strong>"}` or `{es value="<strong>FooBar</strong>"}`
 * or `{es v="<strong>FooBar</strong>" assign=fooBar}`
 *
 * The last example will assign the smarty variable $fooBar
 *
 * Output:
 * `<strong>FooBar</strong>`
 *
 * @param $params
 * @param $template
 * @return mixed
 * @throws \Lava83\LavaProto\Exceptions\SmartyPluginException
 */
function smarty_function_es($params, $template) {
    if(!isset($params['value']) && !isset($params['v'])) {
        throw new \Lava83\LavaProto\Exceptions\SmartyPluginException('No value was set! Please fix it!');
    }

    $value = (isset($params['value'])) ? $params['value'] : $params['v'];
    $value = e($value);

    if (!isset($params['assign'])) {
        return $value;
    } else {
        $template->assign($params['assign'], $value);
    }
}