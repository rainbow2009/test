<?php


namespace base\controller;


use base\controller\traits\BaseTrait;
use base\controller\traits\Singleton;

class BaseRoute
{

    use Singleton, BaseTrait;

    public static function routeDirection()
    {

        if (self::instance()->isAjax()) {
            exit((new BaseAjax())->route());
        }
        RouteController::instance()->route();

    }

}