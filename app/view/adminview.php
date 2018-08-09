<?php
require_once 'app/view/view.php';
class AdminView extends View
{
    protected $administrators;

    public function output()
    {
        $this->administrators = $this->model->getAdministrators();
        parent::output();
    }
}
