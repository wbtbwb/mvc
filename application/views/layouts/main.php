<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <link rel='stylesheet' type='text/css' href='/application/views/css/style.css'>
        <link rel='icon' type='image/png' href='/application/views/images/icon.png'>
        <title><?= $this->title ?></title>
    </head>
    <body>
        <div class='header-menu'>
            <div class='wrapper'>
                <ul><!--
                    --><li><a href='/'>Главная</a></li><!--
                    <?php if (!LOGGED): ?>
                        --><li><a href='/account/sign-up'>Регистрация</a></li><!--
                    <?php endif ?>
                    --><li>
                        <a href='<?= LOGGED ? '/account/sign-out' : '/account/sign-in' ?>'>
                            <?= LOGGED ? 'Выйти' : 'Войти' ?>
                        </a>
                    </li><!--
                --></ul>
            </div>
        </div>
        <div class='content'>
            <div class='wrapper'>
                <?= $template ?>
            </div>
        </div>
        <div class='footer'>
            <div class='wrapper'>
                © 2017
            </div>
        </div>
    </body>
</html>
