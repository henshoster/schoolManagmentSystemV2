<?php
require_once 'app/controller/controller.php';
class AdminController extends Controller
{
    const DB_ADMINISTRATORS = 'administrators';

    public function save()
    {
        // if admin has not entered a new password, remove it from $_POST, so password will not be rewritten to null.
        if ($_POST['password'] == null) {
            unset($_POST['password']);
        }
        // parent deals with the rest ->create new or update.
        parent::saveEntity();
        header('Location:index.php?route=admin');
        die();
    }
}
