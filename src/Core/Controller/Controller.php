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

    protected $cookies = [];

    protected $controller;

    protected $method;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Constructor is fired two events Controller_Init_Pre and CalledController_Init_Pre
     */
    public function __construct()
    {
        $this->view = view();
        $this->controller = get_called_class();
        $args = [
            'subject' => $this
        ];
        notify(__CLASS__ . '_Init_Post', $args);
        notify($this->controller . '_Init_Post', $args);
        \JavaScript::put([
            'baseurl' => url('')
        ]);
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
        $this->beforeCallAction($method);

        $this->response = parent::callAction($method, $parameters);

        $this->afterCallAction($method);

        return $this->response;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
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
        $oView = $this->view->make($sViewName, $aData);
        $oResponse = \Response::make($oView);
        $this->cookies = [];
        foreach ($this->cookies as $oCookie) {
            $oResponse->withCookie($oCookie);
        }
        return $oResponse;
    }

    /**
     * @param $method
     */
    protected function beforeCallAction($method)
    {
        $this->method = $method;
        $args = [
            'subject' => $this
        ];
        notify(__CLASS__ . '_Pre', $args);
        notify($this->controller . '_Pre', $args);
        notify($this->controller . '_Pre::' . $this->method, $args);
    }

    /**
     * @param $method
     */
    protected function afterCallAction($method)
    {
        $args = [
            'subject' => $this
        ];
        notify(__CLASS__ . '_Post', $args);
        notify($this->controller . '_Post', $args);
        notify($this->controller . '_Post::' . $method, $args);
    }
}
