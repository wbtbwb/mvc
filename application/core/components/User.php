<?php

class User
{
    public $logged = false;
    public $id;
    public $username;
    public $role = 'guest';
    
    public function __construct()
    {
        $logged = isset($_SESSION['user']) ? $_SESSION['user']['logged'] : false;
        defined('LOGGED') || define('LOGGED', $logged);
        
        if (true === LOGGED) {
            $this->logged = true;
            $this->id = $_SESSION['user']['id'];
            $this->username = $_SESSION['user']['username'];
            $this->role = $_SESSION['user']['role'];
        }
    }
    
    protected function setSessionData(UserRecord $userRecord)
    {
        $_SESSION['user'] = [
            'logged' => true,
            'id' => $userRecord->id,
            'username' => $userRecord->username,
            'role' => $userRecord->role,
        ];
    }
    
    public function checkIn($userData)
    {
        $user = (new UserRecord)
            ->findOne(['username' => $userData['username']]);
        
        if (!is_null($user)) {
            return false;
        }
        
        $salt = Lib::randStr(8);
        $userData['salt'] = $salt;
        $userData['password_hash'] = md5(md5($userData['password'] . $salt));
        $userData['role'] = 'ordinary';
        
        $userRecord = (new UserRecord)
            ->setAttributes($userData);
        
        if (!$userRecord->save() && $userRecord->hasErrors()) {
            throw new Exception('Не удалось зарегистрировать нового пользователя');
            return null;
        } else {
            $this->setSessionData($userRecord);
            return true;
        }
    }
    
    public function logIn($userData)
    {
        $userRecord = (new UserRecord)
            ->findOne(['username' => $userData['username']]);
        
        if (is_null($userRecord) || $userRecord->password_hash !== md5(md5($userData['password'] . $userRecord->salt))) {
            return false;
        } else {
            $this->setSessionData($userRecord);
            return true;
        }
    }
    
    public function logOut()
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}
