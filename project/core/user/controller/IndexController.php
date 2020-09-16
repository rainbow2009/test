<?php

namespace user\controller;

use base\controller\BaseController;

class IndexController extends BaseController
{
    protected function hellow ()
    {
        $template = $this->render(false, ['name' => 'Masha']);
        dd($template);

    }
}