<?php $this->title = 'Вход' ?>
<h1>Вход</h1>
<form method='POST' action='/account/sign-in' class='sign-form'>
    <label for='login-field'>Логин</label>
    <input type='text' name='user[username]' id='login-field'>
    <label for='password-field'>Пароль</label>
    <input type='password' name='user[password]' id='password-field'>
    <input type='submit' value='Войти'>
</form>
<?php if (isset($errors)): ?>
    <div class='error-box'>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach ?>
    </div>
<?php endif ?>
