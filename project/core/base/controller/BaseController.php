<?php

namespace base\controller;

use base\exceptions\RouteException;
use Exception;
use http\Exception\RuntimeException;

abstract class BaseController
{
    protected $page;
    protected $errors;

    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;

    public function route()
    {

        $controller = str_replace('/', '\\', $this->controller);

        try {
            $object = new \ReflectionMethod($controller, 'request');

            $args = [
                'parameters' => $this->parameters,
                'outputMethod' => $this->outputMethod,
                'inputMethod' => $this->inputMethod
            ];

            $object->invoke(new $controller, $args);

        } catch (\ReflectionException $e) {

            throw new  RouteException($e->getMessage());

        }
    }

    public function request($args)
    {
        $this->parameters = $args['parameters'];
        $outputData = $args['outputMethod'];
        $inputData = $args['inputMethod'];
        $this->$inputData();
        $this->page = $this->$outputData();

        if ($this->errors) {
            $this->writeLog();
        }

        $this->getpage();
    }

    protected function render($path = '', $parameters = [])
    {
        extract($parameters);
        if (!$path) {
            $path = TEMPLATE . explode('controller', strtolower((new \ReflectionClass($this))->getShortName()))[0];
        }

        ob_start();
        if (!@include_once($path . '.php')) {
            throw new RouteException('не найден шаблон  ' . $path);
        }

        return ob_get_clean();
    }

    protected function getPage()
    {
        exit($this->page);
    }

}
