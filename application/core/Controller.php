<?php

abstract class Controller
{
    protected $_view;
    
    public function __construct()
    {
        $this->accessControl(Router::getRoutes()[1]);
        $this->_view = new View;
    }
    
    public function __get($name)
    {
        return Registry::exists($name) ? Registry::get($name) : null;
    }
    
    abstract protected function access();
    
    protected function accessControl($action)
    {
        $access = $this->access();
        
        if (empty($access[$action])) {
            return true;
        }
        
        foreach ($access[$action] as $role) {
            switch ($role) {
                case '*':
                break;
                case 'G':
                    if (false !== LOGGED) {
                        header('Location: /');
                    }
                break;
                case 'L':
                    if (true !== LOGGED) {
                        header('Location: /account/sign-in');
                    }
                break;
                case 'A':
                    if ('admin' !== $this->user->role) {
                        Response::is403();
                        die();
                    }
                break;
                case 'D':
                    if (true !== DEV) {
                        Response::is404();
                        die();
                    }
            }
        }
        
        return true;
    }
    
    public function render($templateName, $data = null)
    {
        return $this->_view->render($templateName, $data);
    }
}
