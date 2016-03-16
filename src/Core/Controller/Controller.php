<?php
/**
 * Copyright (c) 2016. Stefan Riedel <sr_at_srit83.de>
 * This software is licensed in gplv3 http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 19.01.16
 * Time: 14:24
 */

namespace Lava83\LavaProto\Core\Controller;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\Factory;
use Lava83\LavaProto\View\View;
use Prettus\Repository\Contracts\RepositoryInterface;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $_cookies = [];

    protected $_controller;

    protected $_method;

    /**
     * @var View
     */
    protected $_view;

    /**
     * @var Response
     */
    protected $_response;

    /**
     * @var RepositoryInterface
     */
    protected $_repository;

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Constructor is fired two events Controller_Init_Pre and CalledController_Init_Pre
     */
    public function __construct() {
        $this->_view = view();
        $this->_controller = get_called_class();
        $args = [
            'subject' => $this
        ];
        notify(__CLASS__ . '_Init_Post', $args);
        notify($this->_controller . '_Init_Post', $args);
    }

    /**
     * Execute an action on the controller.
     *
     * And is fired pre and post events on controller
     *
     * @param  string $method
     * @param  array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $this->_method = $method;
        $args = [
            'subject' => $this
        ];
        notify(__CLASS__ . '_Pre', $args);
        notify($this->_controller . '_Pre', $args);
        notify($this->_controller . '_Pre::' . $this->_method, $args);

        $this->_response = parent::callAction($method, $parameters);

        notify(__CLASS__ . '_Post', $args);
        notify($this->_controller . '_Post', $args);
        notify($this->_controller . '_Post::' . $this->_method, $args);

        return $this->_response;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     *
     * build a view response
     *
     * @param $sViewName
     * @param array $aData
     * @return \Illuminate\Http\Response
     */
    public function view($sViewName, array $aData = [])
    {
        $oView = $this->_view->make($sViewName, $aData);
        $oResponse = \Response::make($oView);
        $this->_cookies = [];
        foreach ($this->_cookies as $oCookie) {
            $oResponse->withCookie($oCookie);
        }
        return $oResponse;
    }

}