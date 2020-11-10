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
                $this->clearPostFields($item);
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
                $method = 'edit';
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

        $this->createAlias($id);

        $this->updateMenuPosition();

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

    protected function updateMenuPosition()
    {

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

        if (!$columns) throw new RouteException('table columns exist ' . $table);

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
            if (!$name) $name = $columns['id_row'] . ' as name';
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

        if (!$settings) $settings = $this->settings ?: Settings::instance();

        $manyToMany = $settings::get('manyToMany');
        $blocks = $settings::get('blockNeedle');
        if ($manyToMany) {

            foreach ($manyToMany as $mTable => $tables) {

                $targetKey = array_search($this->table, $tables);

                if (!$targetKey !== false) {

                    $otherKey = $targetKey ? 0 : 1;

                    $checkBoxList = $settings::get('templateArr')['checkboxlist'];
                    if (!$checkBoxList || !in_array($tables[$otherKey], $checkBoxList)) {

                        continue;
                    }
                    if (!$this->translate[$tables[$otherKey]]) {
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

                        if (!$insert) $this->blocks[array_keys($this->blocks)[0]][] = $tables[$otherKey];

                        $foreign = [];

                        if ($this->data) {
                            $res = $this->model->get($mTable, [
                                'fields' => [$tables[$otherKey] . '_' . $orderData['columns']['id_row']],
                                'where' => [$this->table . '_' . $this->columns['id_row']
                                        = $this->data[$this->columns['id_row']]]
                            ]);
                            if($res){
                                foreach ($res as $item){
                                    $foreign[] = $tables[$otherKey] . '_' . $orderData['columns']['id_row'];
                                }
                            }
                        }

                    }
                }
            }
        }
    }

}