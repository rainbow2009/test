<?php


namespace admin\controller;


use admin\model\Model;
use base\controller\BaseController;
use base\exceptions\RouteException;
use base\settings\Settings;
use libraries\FileEdit;

abstract class BaseAdmin extends BaseController
{
    protected $model;

    protected $menu;
    protected $title;

    protected $fileArr;
    protected $alias;

    protected $table;
    protected $columns;
    protected $foreignData;

    protected $adminPath;

    protected $translate;

    protected $messages;
    protected $settings;

    protected $blocks = [];

    protected $templateArr;
    protected $formTemplates;
    protected $noDelete;

    protected function inputData()
    {

        $this->init(true);
        $this->title = 'VG engine';
        if (!$this->model) {
            $this->model = Model::instance();
        }
        if (!$this->menu) {
            $this->menu = Settings::get('projectTable');
        }
        if (!$this->adminPath) {
            $this->adminPath = PATH . Settings::get('routes')['admin']['alias'] . '/';
        }
        if (!$this->templateArr) {
            $this->templateArr = Settings::get('templateArr');
        }
        if (!$this->formTemplates) {
            $this->formTemplates = Settings::get('formTemplates');
        }

        if (!$this->messages) {
            $this->messages = include $_SERVER['DOCUMENT_ROOT'] . PATH . Settings::get('messages') . 'informationMessages.php';
        }
        $this->sendNoCacheHeaders();

    }

    protected function outputData()
    {

        if (!$this->content) {
            $args = func_get_arg(0);
            $vars = $args ? $args : [];
            // if(!$this->template) $this->template =ADMIN_TEMPLATE.'show';

            $this->content = $this->render($this->template, $vars);
        }

        $this->header = $this->render(ADMIN_TEMPLATE . 'include/header');
        $this->footer = $this->render(ADMIN_TEMPLATE . 'include/footer');

        return $this->render(ADMIN_TEMPLATE . 'layout/default');

    }

    protected function sendNoCacheHeaders()
    {

        header('Last-Modified: ' . gmdate("D, d m Y H:i:s") . " GMT");
        header('Cache-Control: no-cache, must-revalidate');
        header('Cache-Control: max-age=0');
        header('Cache-Control: post-chek=0,pre-chek=0');


    }

    protected function execBase()
    {
        self::inputData();
    }

    protected function createTableData($settings = false)
    {
        if (!$this->table) {
            if ($this->parameters) {
                $this->table = array_keys($this->parameters)[0];
            } else {
                if (!$settings) {
                    $settings = Settings::instance();
                }
                $this->table = $settings::get('defaultTable');
            }
        }
        $this->columns = $this->model->showColumns($this->table);

        if (!$this->columns) {
            new RouteException('bad fields name at table' . $this->table, 2);
        }

    }


    protected function expansion($args = [], $settings = false)
    {

        $filename = explode('_', $this->table);
        $className = '';

        foreach ($filename as $item) {
            $className .= ucfirst($item);
        }

        if (!$settings) {
            $path = Settings::get('expansion');
        } elseif (is_object($settings)) {
            $path = $settings::get('expansion');
        } else {
            $path = $settings;
        }

        $class = $path . $className . 'Expansion';

        if (is_readable($_SERVER['DOCUMENT_ROOT'] . PATH . $class . '.php')) {

            $class = str_replace('/', '\\', $class);
            $exp = $class::instance();

            foreach ($this as $name => $val) {
                $exp->$name = &$this->$name;
            }
            return $res = $exp->expansion($args);

        } else {

            $file = $_SERVER['DOCUMENT_ROOT'] . PATH . $path . $this->table . '.php';
            extract($args);

            if (is_readable($file)) {
                return include $file;
            }

            return false;

        }
    }

    protected function createOutputData($settings = false)
    {

        if (!$settings) {
            $settings = Settings::instance();
        }

        $blocks = $settings::get('blockNeedle');
        $this->translate = $settings::get('translate');
        if (!$blocks || !is_array($blocks)) {
            foreach ($this->columns as $name => $item) {
                if ($name === 'id_row') {
                    continue;
                }
                if (!$this->translate[$name]) {
                    $this->translate[$name][] = $name;
                }
                $this->blocks[0][] = $name;

            }
            return;
        }

        $default = array_keys($blocks)[0];

        foreach ($this->columns as $name => $item) {

            if ($name === 'id_row') {
                continue;
            }

            $insert = false;

            foreach ($blocks as $block => $val) {
                if (!array_key_exists($block, $this->blocks)) {
                    $this->blocks[$block] = [];
                }
                if (in_array($name, $val)) {
                    $this->blocks[$block][] = $name;
                    $insert = true;
                    break;
                }
            }
            if (!$insert) {
                $this->blocks[$default][] = $name;
            }
            if (!$this->translate[$name]) {
                $this->translate[$name][] = $name;
            }
        }
    }

