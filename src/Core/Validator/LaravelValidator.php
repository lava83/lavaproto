<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Lava83\LavaProto\Core\Validator;

use Prettus\Validator\LaravelValidator as BaseLaravelValidator;

class LaravelValidator extends BaseLaravelValidator
{

    protected $messages = [];

    /**
     *
     * override parent method if own messages available
     *
     * @param null $action
     * @return bool
     */
    public function passes($action = null)
    {
        if (!empty($this->messages)) {
            $rules = $this->getRules($action);
            $validator = $this->validator->make($this->data, $rules, $this->messages);

            if ($validator->fails()) {
                $this->errors = $validator->messages();
                return false;
            }

            return true;
        }
        return parent::passes($action);
    }


}
