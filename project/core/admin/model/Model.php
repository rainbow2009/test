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

public function showForeignKeys($table, $key = false){

$db =DB_NAME;
dd($arrays = func_get_args());
if($key) $where = "AND COLUM_NAME = '$key' LIMIT 1";

    $query = "SELECT COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND
   CONSTRAINT_NAME <> 'PRIMERY' AND REFERENCED_TABLE_NAME is not null  $where";

  return $this->query($query);
}

}

