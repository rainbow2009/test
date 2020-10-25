<?php


namespace admin\controller;


use base\settings\Settings;

class ShowController extends BaseAdmin
{

    protected function inputData()
    {

        if (!$this->userId) {
            $this->execBase();
        }

        $this->createTableData();
        $this->createData();
        return $this->expansion(get_defined_vars());
    }


    protected function createData($arr = [])
    {
        $fields = [];
        $order = [];
        $order_direction = [];

        if (!$this->columns['id_row']) {
            return $this->data = [];
        }
        $fields[] = $this->columns['id_row'] . " as id";
        if ($this->columns['name']) {
            $fields['name'] = 'name';
        }
        if ($this->columns['img']) {
            $fields['img'] = 'img';
        }
        if (count($fields) < 3) {
            foreach ($this->columns as $key => $item) {
                if (!$fields['name'] && strpos($key, 'name') !== false) {
                    $fields['name'] = $key . ' as name';
                }
                if (!$fields['img'] && strpos($key, 'img') === 0) {
                    $fields['img'] = $key . ' as img';
                }

            }
        }
        if (isset($arr['fields'])) {
            if (is_array($arr['fields'])) {
                $fields = Settings::instance()->arrayMergeRecurcive($fields, $arr['fields']);
            } else {
                $fields[] = $arr['fields'];
            }

        }
        if ($this->columns['parents_id']) {
            if (!in_array('parent_id', $fields)) {
                $fields[] = 'parent_id';
            }
            $order[] = 'parent_id';
        }
        if ($this->columns['menu_position']) {
            $order[] = 'menu_position';
        } elseif ($this->columns['date']) {
            if ($order) {
                $order_direction = ['ASC', 'DESC'];
            } else {
                $order_direction[] = "DESC";
            }
            $order[] = 'date';
        }
        if (isset($arr['order'])) {
            if (is_array($arr['order'])) {
                $order = Settings::instance()->arrayMergeRecurcive($order, $arr['order']);
            } else {
                $order[] = $arr['order'];
            }
        }
        if (isset($arr['order_direction'])) {
            if (is_array($arr['order_direction'])) {
                $order_direction = Settings::instance()->arrayMergeRecurcive($order_direction,
                    $arr['order_direction']);
            } else {
                $order_direction[] = $arr['order_direction'];
            }
        }


        $this->data = $this->model->get($this->table, [
            'fields' => $fields,
            'order' => $order,
            'order_direction' => $order_direction
        ]);

    }
}