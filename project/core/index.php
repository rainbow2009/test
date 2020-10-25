<?php
define("VG_ACCESS", true);
header('Content-Type:text/html;charset=utf-8');
session_start();

use base\controller\RouteController;
use base\exceptions\RouteException;
use  base\exceptions\DbException;


require_once('libraries/helpers.php');
require_once("config.php");
require_once("base/settings/internal_settings.php");

try {

    RouteController::instance()->route();

} catch (RouteException $e) {
    exit($e->getMessage());
} catch (DbException $e) {
    exit($e->getMessage());
}