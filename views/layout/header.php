<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Manager</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url('contacts')) ?>">Contact Manager</a>
    <nav>
        <?php if (Auth::check()): ?>
            <span class="muted">Signed in as <?= e(Auth::username()) ?></span>
            <form method="post" action="<?= e(url('logout')) ?>" class="inline">
                <?= Csrf::field() ?>
                <button type="submit" class="link">Log out</button>
            </form>
        <?php else: ?>
            <a href="<?= e(url('login')) ?>">Admin login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
    <?php if ($msg = flash()): ?>
        <div class="flash"><?= e($msg) ?></div>
    <?php endif; ?>
