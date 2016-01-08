<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 08.01.16
 * Time: 13:59
 */

/**
 *
 * translation modifier
 * can use:
 *
 * ```
 * {"key.to.translate"|tr:['name' => 'foo']:10:'messages':'de'}
 * ```
 *
 * this will output:
 *
 * > Hallo 'foo' du hast 10 Bananen gefunden.
 *
 * @param null $value the translation key
 * @param array $params the parameter for translation eg ['name' => $foo]
 * @param null $pluralization integer for pluralization if this is set you can build plural and singular translations
 * @param null $domain the message domain default: messages
 * @param null $locale locale if not set the active locale from laravel will use
 * @return string
 */
function smarty_modifier_tr($value = null, $params = [], $pluralization = null, $domain = null, $locale = null) {

    if($pluralization && is_int($pluralization)) {
        return trans_choice($value, $pluralization, $params, $domain, $locale);
    }

    return trans($value, $params, $domain, $locale);

}