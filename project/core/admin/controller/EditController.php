<?php


namespace admin\controller;

use base\exceptions\RouteException;
use base\settings\Settings;


class EditController extends BaseAdmin
{
    protected $action = 'edit';

    protected function inputData()
    {

        if (!$this->userId) {
            $this->execBase();
        }

        $this->checkPost();

        $this->createTableData();

        $this->createForeignData();

        $this->createData();


        $this->createMenuPosition();


        $this->createRadio();

        $this->createOutputData();

        $this->createManyToMany();

        $this->template = ADMIN_TEMPLATE . 'add';

        return $this->expansion();

    }

    protected function createData()
    {
        $id = $this->clearNum($this->parameters[$this->table]);
        if(!$id) throw new RouteException('Не корректный идентификатор -'. $id .
            ' при редактировании таблицы - '.$this->table);
        $this->data = $this->model->get($this->table,[
            'where' => [$this->columns['id_row'] =>$id]
        ]);

        $this->data && $this->data = $this->data[0];
    }

    protected function checkOldAlias($id)
    {

        $tables = $this->model->showTables();

        if (in_array('old_alias', $tables)) {
            $old_alias = $this->model->get($this->table, [
                'fields' => ['alias'],
                'where' => [$this->columns['id_row'] => $id]
            ])[0]['alias'];

            if ($old_alias && $old_alias !== $_POST['alias']) {
                $this->model->delet('old_alias', [
                    'where' => ['alias' => $old_alias, 'table_name' => $this->table]
                ]);
                $this->model->delet('old_alias', [
                    'where' => ['alias' => $_POST['alias'], 'table_name' => $this->table]
                ]);

                $this->model->add('old_alias', [
                    'fields' => ['alias' => $old_alias, 'table_name' => $this->table, 'table_id' => $id],
                ]);
            }

        }


    }

    protected function createForeignProperty($arr, $rootItems)
    {

        if (in_array($this->table, $rootItems['tables'])) {
            $this->foreignData[$arr['COLUMN_NAME']][0]['id'] = 'NULL';
            $this->foreignData[$arr['COLUMN_NAME']][0]['name'] = $rootItems['name'];
        }

        $orderData = $this->createOrderData($arr['REFERENCED_TABLE_NAME']);

        if ($this->data) {
            if ($arr['REFERENCED_TABLE_NAME'] === $this->table) {
                $where[$this->columns['id_row']] = $this->data[$this->columns['id_row']];
                $operand[] = '<>';
            }
        }

        $foreign = $this->model->get($arr['REFERENCED_TABLE_NAME'], [
            'fields' => [$arr['REFERENCED_COLUMN_NAME'] . ' as id', $orderData['name'], $orderData['parent_id']],
            'where' => $where,
            'operand' => $operand,
            'order' => $orderData['order']
        ]);

        if ($foreign) {

            if ($this->foreignData[$arr['COLUMN_NAME']]) {
                foreach ($foreign as $val) {
                    $this->foreignData[$arr['COLUMN_NAME']][] = $val;
                }
            } else $this->foreignData[$arr['COLUMN_NAME']] = $foreign;

        }
    }

    protected function createForeignData($setings = false)
    {
        if (!$setings) $setings = Settings::instance();

        $rootItems = $setings::get('rootItems');

        $keys = $this->model->showForeignKeys($this->table);
        if ($keys) {

            foreach ($keys as $item) {
                $this->createForeignProperty($item, $rootItems);
            }

        } elseif ($this->columns['parent_id']) {

            $arr['COLUMN_NAME'] = 'parent_id';
            $arr['REFERENCED_COLUMN_NAME'] = $this->columns['id_row'];
            $arr['REFERENCED_TABLE_NAME'] = $this->table;

            $this->createForeignProperty($arr, $rootItems);
        }
    }

    protected function createMenuPosition($setings = false)
    {

        if ($this->columns['menu_position']) {

            if (!$setings) $setings = Settings::instance();
            $rootItems = $setings::get('rootItems');

            if ($this->columns['parent_id']) {

                if (in_array($this->table, $rootItems['tables'])) {

                    $where = 'parent_id IS NULL OR parent_id = 0';
                } else {
                    $parent = $this->model->showForeignKeys($this->table, 'parent_id')[0];


                    if ($parent) {

                        if ($this->table === $parent['REFERENCED_TABLE_NAME']) {
                            $where = 'parent_id IS NULL OR parent_id = 0';
                        } else {
                            $columns = $this->model->showColumns($parent['REFERENCED_TABLE_NAME']);
                            if ($columns['parent_id']) {
                                $order[] = 'parent_id';
                            } else {
                                $order[] = $parent['REFERENCED_COLUMN_NAME'];
                            }
                            $id = $this->model->get($parent['REFERENCED_TABLE_NAME'], [
                                'fields' => [$parent['REFERENCED_COLUMN_NAME']],
                                'order' => $order,
                                'limit' => '1'
                            ])[0][$parent['REFERENCED_COLUMN_NAME']];

                            if ($id) {
                                $where = ['parent_id' => $id];
                            }
                        }
                    } else {
                        $where = 'parent_id IS NULL OR parent_id = 0';
                    }
                }
            }

            $menu_pos = $this->model->get($this->table, [
                    'fields' => ['COUNT(*) as count'],
                    'where' => $where,
                    'no_concat' => true
                ])[0]['count'] ;

            for ($i = 1; $i <= $menu_pos; $i++) {
                $this->foreignData['menu_position'][$i - 1]['id'] = $i;
                $this->foreignData['menu_position'][$i - 1]['name'] = $i;
            }
        }


        return;
    }


}