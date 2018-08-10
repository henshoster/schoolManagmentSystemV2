<?php
class View
{
    protected $model;
    protected $route;

    //$main_container_tpl = path to relevant main container template.
    //$selected_entity_info = array of info about the selected entity.
    protected $main_container_tpl;
    protected $selected_entity_info;

    public function __construct(Model $model, $route = null)
    {
        $this->model = $model;
        $this->route = $route;
    }
    // include relevant templates for the header (nav bar + login).
    public function HeaderOutput()
    {
        $classification = $this->model->getClassification();
        $loggedInUser = $this->model->getLoggedInUser();
        include 'app/view/templates/header/header_tpl.php';
        include 'app/view/templates/modals/login_modal.php';
    }
    // include relevant templates for the section of the page.
    public function output()
    {
        if ($this->route != null) {
            $this->main_container_tpl = $this->model->getMainContainerTpl();
            $this->selected_entity_info = $this->model->getSelectedEntityInfo();
            include 'app/view/templates/' . str_replace('view', '', strtolower(get_class($this))) . '/' . str_replace('view', '', strtolower(get_class($this))) . '_tpl.php';
            include 'app/view/templates/modals/delete_confirmation_modal.php';
        } else {
            include 'main_page/default_main_page_tpl.php';
        }
    }
}
