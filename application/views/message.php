<div class='message'>
    <h3><?= $header ?></h3>
    <?php if (isset($message)): ?>
            <p><?= $message ?></p>
    <?php endif ?>
    <?php if (isset($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <p><code><?= $error ?></code></p>
        <?php endforeach ?>
    <?php endif ?>
</div>
