<?php

class Router
{
    private static $_controllerName = 'main';
    private static $_actionName = 'index';
    private static $_routes = [];
    
    public static function start()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (!empty($_GET)) {
            $uri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $uri);
        }
        $routes = explode('/', $uri);
        array_shift($routes);
        
        if (!empty($routes[0])) {
            self::$_controllerName = $routes[0];
        }
        if (!empty($routes[1])) {
            self::$_actionName = $routes[1];
        }
        
        self::$_routes = [self::$_controllerName, self::$_actionName];
        
        self::$_controllerName = self::parseRouteName(self::$_controllerName) . 'Controller';
        self::$_actionName = 'action' . self::parseRouteName(self::$_actionName);
        
        $controllerPath = APP_DIR . '/controllers/' . self::$_controllerName . '.php';
        if (!file_exists($controllerPath)) {
            Response::is404();
            die();
        }
        require_once $controllerPath;
        
        $controller = new self::$_controllerName;
        if (!method_exists($controller, self::$_actionName)) {
            Response::is404();
            die();
        }
        $controller->{self::$_actionName}();
    }
    
    private static function parseRouteName($name)
    {
        $nameParts = explode('-', $name);
        $name = '';
        foreach ($nameParts as $part) {
            $name .= ucfirst($part);
        }
        return $name;
    }
    
    public static function getRoutes()
    {
        return self::$_routes;
    }
}
