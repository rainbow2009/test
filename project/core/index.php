<?php
define("VG_ACCESS", true);
header('Content-Type:text/html;charset=utf-8');
session_start();

use base\exceptions\RouteException;
use  base\exceptions\DbException;
use base\controller\BaseRoute;

require_once('libraries/helpers.php');
require_once("config.php");
require_once("base/settings/internal_settings.php");

if($_POST) exit('dsad   ');
//dd($_POST,$_GET);
try {

    BaseRoute::routeDirection();

} catch (RouteException $e) {
    exit($e->getMessage());
} catch (DbException $e) {
    exit($e->getMessage());
}