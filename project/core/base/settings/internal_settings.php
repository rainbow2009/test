<?php
defined('VG_ACCESS') or die('Access denied');

const TEMPLATE = "templates/default/";
const ADMIN_TEMPLATE = "admin/view/";
const UPLOAD_DIR = "userfiles/";

const COOKIE_VERSION = '1.0.0';
const CRYPT_KEY = 'hWmZq4t7w!z%C*F-PeShVmYq3t6w9z$C+KaPdSgVkYp3s6v9D*G-KaNdRgUkXp2s!z%C*F-JaNcRfUjXt6w9z$C&F)J@McQfYp3s6v9y$B&E)H@MgUkXp2s5v8y/B?E(NcRfUjXn2r5u8x/A)H@McQfTjWnZr4u7';
const COOKIE_TIME = 60;
const BLOCK_TIME = 3;

const QTY = 8;
const QTY_LINKS = 3;

const ADMIN_CSS_JS = [
    'styles' => ['css/main.css'],
    'scripts' => ['js/frameworkfunctions.js','js/script.js']
];

const USER_CSS_JS = [
    'styles' => ['css/style.css'],
    'scripts' => []
];
use base\exceptions\RouteException;


function autoloadMainClasses($class_name)
{
    $class_name = str_replace('\\', '/', $class_name);
    if (!@include_once $class_name . '.php') {
        throw new RouteException('bad file name ' . $class_name);
    }
}

spl_autoload_register('autoloadMainClasses');
