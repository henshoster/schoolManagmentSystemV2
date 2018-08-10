<?php
require_once 'app/view/view.php';
class SchoolView extends View
{
    //$courses = array of all courses
    //$students = array of all students
    //$connected_type_info = array of connected entities to the selcted entity.
    //$connected_type_name = string -> conncted entities type name. (student or course);
    //$type_columns_names = entity fields names (keys).
    protected $courses;
    protected $students;
    protected $connected_type_info;
    protected $connected_type_name;
    protected $type_columns_names;

    public function output()
    {
        $this->courses = $this->model->getCourses();
        $this->students = $this->model->getStudents();
        $this->connected_type_info = $this->model->getConnectedEntityInfo();
        $this->connected_type_name = $this->model->getConnectedEntityName();
        $this->type_columns_names = $this->model->getEntityFieldsNames();
        parent::output();
    }
}
