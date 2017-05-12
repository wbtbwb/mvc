<?php

class MigrationController extends Controller
{
    public function actionIndex()
    {
        $migration = new Migration;
        $success = $migration->migrateUp('user');
        
        $header = $success ? 'Миграция проведена успешно' : 'Возникли следующие ошибки:';
        $errors = $migration->errors;
        
        return $this->render('message', compact('header', 'errors'));
    }
    
    protected function access()
    {
        return [
            'index'  => ['D'],
        ];
    }
}
