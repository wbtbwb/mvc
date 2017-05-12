<?php

class MainController extends Controller
{
    public function actionIndex()
    {
        return $this->render('main');
    }
    
    protected function access()
    {
        return [
            'index'  => ['*'],
        ];
    }
}
