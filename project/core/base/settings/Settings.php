<?php

namespace base\settings;

use base\controller\traits\Singleton;

class Settings
{
    use Singleton;

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
        ],
    ];
    private $defaultTable = 'goods';

    private $templateArr = [
        'text' => ['name'],
        'textarea' => ['content','tags'],
        'select' => ['menu_position', 'parent_id'],
        'checkboxlist' => ['filters'],
        'radio' => ['visible'],
        'img' => ['img'],
        'gallery_img' => ['gallery_img']
    ];

    private $fileTemplates = ['img', 'gallery_img'];

    private $formTemplates = PATH . 'admin/view/include/form_templates/';

    private $translate = [
        'name' => ['Название', 'Не более 100 символов'],
        'content' => ['Контент', 'Не более 70 символов'],
        'menu_position' => ['menu Position']
    ];

    private $radio = [
        'visible' => ['Нет', 'Да', 'default' => 'Да']
    ];

    private $rootItems = [
        'name' => 'корневая',
        'tables' => ['goods', 'filters','articles']
    ];

    private $manyToMany = [
        'goods_filters' => [
            'goods',
            'filters',
//            'type' => 'root'
        ] //'type' => 'child' || 'root'
    ];

    private $blockNeedle = [
        'vg-rows' => ['tags'],
        'vg-img' => ['img'],
        'vg-content' => ['content']
    ];

    private $validation = [
        'name' => ['empty' => true, 'trim' => true],
        'content' => ['empty' => true, 'trim' => true, 'count' => 70],
        'price' => ['int' => true],
        'login' => ['empty' => true, 'trim' => true],
        'password' => ['crypt' => true],
        'keywords' => ['count' => 4, 'trim' => true],
        'description' => ['count' => 160, 'trim' => true],
        'tags '=> ['count' => 255, 'trim' => true],
    ];

    private $projectTable = [
        'articles' => [
            'name' => 'статьи',
        ],
        'pages' => [
            'name' => 'страницы',
        ],
        'filters' => [
            'name' => 'filter',
            'img' => 'pages.png'
        ],
        'goods' => [
            'name' => 'goods',
        ]
    ];

    private $expansion = 'admin/expansion/';

    private $messages = 'base/messages/';

    static public function get($property)
    {
        return self::instance()->$property;
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
