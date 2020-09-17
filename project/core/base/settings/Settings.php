<?php

namespace base\settings;

class Settings
{
    static private $_instance;

    private $routes = [
        'admin' => [
            'alias' => 'sudo',
            'path' => 'admin/controller/',
            'hrUrl' => false,

        ],
        'settings' => [
            'path' => 'base/settings/'
        ],
        'plugins' => [
            'path' => 'plugins/',
            'hrUrl' => false,
            'dir' => false
        ],
        'user' => [
            'path' => 'user/controller/',
            'hrUrl' => true,
            'routes' => [

            ],
        ],
        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod' => 'outputData'
        ]
    ];

    private $templateArr = [
        'text' => ['name', 'phone', 'address'],
        'textArea' => ['content', 'keywords'],
    ];


    private function __construct()
    {
    }

    private function __clone()
    {
    }

    static public function get($property)
    {
        return self::instance()->$property;
    }

    static public function instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }
        return self::$_instance = new self();
    }

    public function clueProperties($class)
    {
        $baseProperties = [];

        foreach ($this as $name => $item) {
            $property = $class::get($name);

            if (is_array($property) && is_array($item)) {
                $baseProperties[$name] = $this->arrayMergeRecurcive($this->$name, $property);
                continue;
            }
        }
        if (!$property) {
            $baseProperties[$name] = $this->$name;
        }
        return $baseProperties;
    }

    public function arrayMergeRecurcive()
    {
        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && is_array($base[$key])) {
                    $base[$key] = $this->arrayMergeRecurcive($base[$key], $value);
                } else {
                    if (is_int($key)) {
                        if (!in_array($value, $base)) {
                            array_push($base, $value);
                        }
                        continue;
                    }
                    $base[$key] = $value;
                }
            }
        }
        return $base;
    }
}
