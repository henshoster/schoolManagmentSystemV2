<?php
require_once 'app/model/model.php';
class SchoolModel extends Model
{
    const DB_STUDENTS = 'students';
    const DB_COURSES = 'courses';
    const DB_S2C = 'students2courses';

    protected $courses;
    protected $students;

    protected $connected_type_info;
    protected $connected_type_name;
    protected $type_columns_names;

    public function __construct()
    {
        parent::__construct();
        if ($this->classification < 1) {
            header('Location:index.php');
            die();
        }
        $this->courses = $this->select(self::DB_COURSES);
        $this->students = $this->select(self::DB_STUDENTS);
        $this->connected_type_info = null;
        $this->connected_type_name = null;
        $this->type_columns_names = null;
    }
    public function getCourses()
    {
        return $this->courses;
    }
    public function getStudents()
    {
        return $this->students;
    }

    public function getMainContainerConnectedTypeInfo()
    {
        return $this->connected_type_info;
    }

    public function getConnectedTypeName()
    {
        return $this->connected_type_name;
    }
    public function getTypeColumnsNames()
    {
        return $this->type_columns_names;
    }

    public function setConnectedEntityInfo($connected_entity_info,$connected_entity_name)
    {
        $this->connected_type_info =$connected_entity_info;
        $this->connected_type_name = $connected_entity_name;
    }
    public function setTypeColumnsNames($type)
    {
        $this->type_columns_names = $this->describeTable($type);
        unset($this->type_columns_names['0']);
    }

}
