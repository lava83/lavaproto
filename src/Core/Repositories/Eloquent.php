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
use Prettus\Repository\Events\RepositoryEntityCreated;
use Prettus\Repository\Events\RepositoryEntityUpdated;
use Prettus\Validator\Contracts\ValidatorInterface;

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
        return $this->fireParentMethodAndNotify([$attributes, $id], 'update');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->fireParentMethodAndNotify([$id], 'delete');
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes)
    {
        return $this->fireParentMethodAndNotify([$attributes], 'create');
    }

    public function forceCreate(array $attributes) {
        $args = [
            'subject' => $this
        ];
        $this->notifyPreEvents($args, 'create');

        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $attributes = $this->model->newInstance()->forceFill($attributes)->toArray();

            $this->validator->with($attributes)->passesOrFail(ValidatorInterface::RULE_CREATE);
        }

        $model = $this->model->newInstance();
        $model->forceFill($attributes);
        $model->save();
        $this->resetModel();
        event(new RepositoryEntityCreated($this, $model));
        $ret = $this->parserResult($model);
        $args = array_merge($args, ['ret' => $ret]);
        $this->notifyPostEvents($args, 'create');
        return $ret;
    }

    public function forceUpdate(array $attributes, $id) {
        $args = [
            'subject' => $this
        ];
        $this->notifyPreEvents($args, 'update');

        $this->applyScope();

        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $attributes = $this->model->newInstance()->forceFill($attributes)->toArray();

            $this->validator->with($attributes)->setId($id)->passesOrFail(ValidatorInterface::RULE_UPDATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->findOrFail($id);
        $model->forceFill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        $ret = $this->parserResult($model);

        $args = array_merge($args, ['ret' => $ret]);
        $this->notifyPostEvents($args, 'update');
        return $ret;
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

    protected function fireParentMethodAndNotify($attributes, $method, $args = [])
    {
        $args = array_merge($args, [
            'subject' => $this
        ]);
        $this->notifyPreEvents($args, $method);
        $ret = call_user_func_array('parent::' . $method, $attributes);
        $args = array_merge($args, ['ret' => $ret]);
        $this->notifyPostEvents($args, $method);
        return $ret;
    }

    protected function notifyPreEvents($args, $method)
    {
        $this->notifyEvents($args, 'Pre::' . $method);
    }

    protected function notifyPostEvents($args, $method)
    {
        $this->notifyEvents($args, 'Post::' . $method);
    }

    protected function notifyEvents($args, $suffix = null)
    {
        notify(__CLASS__ . '_' . $suffix, $args);
        notify(get_called_class() . '_' . $suffix, $args);
    }
}
