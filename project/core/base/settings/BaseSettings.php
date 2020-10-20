<?php


namespace base\settings;

use base\settings\Settings;
use base\controller\traits\Singleton;

trait BaseSettings
{
    use Singleton{
        instance as SingletonInstance;
    }

    private array $baseSettings;

    static public function get($property)
    {
        return self::instance()->$property;
    }

    static private function instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }

        self::SingletonInstance()->baseSettings = Settings::instance();
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