    protected function createRadio($settings = false)
    {
        if (!$settings) {
            $settings = Settings::instance();
        }
        $radio = $settings::get('radio');

        if ($radio) {
            foreach ($this->columns as $name => $val) {
                if ($radio[$name]) {
                    $this->foreignData[$name] = $radio[$name];
                }
            }

        }
    }

    protected function checkPost($settings = false)
    {

        if ($this->isPost()) {
            $this->clearPostFields($settings);
            $this->table = $this->clearStr($_POST['table']);
            unset($_POST['table']);
            if ($this->table) {
                $this->createTableData($settings);
                $this->editData();
            }
        }
    }

    protected function addSessionData($arr = [])
    {
        if (!$arr) {
            $arr = &$_POST;
        }
        foreach ($arr as $key => $item) {
            $_SESSION['res'][$key] = $item;
        }
        $this->redirect();
    }

    protected function countChar($str, $counter, $answer, $arr)
    {
        if (mb_strlen($str) > $counter) {
            $str_res = mb_str_replace('$1', $answer, $this->messages['count']);
            $str_res = mb_str_replace('$2', $counter, $str_res);
            $_SESSION['res']['answer'][] = '<div class="vg-element vg-padding-in-px" style="color: red">' . $str_res . '</div>';

            return false;
        }
        return true;
    }

    protected function emptyFields($item, $answer, $arr = false)
    {

        if (empty($item)) {
            $_SESSION['res']['answer'][] = '<div class="vg-element vg-padding-in-px" style="color: red">' . $this->messages['empty'] . ' ' . $answer . '</div>';
            return false;

        }
        return true;
    }

