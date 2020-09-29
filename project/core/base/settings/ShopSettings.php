<?php


namespace base\settings;

use base\controller\traits\Singletone;

class ShopSettings
{
    use Singletone;

    private $baseSettings;

    private $routes = [
        'plugins' => [
            'hrUrl' => false,
            'dir' => false,
            'routes' => [

            ],
        ],

    ];

    private $templateArr = [
        'text' => ['short', 'price'],
        'textArea' => ['goods_content'],
    ];


    static public function get($property)
    {
        return self::getInstance()->$property;
    }

    static private function getInstance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }

        self::instance()->baseSettings = Settings::instance();
        $baseProperties = self::$_instance->baseSettings->clueProperties(get_class());
        self::$_instance->setProperty($baseProperties);

        return self::$_instance;
    }

    protected function setProperty($properties)
    {
        if ($properties) {
            foreach ($properties as $name => $property) {
                $this->$name = $property;
            }
        }
    }
}