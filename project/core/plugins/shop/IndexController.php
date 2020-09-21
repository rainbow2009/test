<?php

namespace plugins\shop;

use base\controller\BaseController;

class IndexController extends BaseController
{

    protected $name;

    protected function inputData()
    {

        $name = 'masha';
        $surname = 'vasay';
        $this->name = 'croco';

        $content = $this->render(TEMPLATE.'content', compact('name'));
        $header= $this->render(TEMPLATE.'header');
        $footer= $this->render(TEMPLATE.'footer');

        return compact('header', 'content','footer');
    }

    protected function outputData()
    {
        $vars = func_get_arg(0);
      $this->page = $this->render(TEMPLATE.'templay', $vars);
    }

   
}