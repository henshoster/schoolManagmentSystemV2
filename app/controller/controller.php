<?php
class Controller
{
    const DB_TABLE = 'administrators';

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
        // check user input. security
        if (isset($_POST)) {
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->checkInput($value);
            }
        }
    }

    // get user input and return more secured data before sent to db:
    // trim - Strip whitespace from the beginning and end of a string.
    // stripslashes - Un-quotes a quoted string.
    // htmlspecialchars - Convert special characters to HTML entities.
    public function checkInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function getName()
    {
        return get_class($this);
    }

    public function logIn()
    {
        // prevent user manipulate the system and trigger log in without get through login page and click log in button.
        if (!isset($_POST['log_in'])) {
            header("Location:index.php");
            die();
        }
        // $hash = current user password(encrypted) as provided from db.
        $hash = $this->model->select(self::DB_TABLE, 'password', "email = '{$_POST['user_email_login']}'")[0]['password'];
        // checks if user input 'password' match the password in db. if pass-> create session and continue, if not pass -> error massage.
        if (password_verify($_POST['password_login'], $hash)) {
            $_SESSION['loggedInUser'] = $this->model->select(self::DB_TABLE, '*', "email = '{$_POST['user_email_login']}'");
            header("Location:index.php?route=school");
            die();
        } else {
            $error = 'Email or Password incorrect or do not exist';
            header("Location:index.php?loginerror={$error}");
            die();
        }
    }

    public function logOut()
    {
        unset($_COOKIE['PHPSESSID']);
        setcookie('PHPSESSID', '', time() - 3600, '/');
        session_destroy();
        header("Location:index.php");
        die();
    }

    // Create or updates new/exisiting entity. returns the id of the entity.
    public function saveEntity()
    {
        // in this system email field for each entity (student / admin) is unique -> checks that email adress is not already exist in case we creating new entity or editing existing entity.
        if (isset($_POST['email'])) {
            $getEntityType = "get" . ucfirst($_GET['type']);
            // Running through all entities from relevant type, and search if email already exist. if email found and its not this selected entity email -> ERROR.
            foreach ($this->model->{$getEntityType}() as $key => $value) {
                if (strtolower($value['email']) == strtolower($_POST['email'])) {
                    // if the email wich was found is this entity current email (email was not edited) -> break loop and continue.
                    if (isset($_GET['id']) && ($value['id'] == $_GET['id'])) {
                        break;
                    }
                    header('Location:' . str_replace('save', $_POST['last_action'], "index.php?{$_SERVER['QUERY_STRING']}&upload_error=Email already exist! Please choose another one"));
                    die();
                }
            }
        }

        // checks if any file uploaded by user ->if yes, continue to file upload.
        if ($_FILES['fileToUpload']["tmp_name"] != null) {
            $uploadResult = $this->fileUpload();
            // if file uploaded without any errors to server -> save his path to $_POST['image_src'].
            //else -> return relevant error massage.
            if ($uploadResult['uploadOk'] == 1) {
                $_POST['image_src'] = $uploadResult['target_file'];
            } else {
                header('Location:' . str_replace('save', $_POST['last_action'], "index.php?{$_SERVER['QUERY_STRING']}&upload_error={$uploadResult['error']}"));
                die();
            }
        }
        unset($_POST['last_action']);

        // $coulumns = relevant field names of the entity to be inserted/updated in db.
        $columns = [];
        // $values = the values to be inserted to each field name ($columns)
        $values = [];
        foreach ($_POST as $key => $value) {
            array_push($columns, $key);
            array_push($values, $key == 'password' ? password_hash($value, PASSWORD_DEFAULT) : $value);
        }
        // if entity already exist -> update , if its new entity ->create new (insert)
        if (isset($_GET['id'])) {
            $this->model->update($_GET['type'], $columns, $values, "id='{$_GET['id']}'");
            $id = $_GET['id'];
        } else {
            $this->model->insert($_GET['type'], $columns, $values);
            $id = $this->model->getLastId();
        }
        return $id;
    }

    public function editEntity()
    {
        $this->model->setMainContainerTpl('app/view/templates/' . str_replace('controller', '', strtolower(get_class($this))) . '/maincontainer/edit_tpl.php');
        $this->findSelectedEntityInfo();
    }

    public function newEntityForm()
    {
        $this->model->setMainContainerTpl('app/view/templates/' . str_replace('controller', '', strtolower(get_class($this))) . '/maincontainer/newentity_tpl.php');
    }

    public function deleteEntity()
    {
        $this->model->delete($_GET['type'], "id='{$_GET['id']}'");
        header('Location:index.php?route=' . str_replace('controller', '', strtolower(get_class($this))));
        die();
    }

    // Running through all entities from relevant type and search for matching id, when match -> set this entity info to Model::$selected_entity_info.
    public function findSelectedEntityInfo()
    {
        $getEntitiesMethod = "get".ucfirst($_GET['type']);
        foreach ($this->model->{$getEntitiesMethod}() as $entity) {
            if ($entity['id'] == $_GET['id']) {
                $this->model->setSelectedEntityInfo($entity);
                break;
            }
        }
    }

    // file upload with validations.
    public function fileUpload()
    {
        $uploadResult = [];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadResult['uploadOk'] = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        $error = '';
        // first simple check if image file is a actual image or fake image. 
        if ($check !== false) {
            $uploadResult['uploadOk'] = 1;
        } else {
            $uploadResult['error'] = "File is not an image.";
            $uploadResult['uploadOk'] = 0;
            return $uploadResult;
        }
        // validate max size is 500kb , if somehow you got up here and size is 0 , its mean you you are dealing with file wich max-size is more that your max size allowed by server for fileupload.
        if ($_FILES["fileToUpload"]["size"] > 500000 || $_FILES["fileToUpload"]["size"] == 0) {
            $uploadResult['error'] = "Sorry, your file is too large max size is:500kb.";
            $uploadResult['uploadOk'] = 0;
            return $uploadResult;
        }
        // validate file formats - > allowed : png, jpeg, jpg, gif.
        if (($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") || $_FILES["fileToUpload"]["type"] == null) {
            $uploadResult['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadResult['uploadOk'] = 0;
            return $uploadResult;
        }
        // Check if $uploadOk is set to 0 by an error ->send error. else ->try to upload file.
        if ($uploadResult['uploadOk'] == 0) {
            $uploadResult['error'] = "Sorry, your file was not uploaded.";
            return $uploadResult;
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $uploadResult['target_file'] = $target_file;
                return $uploadResult;
            } else {
                $uploadResult['error'] = "Sorry, there was an error uploading your file.";
                $uploadResult['uploadOk'] = 0;
                return $uploadResult;
            }
        }
    }
}
