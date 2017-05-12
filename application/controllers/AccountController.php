<?php

class AccountController extends Controller
{
    public function actionSignUp()
    {
        if (!$this->request->post['user']) {
            return $this->render('registration');
        }
        
        $userData = $this->request->post['user'];
        $registrationForm = (new RegistrationForm)
            ->setAttributes($userData);
        
        if (!$registrationForm->isValid()) {
            return $this->render('registration', [
                'errors' => $registrationForm->errors,
            ]);
        }
        
        $success = $this->user->checkIn($userData);
        $errors = [];
        
        if(false === $success) {
            $errors = ['Пользователь с таким лонином уже существует'];
        } elseif (is_null($success)) {
            $errors = ['Регистрация прервалась по неизвестным причинам'];
        }
        
        if (!empty($errors)) {
            return $this->render('registration', ['errors' => $errors]);
        }
        
        return $this->render('message', [
            'header' => 'Вы успешно зарегестрированы',
        ]);
    }
    
    public function actionSignIn()
    {
        if (!$this->request->post['user']) {
            return $this->render('login');
        }
        
        $userData = $this->request->post['user'];
        $loginForm = (new LoginForm)
            ->setAttributes($userData);
            
        if (!$loginForm->isValid()) {
            return $this->render('login', [
                'errors' => $loginForm->errors,
            ]);
        }
        
        if(!$this->user->logIn($userData)) {
            return $this->render('login', [
                'errors' => ['Неверный логин или пароль'],
            ]);
        }
        
        header('Location: /');
    }
    
    public function actionSignOut()
    {
        $this->user->logOut();
        header('Location: /');
    }
    
    protected function access()
    {
        return [
            'sign-up' => ['G'],
            'sign-in' => ['G'],
            'sign-out' => ['L'],
        ];
    }
}
