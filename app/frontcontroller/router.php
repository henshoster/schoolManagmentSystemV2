<?php
// saves routes to all MVC triad combinations.
class Router
{
    protected $table = array();

    public function __construct()
    {
        $this->table['default'] = new Route('Model', 'View', 'Controller');
        $this->table['school'] = new Route('schoolModel', 'schoolView', 'schoolController');
        $this->table['admin'] = new Route('adminModel', 'adminView', 'adminController');
    }

    public function getRoute($route)
    {
        $route = strtolower($route);
        if (!isset($this->table[$route])) {
            return $this->table['default'];
        }
        return $this->table[$route];
    }
}
