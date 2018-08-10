<?php
require_once 'database/database.php';
// contains connected user(administrator) details (getting them from db), and creates classification based on role. 
// each refresh $loggedInUser details are reloaded -> and creates new $classification. Prevents user wich already been downgraded or removed to save old session permissions.
abstract class User extends DataBase
{
    protected $loggedInUser;
    protected $classification;

    const DB_TABLE = 'administrators';

    // if user have active session -> reloads user details from db and creates new classification.
    // if user dont have any active sessions -> provide him guest classification (0);
    public function __construct()
    {
        parent::__construct();
        if (isset($_SESSION['loggedInUser'])) {
            $this->loggedInUser = $this->select(self::DB_TABLE, '*', "id = '{$_SESSION['loggedInUser'][0]['id']}'")[0];
            switch ($this->loggedInUser['role']) {
                case 'owner':
                    $this->classification = 3;
                    break;
                case 'manager':
                    $this->classification = 2;
                    break;
                case 'sales':
                    $this->classification = 1;
                    break;
            }
        } else {
            $this->classification = 0;
        }
    }
    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }
    public function getClassification()
    {
        return $this->classification;
    }
}
