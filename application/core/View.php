<?php

class View
{
    const VIEWS_DIR = APP_DIR . '/views';
    const LAYOUTS_DIR = APP_DIR . '/views/layouts';
    
    public $layoutName = 'main';
    public $title = SITE_NAME;
    
    public function render($templateName, $data = null)
    {
        if (is_array($data)) {
            extract($data);
        }
        
        ob_start();
        require_once self::VIEWS_DIR . "/$templateName.php";
        
        $template = ob_get_contents();
        ob_end_clean();
        
        require_once self::LAYOUTS_DIR . '/' . $this->layoutName . '.php';
    }
}
