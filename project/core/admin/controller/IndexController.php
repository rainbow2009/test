<?php

namespace admin\controller;

use base\controller\BaseController;

class IndexController extends BaseController
{

    protected $name;

    protected function inputData()
    {

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