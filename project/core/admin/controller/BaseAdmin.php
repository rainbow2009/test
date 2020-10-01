<?php


namespace admin\controller;


use admin\model\Model;
use base\controller\BaseController;
use base\exceptions\RouteException;
use base\settings\Settings;

abstract class BaseAdmin extends BaseController
{
    protected $model;
    protected $menu;
    protected $title;
    protected $table;
    protected $columns;

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
        $this->sendNoCacheHeaders();
        $this->createTableData();

    }

    protected function sendNoCacheHeaders()
    {

        header('Last-Modified: ' . gmdate("D, d m Y H:i:s") . " GMT");
        header('Cache-Control: no-cache, must-revalidate');
        header('Cache-Control: max-age=0');
        header('Cache-Control: post-chek=0,pre-chek=0');


    }

    protected function exectBase()
    {
        self::inputData();
    }

    protected function createTableData()
    {
        if (!$this->table) {
            if ($this->parameters) {
                $this->table = array_key_exists($this->parameters)[0];
            } else {
                $this->table = Settings::get('defaultTable');
            }
        }
        $this->columns = $this->model->showColumns($this->table);

        if (!$this->columns) {
            new RouteException('bad fields name at table' . $this->table, 2);
        }
        dd($this->columns);
    }


}