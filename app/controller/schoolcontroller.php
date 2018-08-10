<?php
require_once 'app/controller/controller.php';
class SchoolController extends Controller
{
    const DB_STUDENTS = 'students';
    const DB_COURSES = 'courses';
    const DB_S2C = 'students2courses';

    public function deleteEntity()
    {
        if ($_GET['type'] == 'students') {
            $this->model->delete(self::DB_S2C, "students_id='{$_GET['id']}'");
        }
        parent::deleteEntity();
    }
    public function newEntityForm()
    {
        if ($_GET['type'] == 'courses' && $this->model->getClassification() < 2) {
            header('Location:index.php?route=school');
            die();
        }
        $this->model->setTypeColumnsNames($_GET['type']);
        parent::newEntityForm();
    }

    public function save()
    {
        $courses = isset($_POST['courses']) ? $_POST['courses'] : null;
        unset($_POST['courses']);
        // create / update entity -> $id = contains the id of the entity.
        $id = parent::saveEntity();
        // in case the new entity is student -> push student id + courses id wich he is registered to them to the related table (students2courses) . first remove all data for this student id, then push the new data(even if the data is not changed).
        if ($_GET['type'] == 'students') {
            $this->model->delete(self::DB_S2C, "students_id='$id'");
            $columns = ['students_id', 'courses_id'];
            $values = [];
            foreach ($courses as $key => $value) {
                array_push($values, $id);
                array_push($values, $value);
                $this->model->insert(self::DB_S2C, $columns, $values);
                $values = [];
            }
        }

        header('Location:' . str_replace('save', 'showdetails', "index.php?{$_SERVER['QUERY_STRING']}" . (isset($_GET['id']) ? "" : "&id={$id}")));
        die();
    }
    public function editEntity()
    {
        //Prevents from admin type of 'Sales' to edit courses if he will try to do it by url.
        if ($_GET['type'] == 'courses' && $this->model->getClassification() < 2) {
            header('Location:index.php?route=school');
            die();
        }
        parent::editEntity();
    }
    public function showDetails()
    {
        $this->model->setMainContainerTpl("app/view/templates/school/maincontainer/details_tpl.php");
        $this->findSelectedEntityInfo();
    }

    public function findSelectedEntityInfo()
    {
        // parent function finds the entity from the array of entities and set it to $selected_entity_info in model.
        parent::findSelectedEntityInfo();
        // the rest of the function find the connected entities to the selected entity to be displayed in the relevant page and set it to the model.
        $connected_entity_name = str_replace('2', '', str_replace($_GET['type'], '', self::DB_S2C));
        $temp_connected_entity_info = $this->model->queryTreatment(
            "SELECT id,name,image_src
            FROM students2courses
            LEFT JOIN $connected_entity_name
            ON students2courses.{$connected_entity_name}_id = {$connected_entity_name}.id
            WHERE {$_GET['type']}_id='{$_GET['id']}'");
        $connected_entity_info = [];
        foreach ($temp_connected_entity_info as $key => $value) {
            $connected_entity_info[$value['id']] = $value;
        }
        $this->model->setConnectedEntityInfo($connected_entity_info,$connected_entity_name);
    }
}
