<?php


namespace base\settings;


class ShopSettings
{

    use BaseSettings;

    private  $routes = [
        'plugins' => [
            'hrUrl' => false,
            'dir' => false,
            'routes' => [

            ],
        ],

    ];

    private  $templateArr = [
        'text' => ['short', 'price'],
        'textArea' => ['goods_content'],
    ];


}