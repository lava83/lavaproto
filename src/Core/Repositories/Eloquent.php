<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Created by PhpStorm.
 * User: stefanriedel
 * Date: 16.03.16
 * Time: 11:52
 */

namespace Lava83\LavaProto\Core\Repositories;

use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository as BaseEloquentRepository;

abstract class Eloquent extends BaseEloquentRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $args = [
            'subject' => $this
        ];
        notify(__CLASS__ . '_Init', $args);
        notify(get_called_class() . '_Init', $args);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $attributes, $id)
    {
        return $this->_fireParentMethodAndNotify([$attributes, $id], 'update');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->_fireParentMethodAndNotify([$id], 'delete');
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes)
    {
        return $this->_fireParentMethodAndNotify([$attributes], 'create');
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param $rules
     * @return $this
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }

    protected function _fireParentMethodAndNotify($attributes, $method, $args = [])
    {
        $args = array_merge($args, [
            'subject' => $this
        ]);
        $this->_notifyPreEvents($args, $method);
        $ret = call_user_func_array('parent::' . $method, $attributes);
        $args = array_merge($args, ['ret' => $ret]);
        $this->_notifyPostEvents($args, $method);
        return $ret;
    }

    protected function _notifyPreEvents($args, $method)
    {
        $this->_notifyEvents($args, 'Pre::' . $method);
    }

    protected function _notifyPostEvents($args, $method)
    {
        $this->_notifyEvents($args, 'Post::' . $method);
    }

    protected function _notifyEvents($args, $suffix = null)
    {
        notify(__CLASS__ . '_' . $suffix, $args);
        notify(get_called_class() . '_' . $suffix, $args);
    }


}