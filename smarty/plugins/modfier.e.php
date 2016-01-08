<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 08.01.16
 * Time: 15:26
 */


/**
 *
 * an escape modifier to escape html and script tags in html
 * can use:
 *
 * `{"<strong>FooBar</strong>"|e}`
 *
 * Output:
 * `<strong>FooBar</strong>`
 *
 * @param null $value the value to escape
 * @return null|string
 */
function smarty_function_e($value = null) {
    if(!empty($value)) {
        return e($value);
    }
    return $value;
}