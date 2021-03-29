<?php


namespace admin\controller;



class DeleteController extends BaseAdmin
{
    protected function inputData()
    {
        if(!$this->userId) $this->execBase();

    }
}