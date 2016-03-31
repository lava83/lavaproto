<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 15.01.16
 * Time: 09:37
 */

namespace Lava83\LavaProto\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{

    protected $dates = [
        'created_at',
        'updated_at',
        'installation_date',
        'activation_date'
    ];

    protected $fillable = [
        'namespace',
        'source',
        'name',
        'version',
        'author',
        'copyright',
        'license',
        'description',
        'link',
        'support_link',
        'installed',
        'active',
        'installation_date',
        'activation_date',
    ];


    protected $casts = [
        'active' => 'bool'
    ];

    public function subscribes()
    {
        return $this->hasMany(PluginSubscribe::class);
    }
}
