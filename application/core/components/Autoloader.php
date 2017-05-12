<?php

class Autoloader
{
    protected function map()
    {
        return [
            COMPONENTS_DIR,
            APP_DIR . '/models',
        ];
    }
    
    public function register()
    {
        spl_autoload_register([$this, 'autoload']);
    }
    
    protected function autoload($className)
    {
        foreach ($this->map() as $dir) {
            if (file_exists($dir . "/$className.php")) {
                require_once $dir . "/$className.php";
                return true;
            }
        }
        throw new Exception("Не удалось подключить класс $className");
    }
}
