<?php


namespace base\settings;


class ShopSettings
{

use BaseSettings;

    private array $routes = [
        'plugins' => [
            'hrUrl' => false,
            'dir' => false,
            'routes' => [

            ],
        ],

    ];

    private array $templateArr = [
        'text' => ['short', 'price'],
        'textArea' => ['goods_content'],
    ];



}