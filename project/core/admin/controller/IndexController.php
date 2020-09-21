<?php

namespace admin\controller;

use base\controller\BaseController;
use admin\model\Model;

class IndexController extends BaseController
{

    protected $name;

    protected function inputData()
    {


        $db = Model::instance();
        $query = "SELECT * FROM product";

        $res = $db ->query($query, 'e');
        dd($res);







        
        $name = 'masha';
        $surname = 'vasay';
        $this->name = 'croco';

        $content = $this->render(ADMIN_TEMPLATE.'content', compact('name'));
        $header= $this->render(ADMIN_TEMPLATE.'header');
        $footer= $this->render(ADMIN_TEMPLATE.'footer');

        return compact('header', 'content','footer');
    }

    protected function outputData()
    {
        $vars = func_get_arg(0);
      $this->page = $this->render(TEMPLATE.'templay', $vars);
    }

   
}