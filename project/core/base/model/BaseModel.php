<?php


namespace base\model;

use base\controller\traits\Singletone;
use base\exceptions\DbException;
use \mysqli as DB;

class BaseModel extends BaseModelMethods
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

    /**
     * @param $query
     * @param string $crud $crud = r Select /  c - insert / u - update/ d -delete
     * @param false $return_id
     * @return array|bool|mixed
     * @throws DbException
     */

    final public function query($query, $crud = 'r', $return_id = false)
    {
        $result = $this->db->query($query);
        if ($this->db->affected_rows === -1) {
            throw  new DbException('Ощибка в SQL запросе:' .
                $query . '-' . $this->db->errno . '  ' . $this->db->error);
        }


        switch ($crud) {

            case 'r' :
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

    /**
     * @param $table db table
     * @param array set
     * 'fields' => ['id','name]
     * 'where' => ['fio', 'name','surname']
     * 'operand' =>['=', '<>']
     * 'condition' => ['AND']
     * 'oreder' => ['fio', 'name']
     * 'order_direction' => ['ASC','DESC']
     * 'limit' => '1'
     *  * 'join' =>[
     *   [
     *  'table' => 'join_table1',
     *   'fields' => ['id as j_id','name as j_name'],
     *   'type' =>'left',
     *  'where' => [ 'name' => 'sasha'],
     *   'operand' =>['='],
     *   'condition' => ["OR"],
     *   'on' => [
     *    'table' => 'teachers',
     *    'fields' => ['id', 'parent_id']
     *   ]
     * ],
     * 'join_table2' =>[
     *     'table' => 'join_table2',
     *     'fields' => ['id as j2_id','name as j2_name'],
     *     'type' =>'left',
     *     'where' => [ 'name' => 'sasha'],
     *     'operand' =>['<>'],
     *   'condition' => ["AND"],
     *     'on' =>['id', 'parent_id']
     * ]
     */


    final public function get($table, $set = [])
    {

        $fields = $this->createFields($set, $table);


        $where = $this->createWhere($set, $table);
        $join_arr = $this->createJoin($set, $table);

        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];


        $order = $this->createOrder($set, $table);
        $limit = $set['limit'] ? "LIMIT " . $set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";

        return $this->query($query);
    }

    final public function add($table, $set = [])
    {
        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
        $set['files'] = (is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;
        if (!$set['files'] && !$set['fields']) {
            return false;
        }

        $set['return_id'] = $set['return_id'] ? true : false;
        $set['except'] = (is_array($set['except']) && !empty($set['except'])) ? $set['except'] : false;

        $insert_arr = $this->createInsert($set['fields'], $set['files'], $set['except']);

        if ($insert_arr) {
            $query = "INSERT INTO $table({$insert_arr['fields']}) VALUE  ({$insert_arr['values']})";

            return $this->query($query, 'c', $set['return_id']);
        }
        return false;
    }


    final public function update($table, $set = [])
    {

        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
        $set['files'] = (is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;

        if (!$set['files'] && !$set['fields']) {
            return false;
        }


        $set['except'] = (is_array($set['except']) && !empty($set['except'])) ? $set['except'] : false;

        if (!$set['all_rows']) {

            if ($set['where']) {
                $where = $this->createWhere($set);
            } else {

                $columns = $this->showColumns($table);

                if (!$columns) {
                    return false;
                }

                if ($columns['id_row'] && $set['fields'][$columns['id_row']]) {
                    $where = "WHERE " . $columns['id_row'] . '=' . $set['fields'][$columns['id_row']];
                    unset($set['fields'][$columns['id_row']]);
                }
            }

        }

        $update = $this->createUpdate($set['fields'], $set['files'], $set['except']);
        $query = "UPDATE $table SET $update $where";

        return $this->query($query, 'u');

    }

    /**
     * @param $table db table
     * @param array set
     * 'fields' => ['id','name]
     * 'where' => ['fio', 'name','surname']
     * 'operand' =>['=', '<>']
     * 'condition' => ['AND']
     *  * 'join' =>[
     *   [
     *  'table' => 'join_table1',
     *   'fields' => ['id as j_id','name as j_name'],
     *   'type' =>'left',
     *  'where' => [ 'name' => 'sasha'],
     *   'operand' =>['='],
     *   'condition' => ["OR"],
     *   'on' => [
     *    'table' => 'teachers',
     *    'fields' => ['id', 'parent_id']
     *   ]
     * ],
     * 'join_table2' =>[
     *     'table' => 'join_table2',
     *     'fields' => ['id as j2_id','name as j2_name'],
     *     'type' =>'left',
     *     'where' => [ 'name' => 'sasha'],
     *     'operand' =>['<>'],
     *   'condition' => ["AND"],
     *     'on' =>['id', 'parent_id']
     * ]
     */

    final public function delete($table, $set)
    {
        $table = trim($table);
        $where = $this->createWhere($set, $table);
        $columns = $this->showColumns($table);
        if (!$columns) return false;

        if(is_array($set['fields']) && !empty($set['fields'])){

            if($columns['id_row']){
                $key = array_search($columns['id_row'],$set['fields']);
                if ($key !== false) unset($set['fields'][$key]);
            }

            $fields =[];
            foreach ($set['fields'] as $field){
                $fields[$field] = $columns[$field]['Default'];
            }

            $update = $this->createUpdate($fields,false,false);

            $query = "UPDATE $table SET $update $where";

        }

    }

    final public function showColumns($table)
    {

        $query = "SHOW COLUMNS FROM $table";

        $res = $this->query($query);

        $columns = [];

        foreach ($res as $row) {
            $columns[$row['Field']] = $row;
            if ($row['Key'] === "PRI") {
                $columns['id_row'] = $row['Field'];
            }
        }

        return $columns;

    }


}