<h1><?= e($title) ?></h1>

<form method="post" action="<?= e($action) ?>" class="card" novalidate>
    <?= Csrf::field() ?>

    <label for="name">Name</label>
    <input id="name" name="name" type="text" maxlength="100" required value="<?= e($contact['name']) ?>">
    <?php if (isset($errors['name'])): ?><p class="error"><?= e($errors['name']) ?></p><?php endif; ?>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" maxlength="150" required value="<?= e($contact['email']) ?>">
    <?php if (isset($errors['email'])): ?><p class="error"><?= e($errors['email']) ?></p><?php endif; ?>

    <label for="phone">Phone</label>
    <input id="phone" name="phone" type="tel" maxlength="20" required value="<?= e($contact['phone']) ?>">
    <?php if (isset($errors['phone'])): ?><p class="error"><?= e($errors['phone']) ?></p><?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn">Save</button>
        <a href="<?= e(url('contacts')) ?>">Cancel</a>
    </div>
</form>
