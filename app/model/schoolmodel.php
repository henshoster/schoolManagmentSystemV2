<?php
require_once 'app/model/model.php';

// model for 'School' tab -> loaded only when route=school and classification > 0.

// $courses = array of all courses.
// $students = array of all students.

// $connected_entity_name = name of the connected entity to the parent::$selected_entity_info.
// $connected_entity_info = array of all connected entities to the parent::$selected_entity_info.

// $entity_fields_names = array of all fields names needed to create new entity from requested type.
class SchoolModel extends Model
{
    const DB_STUDENTS = 'students';
    const DB_COURSES = 'courses';
    const DB_S2C = 'students2courses';

    protected $courses;
    protected $students;

    protected $connected_entity_info;
    protected $connected_entity_name;

    protected $entity_fields_names;

    public function __construct()
    {
        parent::__construct();
        if ($this->classification < 1) {
            header('Location:index.php');
            die();
        }
        $this->courses = $this->select(self::DB_COURSES);
        $this->students = $this->select(self::DB_STUDENTS);
        $this->connected_entity_info = null;
        $this->connected_entity_name = null;
        $this->entity_fields_names = null;
    }
    public function getCourses()
    {
        return $this->courses;
    }
    public function getStudents()
    {
        return $this->students;
    }

    public function getConnectedEntityInfo()
    {
        return $this->connected_entity_info;
    }

    public function getConnectedEntityName()
    {
        return $this->connected_entity_name;
    }
    public function getEntityFieldsNames()
    {
        return $this->entity_fields_names;
    }

    public function setConnectedEntityInfo($connected_entity_info,$connected_entity_name)
    {
        $this->connected_entity_info =$connected_entity_info;
        $this->connected_entity_name = $connected_entity_name;
    }
    public function setTypeColumnsNames($type)
    {
        $this->entity_fields_names = $this->describeTable($type);
        // unset id field.
        unset($this->entity_fields_names['0']);
    }

}
