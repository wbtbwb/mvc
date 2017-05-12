<?php

class UserRecord extends ActiveRecord
{
    protected function tableName()
    {
        return 'user';
    }
    
    protected function rules()
    {
        return [
            'id' => ['type' => 'integer'],
            'username' => ['type' => 'string', 'max' => 16, 'required'],
            'password_hash' => ['type' => 'string', 'max' => 32, 'required'],
            'salt' => ['type' => 'string', 'max' => 8, 'required'],
            'role' => ['type' => 'string', 'max' => 16],
        ];
    }
}
