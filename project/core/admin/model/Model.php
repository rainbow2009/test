<?php


namespace admin\model;

use base\controller\traits\Singletone;
use base\model\BaseModel;

class Model extends BaseModel
{
use Singletone;

private function __construct(){
    $this->connect();
}
}

