<?php $this->title = 'Регистрация' ?>
<h1>Регистрация</h1>
<form method='POST' action='/account/sign-up' class='sign-form'>
    <label for='login-field'>Логин</label>
    <input type='text' name='user[username]' id='login-field'>
    <label for='password-field'>Пароль</label>
    <input type='password' name='user[password]' id='password-field'>
    <label for='re_password-field'>Подтвердите пароль</label>
    <input type='password' name='user[re_password]' id='re_password-field'>
    <input type='submit' value='Зарегистрироваться'>
</form>
<?php if (isset($errors)): ?>
    <div class='error-box'>
    <?php foreach ($errors as $error): ?>
        <p><?= $error ?></p>
    <?php endforeach ?>
    </div>
<?php endif ?>
