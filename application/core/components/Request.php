<?php

class Request
{
    public $get;
    public $post;
    
    public function __construct()
    {
        $this->get = empty($_GET) ? null : $this->clearData($_GET);
        $this->post = empty($_POST) ? null : $this->clearData($_POST);
    }
    
    private function clearData($data)
    {
        $cleanData = [];
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $cleanData[$key] = trim($value);
                $cleanData[$key] = strip_tags($cleanData[$key]);
            } else {
                $cleanData[$key] = $this->clearData($value);
            }
        }
        return $cleanData;
    }
}
