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

<<<<<<< HEAD
            case 'r' :
=======
            case 'e' :
>>>>>>> 3ee08f3029708644d629f25eee41495b6e5fbb4d
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

<<<<<<< HEAD
/**
 * @param $table bd table
 * @param array set
 * 'fields' => ['id','name]
 * 'where' => ['fio', 'name','surname']
 * 'operand' =>['=', '<>']
 * 'condition' => ['AND']
 * 'oreder' => ['fio', 'name']
 * 'order_direction' => ['ASC','DESC']
 * 'limit' => '1'
 */


    final public function get($table, $set=[]){

        $fields = $this->createFields($table,$set);
        $where = $this ->createWhere($table, $set);

        $join_arr = $this->createJoin($table, $set);

        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];


        $order = $this->createOrder($table, $set);

        $limit = $set['limit'] ? $set['limit']:'';
        
        $query = "SELECT $fields FROM $table $join $where $order $limit";

        return $this->query($query);
    }

    final protected function createFields($table =false, $set){

        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) 
        ? $set['fields'] : '*';
$table = $table ? $table . '.' : '';
$fields = '';
foreach($set['fields'] as $field){
    $fields .= $table . $field .',';

    return $fields;
}

    }

    final protected function createWhere(){

    }

    final protected function createJoin(){

    }

    final protected function createOrder($table =false, $set){

        $table = $table['order'] ? $table['order'] .'.' : "";

        $order_by = '';
        if(is_array($set['order']) && !empty($set['order'])){
$set['order_direction'] = (is_array($set['order_direction']) && !empty($set['order_direction']))
    ? $set['order_direction'] : "ASC";
       
        
        $order_by = "ORDER BY ";
        $direct_count = 0;

        foreach($set['order'] as $order){
            if($set['order_direction'][$direct_count]){
                $order_direction = strtoupper($set['order_direction'][$direct_count]);
                $direct_count ++;
            }else{
                $order_direction = strtoupper($set['order_direction'][$direct_count -1]);
            }
            $order_by .= $table . $order. ' '. $order_direction . ',';
        }
$order_by = trim($order_by, ',');
}
return $order_by;
    }
=======
>>>>>>> 3ee08f3029708644d629f25eee41495b6e5fbb4d

}