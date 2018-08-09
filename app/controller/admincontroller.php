<?php
require_once 'app/controller/controller.php';
class AdminController extends Controller
{
    const DB_ADMINISTRATORS = 'administrators';

    public function save()
    {
        if ($_POST['password'] == null) {
            unset($_POST['password']);
        }
        parent::saveEntity();
        header('Location:index.php?route=admin');
        die();
    }
}
