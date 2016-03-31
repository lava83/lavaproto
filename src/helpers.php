<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 19.01.16
 * Time: 14:34
 */

use \Lava83\LavaProto\Core\Events\Args;

function notify($event, $args = [], $halt = false)
{
    if (is_array($args)) {
        $args = new Args($args);
    } elseif (!($args instanceof \Lava83\LavaProto\Core\Events\Args)) {
        $args = new Args((array)$args);
    }
    return event($event, $args, $halt);
}
