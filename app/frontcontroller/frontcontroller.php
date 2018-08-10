<?php
// The FrontController controlls the MVC structure of this project by triggers from the user that comes from the URL ($_GET['route'] and $_GET['action']).  
class FrontController
{
    protected $controller;
    protected $view;
    protected $model;

    // $router = saves all MVC triad combinations.
    // $routeName = $_GET['route'] / null(if not provided any route) - controls wich MVC triad will be load from the router, if null = loading default MVC.
    // $action = $_GET['action'] / null(if not provided any action) - triggers the relevant controller to execute action (function).
    public function __construct(Router $router, $routeName, $action)
    {
        require_once "app/model/{$routeName}model.php";
        require_once "app/controller/{$routeName}controller.php";
        require_once "app/view/{$routeName}view.php";
        $route = $router->getRoute($routeName);
        $modelName = $route->model;
        $controllerName = $route->controller;
        $viewName = $route->view;

        $this->model = new $modelName;
        $this->controller = new $controllerName($this->model);
        $this->view = new $viewName($this->model, $routeName);

        if (!empty($action)) {
            $this->controller->{$action}();
        }
    }

    public function printHeader()
    {
        return $this->view->HeaderOutput();
    }

    public function printSection()
    {
        return $this->view->output();
    }
}
