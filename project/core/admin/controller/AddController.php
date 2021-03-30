<?php

namespace admin\controller;

use base\settings\Settings;

class AddController extends BaseAdmin
{

    /**
     * @var string
     */
    protected $action = 'add';

    protected function inputData()
    {

        if (!$this->userId) {
            $this->execBase();
        }

        $this->checkPost();

        $this->createTableData();

        $this->createForeignData();

        $this->createMenuPosition();

        $this->createRadio();

        $this->createOutputData();

        $this->createManyToMany();

        return $this->expansion();
    }

    protected function manyAdd()
    {

        $fields = [
            'name' => '33!!!', 'menu_position' => '1',

        ];
        $files = [//'img' => '33.jpg'
            'img' => ['33.jpg', '33.jpg', '33.jpg']
        ];
        $this->model->add('teacher', [
            'fields' => $fields,
            'files' => $files
        ]);
    }



}