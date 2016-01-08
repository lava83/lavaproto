<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 07.01.16
 * Time: 12:20
 */

namespace Lava83\LavaProto\Repositories;
use Bosnadev\Repositories\Eloquent\Repository as BaseRepository;
use Illuminate\Cache\TaggedCache;
use Illuminate\Container\Container as App;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Repository extends BaseRepository
{

    /**
     * @var TaggedCache
     */
    protected $_cache;


    /**
     *
     * getter for the cache object
     *
     * @return TaggedCache
     */
    public function getCache() {
        if(null == $this->_cache) {
            $this->_cache = \Cache::tags(get_called_class());
        }
        return $this->_cache;
    }

    /**
     *
     * creates the cache key for the current opereation
     *
     * @param array $attributes
     * @return string
     */
    public function getCacheKey(array $attributes = [])
    {
        $key = get_called_class();
        $key .= implode('_', $attributes);
        /*foreach($this->getCriteria() as $criteria) {
            if($criteria instanceof Criteria) {
                $key .= get_class($criteria);
            }
        }*/
        $key .= serialize($this->getCriteria());
        $key .= \Input::get('page', 1);
        $key = sha1($key);
        return $key;
    }



    //==================================================
    //================= override methods ===============

    /**
     *
     * deletes a node
     *
     * @param mixed $id primary key of node
     * @return bool
     */
    public function delete($id)
    {
        if($ret = parent::delete($id)) {
            $this->getCache()->flush();
        }
        return $ret;

    }

    /**
     *
     * create a node
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $data = $this->_clearFromFormHelpers($data);
        $ret = parent::create($data);
        $this->flushCache();
        return $ret;
    }

    /**
     *
     * update a node
     *
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $data = $this->_clearFromFormHelpers($data);
        $ret =  parent::update($data, $id, $attribute);
        $this->flushCache();
        return $ret;
    }

    /**
     *
     * update a node only the model can find
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function updateRich(array $data, $id)
    {
        $data = $this->_clearFromFormHelpers($data);
        $ret = parent::updateRich($data, $id);
        $this->flushCache();
        return $ret;
    }

    /**
     *
     * get all nodes of the given model
     *
     * @param array $columns
     * @return Collection|null
     */
    public function all($columns = array('*'))
    {
        $args = [$columns];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    /**
     *
     * get one node by $attribute attributes
     *
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return Model|null
     */
    public function findBy($attribute, $value, $columns = array('*'))
    {
        $args = [$attribute, $value, $columns];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    /**
     *
     * get one node by the given primary key $id
     *
     * @param $id
     * @param array $columns
     * @return Model|null
     */
    public function find($id, $columns = array('*'))
    {
        $args = [$id, $columns];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    /**
     *
     * get all nodes by $attribute attributes
     *
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return Collection|null
     */
    public function findAllBy($attribute, $value, $columns = array('*'))
    {
        $args = [$attribute, $value, $columns];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param array $where
     * @param array $columns
     * @param bool $or
     *
     * @return Collection|null
     */
    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $args = [$where, $columns, $or];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    /**
     *
     * get array nodes with the given $key and $value pair
     * if $key is null the value is the array key
     *
     * @param  string $value
     * @param  string $key
     * @return array
     */
    public function lists($value, $key = null)
    {
        $args = [$value, $key];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    /**
     *
     * get paginate collection
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator|null
     */
    public function paginate($perPage = 20, $columns = array('*'))
    {
        $args = [$perPage, $columns];
        return $this->_cachedOrFromParent(__FUNCTION__, $args);
    }

    //==================================================
    //=============== end override methods =============

    /**
     * clearing the cache of this repository
     */
    public function flushCache()
    {
        $this->getCache()->flush();
        return $this;
    }

    /**
     *
     * get the data from cache or from current the parent method
     *
     * @param $method string the methode name of the parent method
     * @param $args array the method parameters
     * @return mixed
     */
    protected function _cachedOrFromParent($method, $args) {
        $cacheKey = $this->getCacheKey([get_called_class() . '::' . $method, serialize($args)]);
        if($this->_cache->has($cacheKey)) {
            $ret = $this->_cache->get($cacheKey);
        } else {
            $ret = call_user_func_array(array('parent', $method), $args);
            $this->_cache->forever($cacheKey, $ret);
        }
        return $ret;
    }

    /**
     *
     * unset form fields which cannot be save from Eloquent
     *
     * @param array $data
     * @return array
     */
    protected function _clearFromFormHelpers(array $data)
    {
        if (isset($data['_token'])) {
            unset($data['_token']);
        }
        if (isset($data['_method'])) {
            unset($data['_method']);
        }
        return $data;
    }

}