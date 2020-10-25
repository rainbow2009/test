<?php

namespace admin\controller;

use base\controller\BaseController;
use admin\model\Model;
use base\settings\Settings;

class IndexController extends BaseController
{

    protected $name;

    protected function inputData()
    {

        $redirect = PATH . Settings::get('routes')['admin']['alias'] . '/show';
        $this->redirect($redirect);

//        $db = Model::instance();
//
//
//        $table = 'teacher';
//
//
//        for ($i = 0; $i < 8; $i++) {
//            $s_id = $db->add('students', [
//                'fields' => ['name' => 'student - ' . $i, 'content' => 'content - ' . $i],
//                'return_id' => true
//            ]);
//            $db->add('teacher', [
//                'fields' => ['name' => 'teacher - ' . $i, 'student_id' => $s_id]
//            ]);
//        }
//
//
////        $res = $db->get($table,
////            [
////                'fields' => ['id','name'],
////                'where' => [ 'name' => 'masha'  ],
////                 'operand' =>['IN', '<>'],
////                 'condition' => ["OR",'AND'],
////                'order' => [ 'name'],
////                'order_direction' => ['DESC'],
////                'limit' => '1',
//        // 'join' =>[
//        //   [
//        //   'table' => 'join_table1',
//        //   'fields' => ['id as j_id','name as j_name'],
//        //   'type' =>'left',
//        //   'where' => [ 'name' => 'sasha'],
//        //   'operand' =>['='],
//        //   'condition' => ["OR"],
//        //   'on' => [
//        //    'table' => 'teachers',
//        //    'fields' => ['id', 'parent_id']
//        //   ]
//        // ],
//        // 'join_table2' =>[
//        //     'table' => 'join_table2',
//        //     'fields' => ['id as j2_id','name as j2_name'],
//        //     'type' =>'left',
//        //     'where' => [ 'name' => 'sasha'],
//        //     'operand' =>['<>'],
//        //    'condition' => ["AND"],
//        //     'on' =>['id', 'parent_id']
//
//        // ]
////            ]
////        ]
////    );
//
//
//        dd(1);
//        $name = 'masha';
//        $surname = 'vasay';
//        $this->name = 'croco';
//
//        $content = $this->render(ADMIN_TEMPLATE . 'content', compact('name'));
//        $header = $this->render(ADMIN_TEMPLATE . 'header');
//        $footer = $this->render(ADMIN_TEMPLATE . 'footer');
//
//        return compact('header', 'content', 'footer');
    }

    protected function outputData()
    {
        $vars = func_get_arg(0);
        $this->page = $this->render(TEMPLATE . 'templay', $vars);
    }


}