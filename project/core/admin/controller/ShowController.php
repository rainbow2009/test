<?php


namespace admin\controller;


use base\settings\Settings;

class ShowController extends BaseAdmin
{

    protected function inputData()
    {
        $arr['order_direction'] = "DSC";
        $this->execBase();
        $this->createTableData();
        $this->createData($arr);
    }

    protected function outputData()
    {

    }


}