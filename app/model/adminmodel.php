<?php
require_once 'app/model/model.php';
// model for 'Administration' tab -> loaded only when route=admin and classification > 1 (role = manager / owner).

// $administrators = array of administrators.


class AdminModel extends Model
{
    const DB_ADMINISTRATORS = 'administrators';

    protected $administrators;

    public function __construct()
    {
        parent::__construct();
        if ($this->classification < 2) {
            header('Location:index.php');
            die();
        }
        // if classification < 3 (role != owner) -> not loading owner details into administrators array.
        if ($this->classification < 3) {
            $this->administrators = $this->select(self::DB_ADMINISTRATORS, '*', "role NOT LIKE 'owner' ORDER BY role='manager' DESC");
        } else {
            $this->administrators = $this->select(self::DB_ADMINISTRATORS, '*', "1  ORDER BY role='owner' desc,role='manager' DESC");
        }
    }
    public function getAdministrators()
    {
        return $this->administrators;
    }

}