    protected function clearPostFields($settings, &$arr = [])
    {
        if (!$arr) {
            $arr = &$_POST;
        }
        if (!$settings) {
            $settings = Settings::instance();
        }

        $validate = $settings::get('validation');

        $id = $_POST[$this->columns['id_row']] ?: false;
        if (!$this->translate) {
            $this->translate = $settings::get('translate');
        }

        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                $this->clearPostFields($settings, $item);
            } else {
                if (is_numeric($item)) {
                    $arr[$key] = $this->clearNum($item);
                }
                if ($validate) {
                    if ($validate[$key]) {
                        if ($this->translate[$key]) {
                            $answer = $this->translate[$key][0];
                        } else {

                            $answer = $key;
                        }
                        if ($validate[$key]['crypt']) {
                            if ($id) {
                                if (empty($item)) {
                                    unset($arr[$key]);
                                    continue;
                                }
                                $arr[$key] = md5($item);
                            }
                        }

                        if ($validate[$key]['empty']) {
                            $empty = $this->emptyFields($item, $answer);
                        }
                        if ($validate[$key]['trim']) {
                            $arr[$key] = trim($item);
                        }
                        if ($validate[$key]['int']) {
                            $arr[$key] = $this->clearNum($item);
                        }

                        if ($validate[$key]['count']) {
                            $count = $this->countChar($item, $validate[$key]['count'], $answer, $arr);
                        }
                    }
                }
            }
        }

        if (isset($_SESSION['res']['answer'][0])) {
            $this->addSessionData($arr);
            exit();
        }


    }

    protected function editData($returnId = false)
    {
        $id = false;
        $method = 'add';

        if ($_POST[$this->columns['id_row']]) {
            $id = is_numeric($_POST[$this->columns['id_row']]) ?
                $this->clearNum($_POST[$this->columns['id_row']]) :
                $this->clearStr($_POST[$this->columns['id_row']]);
            if ($id) {
                $where = [$this->columns['id_row'] => $id];
                $method = 'update';
            }
        }
        foreach ($this->columns as $key => $val) {
            if ($key === 'id_row') {
                continue;
            }
            if ($val['Type'] === 'date' || $val['Type'] === 'datetime') {
                !$_POST[$key] && $_POST[$key] = 'NOW()';
            }
        }
        $this->createFile();

        if ($id && method_exists($this, 'checkFiles')) {
            $this->checkFiles($id);
        }


        $this->createAlias($id);

        $this->updateMenuPosition($id);

        $except = $this->checkExceptFields();

        $res_id = $this->model->$method($this->table, [
            'files' => $this->fileArr,
            'where' => $where,
            'return_id' => true,
            'except' => $except
        ]);
        if (!$id && $method === 'add') {
            $_POST[$this->columns['id_row']] = $res_id;
            $answerSuccess = $this->messages['addSuccess'];
            $answerFail = $this->messages['addFail'];
        } else {
            $answerSuccess = $this->messages['editSuccess'];
            $answerFail = $this->messages['editFail'];
        }

        $this->checkManyToMany();

        $this->expansion(get_defined_vars());

        $result = $this->checkAlias($_POST[$this->columns['id_row']]);

        if ($res_id) {
            $_SESSION['res']['answer'][] = '<div class="vg-element vg-padding-in-px" style="color: green">' . $answerSuccess . '</div>';
            if (!$returnId) {
                $this->redirect();
            }
            return $_POST[$this->columns['id_row']];
        } else {
            $_SESSION['res']['answer'][] = '<div class="vg-element vg-padding-in-px" style="color: red">' . $answerFail . '</div>';
            if (!$returnId) {
                $this->redirect();
            }
        }


    }

    protected function createFile()
    {
        $fileEdit = new FileEdit();
        $this->fileArr = $fileEdit->addFile();

    }

    protected function createAlias($id = false)
    {
        if ($this->columns['alias']) {
            if (!$_POST['alias']) {
                if ($_POST['name']) {
                    $alias_str = $this->clearStr($_POST['name']);
                } else {
                    foreach ($_POST as $key => $item) {
                        if (strpos($key, 'name') !== false && $item) {
                            $alias_str = $this->clearStr($item);
                            break;
                        }
                    }
                }
            } else {
                $alias_str = $_POST['alias'] = $this->clearStr($_POST['alias']);
            }

            $textModify = new \libraries\TextModify();
            $alias = $textModify->translit($alias_str);

            $where['alias'] = $alias;
            $operand[] = '=';
            if ($id) {
                $where[$this->columns['id_row']] = $id;
                $operand[] = '<>';
            }

            $res_alias = $this->model->get($this->table, [
                'fields' => ['alias'],
                'where' => $where,
                'operand' => $operand,
                'limit' => '1'
            ])[0];

            if (!$res_alias) {
                $_POST['alias'] = $alias;
            } else {
                $this->alias = $alias;
                $_POST['alias'] = '';
            }

            if ($_POST['alias'] && $id) {
                method_exists($this, 'checkOldAlias') && $this->checkOldAlias($id);
            }

        }
    }

    protected function updateMenuPosition($id = false)
    {
        if (isset($_POST['menu_position'])) {

            $where = false;
            if ($id && $this->columns['id_row']) $where = [$this->columns['id_row'] => $id];

            if (array_key_exists('parent_id', $_POST)) {
                $this->model->updateMenuPosition($this->table, 'menu_position', $where, $_POST['menu_position'], ['where' => 'parent_id']);
            } else {
                $this->model->updateMenuPosition($this->table, 'menu_position', $where, $_POST['menu_position']);
            }

        }

    }

    protected function checkExceptFields($arr = [])
    {
        $arr = $arr ?: $_POST;
        $except = [];
        if ($arr) {
            foreach ($arr as $key => $val) {
                if (!$this->columns[$key]) {
                    $except[] = $key;
                }
            }
        }
        return $except;
    }

    protected function checkAlias($id)
    {
        if ($id) {
            if ($this->alias) {
                $this->alias .= '-' . $id;
                $this->model->edit($this->table, [
                    'fields' => ['alias' => $this->alias],
                    'where' => [$this->columns['id_row'] => $id]
                ]);
                return true;
            }
        }
        return false;
    }

    protected function createOrderData($table)
    {
        $columns = $this->model->showColumns($table);

        if (!$columns) {
            throw new RouteException('table columns exist ' . $table);
        }

        $name = '';

        if ($columns['name']) {
            $order_name = $name = 'name';
        } else {
            foreach ($columns as $key => $val) {
                if (strpos($key, 'name') !== false) {
                    $order_name = $key;
                    $name = $key . ' as name';
                }
            }
            if (!$name) {
                $name = $columns['id_row'] . ' as name';
            }
        }

        $parent_id = '';
        $order = [];

        if ($columns['parent_id']) {
            $order[] = $parent_id = 'parent_id';
        }

        if ($columns['menu_position']) {
            $order[] = 'menu_position';
        } else {
            $order[] = $order_name;
        }

        return compact('name', 'parent_id', 'order', 'columns');
    }

    protected function createManyToMany($settings = false)
    {

        if (!$settings) {
            $settings = $this->settings ?: Settings::instance();
        }

        $manyToMany = $settings::get('manyToMany');
        $blocks = $settings::get('blockNeedle');

        if ($manyToMany) {
            foreach ($manyToMany as $mTable => $tables) {

                $targetKey = array_search($this->table, $tables);

                if ($targetKey !== false) {

                    $otherKey = $targetKey ? 0 : 1;

                    $checkBoxList = $settings::get('templateArr')['checkboxlist'];

                    if (!$checkBoxList || !in_array($tables[$otherKey], $checkBoxList)) {

                        continue;
                    }
                    if (!$this->translate[$tables[$otherKey]])

                        if ($settings::get('projectTable')[$tables[$otherKey]]) {
                            $this->translate[$tables[$otherKey]] = [$settings::get('projectTable')[$tables[$otherKey]]['name']];
                        }

                    $orderData = $this->createOrderData($tables[$otherKey]);

                    $insert = false;

                    if ($blocks) {

                        foreach ($blocks as $key => $val) {

                            if (in_array($tables[$otherKey], $val)) {

                                $this->blocks[$key][] = $tables[$otherKey];
                                $insert = true;
                                break;
                            }
                        }
                    }

                    if (!$insert) {
                        $this->blocks[array_keys($this->blocks)[0]][] = $tables[$otherKey];
                    }

                    $foreign = [];

                    if ($this->data) {
                        $res = $this->model->get($mTable, [
                            'fields' => [$tables[$otherKey] . '_' . $orderData['columns']['id_row']],
                            'where' => [
                                $this->table . '_' . $this->columns['id_row']
                                => $this->data[$this->columns['id_row']]
                            ]
                        ]);
                        if ($res) {
                            foreach ($res as $item) {
                                $foreign[] = $item[$tables[$otherKey] . '_' . $orderData['columns']['id_row']];
                            }
                        }

                    }
                    if (isset($tables['type'])) {

                        $data = $this->model->get($tables[$otherKey], [
                            'fields' => [
                                $orderData['columns']['id_row'] . ' as id',
                                $orderData['name'],
                                $orderData['parent_id']
                            ],
                            'order' => $orderData['order']
                        ]);

                        if ($data) {
                            $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['name'] = 'Select';
                            foreach ($data as $val) {

                                if ($tables['type'] === 'root' && $orderData['parent_id']) {

                                    if ($val[$orderData['parent_id']] === null) {
                                        $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $val;
                                    }

                                } elseif ($tables['type'] === 'child' && $orderData['parent_id']) {

                                    if ($val[$orderData['parent_id']] !== null) {
                                        $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $val;
                                    }

                                } else {
                                    $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $val;

                                }

                                if (in_array($val['id'], $foreign)) {
                                    $this->data[$tables[$otherKey]][$tables[$otherKey]][] = $val['id'];
                                }
                            }
                        }
                    } elseif ($orderData['parent_id']) {

                        $parent = $tables[$otherKey];

                        $keys = $this->model->showForeignKeys($tables[$otherKey]);

                        if ($keys) {

                            foreach ($keys as $val) {

                                if ($val['COLUMN_NAME'] === 'parent_id') {
                                    $parent = $val['REFERENCED_TABLE_NAME'];
                                    break;
                                }
                            }
                        }

                        if ($parent === $tables[$otherKey]) {

                            $data = $this->model->get($tables[$otherKey], [
                                'fields' => [
                                    $orderData['columns']['id_row'] . ' as id',
                                    $orderData['name'],
                                    $orderData['parent_id']
                                ],
                                'order' => $orderData['order']
                            ]);

                            if ($data) {

                                while (($key = key($data)) !== null) {

                                    if (!$data[$key]['parent_id']) {

                                        $this->foreignData[$tables[$otherKey]][$data[$key]['id']]['name'] = $data[$key]['name'];
                                        unset($data[$key]);
                                        reset($data);
                                        continue;

                                    } else {

                                        if ($this->foreignData[$tables[$otherKey]][$data[$key][$orderData['parent_id']]]) {

                                            $this->foreignData[$tables[$otherKey]][$data[$key][$orderData['parent_id']]]['sub'][$data[$key]['id']] = $data[$key];

                                            if (in_array($data[$key]['id'], $foreign)) {
                                                $this->data[$tables[$otherKey]][$data[$key][$orderData['parent_id']]][] = $data[$key]['id'];
                                            }

                                            unset($data[$key]);
                                            reset($data);
                                            continue;

                                        } else {
                                            foreach ($this->foreignData[$tables[$otherKey]] as $id => $val) {

                                                $parent_id = $data[$key][$orderData['parent_id']];

                                                if (isset($val['sub']) && $val['sub'] && isset($val['sub'][$parent_id])) {
                                                    $this->foreignData[$tables[$otherKey]][$id]['sub'][$data[$key]['id']] = $data[$key];

                                                    if (in_array($data[$key]['id'], $foreign)) {
                                                        $this->data[$tables[$otherKey]][$id][] = $data[$key]['id'];
                                                    }

                                                    unset($data[$key]);
                                                    reset($data);
                                                    continue 2;
                                                }
                                            }
                                        }
                                        next($data);

                                    }
                                }

                            }

                        } else {

                            $parentOrderData = $this->createOrderData($parent);

                            $data = $this->model->get($parent, [
                                'fields' => [$parentOrderData['name']],
                                'join' => [
                                    $tables[$otherKey] => [
                                        'fields' => [$orderData ['columns']['id_row'] . ' as id', $orderData['name']],
                                        'on' => [$parentOrderData['columns']['id_row'], $orderData['parent_id']]
                                    ]
                                ],
                                'join_structure' => true
                            ]);

                            foreach ($data as $key => $val) {
                                if (isset($val['join'][$tables[$otherKey]]) && $val['join'][$tables[$otherKey]]) {

                                    $this->foreignData[$tables[$otherKey]][$key]['name'] = $val['name'];
                                    $this->foreignData[$tables[$otherKey]][$key]['sub'] = $val['join'][$tables[$otherKey]];

                                    foreach ($val['join'][$tables[$otherKey]] as $value) {

                                        if (in_array($value['id'], $foreign)) {
                                            $this->data[$tables[$otherKey]][$key][] = $value['id'];
                                        }

                                    }
                                }
                            }
                        }
                    } else {

                        $data = $this->model->get($tables[$otherKey], [
                            'fields' => [$orderData['columns']['id_row'] . ' as id', $orderData['name'], $orderData['parent_id']],
                            'order' => $orderData['order']
                        ]);

                        if ($data) {

                            $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['name'] = "SElECT";

                            foreach ($data as $val) {

                                $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $val;

                                if (in_array($val['id'], $foreign)) {
                                    $this->data[$tables[$otherKey]][$tables[$otherKey]][] = $val['id'];
                                }
                            }
                        }

                    }

                }
            }
        }
    }


    protected function checkManyToMany($settings = false)
    {

        if (!$settings) {
            $settings = $this->settings ?: Settings::instance();
        }
        $manyToMany = $settings::get('manyToMany');

        if ($manyToMany) {

            foreach ($manyToMany as $mTable => $tables) {
                $targetKey = array_search($this->table, $tables);

                if ($targetKey !== false) {
                    $otherKey = $targetKey ? 0 : 1;
                    $checkboxlist = $settings::get('templateArr')['checkboxlist'];
                    if (!$checkboxlist || !in_array($tables[$otherKey], $checkboxlist)) continue;

                    $columns = $this->model->showColumns($tables[$otherKey]);

                    $targetRow = $this->table . '_' . $this->columns['id_row'];
                    $otherRow = $tables[$otherKey] . '_' . $columns['id_row'];
                    $this->model->delete($mTable, [
                        'where' => [$targetRow => $_POST[$this->columns['id_row ']]]
                    ]);

                    if ($_POST[$tables[$otherKey]]) {
                        $insertArr = [];
                        $i = 0;

                        foreach ($_POST[$tables[$otherKey]] as $val) {
                            foreach ($val as $item) {
                                if ($item) {
                                    $insertArr[$i][$targetRow] = $_POST[$this->columns['id_row']];
                                    $insertArr[$i][$otherRow] = $item;
                                    $i++;
                                }
                            }
                        }

                        if ($insertArr) {
                            $this->model->add($mTable, [
                                'fields' => $insertArr
                            ]);
                        }
                    }
                }
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
                ])[0]['count'] + (int)!$this->data;

            for ($i = 1; $i <= $menu_pos; $i++) {
                $this->foreignData['menu_position'][$i - 1]['id'] = $i;
                $this->foreignData['menu_position'][$i - 1]['name'] = $i;
            }
        }


        return;
    }

}