<?php
require_once 'user.php';

class Model extends User
{
    protected $main_container_tpl;
    protected $selected_entity_info;

    public function __construct()
    {
        parent::__construct();
        if ($this->getClassification() != 0) {
            $this->main_container_tpl = 'app/view/templates/' . str_replace('model', '', strtolower(get_class($this))) . '/maincontainer/default_tpl.php';
        }
    }
    public function getMainContainerTpl()
    {
        return $this->main_container_tpl;
    }
    public function getSelectedEntityInfo()
    {
        return $this->selected_entity_info;
    }

    public function setMainContainerTpl($main_container_tpl)
    {
        $this->main_container_tpl = $main_container_tpl;
    }
    public function setSelectedEntityInfo($entity)
    {
        $this->selected_entity_info = $entity;
    }
}
