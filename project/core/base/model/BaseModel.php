<?php


namespace base\model;

use base\controller\traits\Singletone;
use base\exceptions\DbException;
use \mysqli as DB;

class BaseModel
{

    use Singletone;

    protected $db;

    private function __construct()
    {

        $this->db = new DB(HOST, USER, PASS, DB_NAME);

        if ($this->db->connect_error) {
            throw new DbException('Ошибка подключния к БД: ' .
                $this->db->connect_errno . ': ' . $this->db->connect_error);
        }

        $this->db->query("SET NAMES UTF8");
    }


    final public function query($query, $crud = 'r', $return_id = false)
    {
        $result = $this->db->query($query);
        if ($this->db->affected_rows === -1) {
            throw  new DbException('Ощибка в SQL запросе:' .
                $query . '-' . $this->db->errno . '  ' . $this->db->error);
        }


        switch ($crud) {

            case 'e' :
                if ($result->num_rows) {
                    $res = [];
                    for ($i = 0; $i < $result->num_rows; $i++) {
                        $res[] = $result->fetch_assoc();
                    }
                    return $res;
                }
                return false;
            case 'c':
                if ($return_id) {
                    return $this->db->insert_id;
                }
                return true;
            default:
                return true;

        }

    }


}