<?php


namespace admin\controller;


class DeleteController extends BaseAdmin
{
    protected function inputData()
    {
        if (!$this->userId) $this->execBase();

        $this->createTableData();

        if (!empty($this->parameters[$this->table])) {

            $id = is_numeric($this->parameters[$this->table]) ?
                $this->clearNum($this->parameters[$this->table]) :
                $this->clearStr($this->parameters[$this->table]);

            if ($id) {

                $this->data = $this->model->get($this->table, [
                    'where' => [$this->columns['id_row'] => $id]
                ]);

                if ($this->data) {

                }
            }

        }

        $_SESSION['res']['answer'][] = '<div class="vg-element vg-padding-in-px" style="color: red">' . $this->messages['deleteFail'] . '</div>';
     
        $this->redirect();

    }
}