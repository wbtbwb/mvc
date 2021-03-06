<?php

class RegistrationForm extends Model
{    
    protected function rules()
    {
        return [
            'username' => ['required', 'length' => [3, 15]],
            'password' => ['required', 'length' => [3, 15]],
            're_password' => ['required', 'comparePasswords'],
        ];
    }
    
    protected function comparePasswords($re_password)
    {
        return $this->password === $re_password;
    }
    
    protected function errors()
    {
        return [
            'username' => [
                'required' => 'Укажите логин',
                'length' => 'Длина логина должна быть от 3 до 15 символов',
            ],
            'password' => [
                'required' => 'Укажите пароль',
                'length' => 'Длина пароля должна быть от 3 до 15 символов',
            ],
            're_password' => [
                'required' => 'Подтвердите пароль',
                'comparePasswords' => 'Пароли не совпадают',
            ],
        ];
    }
}
