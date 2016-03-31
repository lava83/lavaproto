<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 15.01.16
 * Time: 09:37
 */

namespace Lava83\LavaProto\Models;

use Illuminate\Database\Eloquent\Model;

class PluginSubscribe extends Model
{
    protected $fillable = [
        'subscribe',
        'listener',
        'position'
    ];

    public function plugin()
    {
        return $this->belongsTo(Plugin::class);
    }
}
