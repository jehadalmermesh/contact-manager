<h1>Admin login</h1>

<form method="post" action="<?= e(url('login/submit')) ?>" class="card">
    <?= Csrf::field() ?>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

    <label for="username">Username</label>
    <input id="username" name="username" type="text" required autocomplete="username" value="<?= e($username) ?>">

    <label for="password">Password</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">

    <div class="form-actions">
        <button type="submit" class="btn">Log in</button>
    </div>
</form>
