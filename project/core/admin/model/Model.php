<?php


namespace admin\model;

use base\controller\traits\Singleton;
use base\model\BaseModel;

class Model extends BaseModel
{
    use Singleton;

    private function __construct()
    {
        $this->connect();
    }

    public function showForeignKeys($table, $key = false)
    {

        $db = DB_NAME;

        if ($key) {
            $where = "AND COLUMN_NAME =  '$key'  LIMIT 1";
        }

        $query = "SELECT COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND
   CONSTRAINT_NAME <> 'PRIMERY' AND REFERENCED_TABLE_NAME is not null  $where";
        return $this->query($query);
    }

    public function updateMenuPosition($table, $row, $where, $end_pos, $update_rows = [])
    {
        if ($update_rows && isset($update_rows['where'])) {

            $update_rows['operand'] = isset($update_rows['operand']) ? $update_rows['operand'] :['='];

            if($where){

                $old_data =$this->get($table, [
                    'fields' => [$update_rows['where'],$row],
                    'where' => $where
                ])[0];
            }

        } else {
            if ($where) {
                $start_pos = $this->get($table, [
                    'fields' => [$row],
                    'where' => $where
                ])[0][$row];
            } else {
                $start_pos = $this->get($table, [
                        'fields' => ['COUNT(*) as count'],
                        'no_concat' => true
                    ])[0]['count'] + 1;
            }
        }
    }

}

