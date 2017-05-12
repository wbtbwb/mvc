<?php

class Migration
{
    private $_migrationsDir = APP_DIR . '/migrations';
    private $_db;
    public $errors = [];
    
    public function __construct()
    {
        $this->_db = Database::getInstance();
        $this->_db->query('SET AUTOCOMMIT=0');
        $this->_db->query('TRANSACTION START');
    }
    
    public function migrateUp($migrationName)
    {
        $migration = file_get_contents("{$this->_migrationsDir}/$migrationName.sql");
        $queries = explode('--delimiter', $migration);
        
        foreach ($queries as $query) {
            if (false === $this->_db->query($query)) {
                $this->errors[] = $this->_db->error[0];
            }
        }
        
        $concluding = empty($this->errors) ? 'COMMIT' : 'ROLLBACK';
        $this->_db->query($concluding);
        
        return empty($this->errors) ? true : false;
    }
}
