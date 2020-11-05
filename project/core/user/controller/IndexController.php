<?php

namespace user\controller;

use admin\model\Model;
use base\controller\BaseController;


class IndexController extends BaseController
{

    protected $name;

    protected function inputData()
    {

//
//        $name = 'masha';
//        $surname = 'vasay';
//        $this->name = 'croco';
//
//        $content = $this->render(TEMPLATE . 'content', compact('name'));
//        $header = $this->render(TEMPLATE . 'header');
//        $footer = $this->render(TEMPLATE . 'footer');
//
//        return compact('header', 'content', 'footer');
//        $srt = '123456789dasdasfsfsdfsd';
//        $en_str = \base\model\Crypt::instance()->encrypt($srt);
//        $dec_str = \base\model\Crypt::instance()->decrypt($en_str);
//
//        dd($en_str,$dec_str);

        $model = Model::instance();
        $res = $model->get('teacher', [
            'where' => ['id' => '2,3'],
            'operand' => ['IN'],
            'join' => [
                'student_teacher f' => ['on' => ['id', 'teacher']],
                'students f' => [
                    'fields' => ['name as student_name', 'content'],
                    'on' => ['student', 'id']
                ],
                [
                    'table' => 'students',
                    'on' =>['parent_id','id']
                ]
            ],
            'join_structure' => true
        ]);
        dd($res);
    }

    protected function outputData()
    {
        $vars = func_get_arg(0);
        $this->page = $this->render(TEMPLATE . 'templay', $vars);
    }


}