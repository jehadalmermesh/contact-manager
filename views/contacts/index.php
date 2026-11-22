<div class="page-head">
    <h1>Contacts (<?= count($contacts) ?>)</h1>
    <?php if (Auth::check()): ?>
        <a class="btn" href="<?= e(url('contacts/create')) ?>">+ Add contact</a>
    <?php endif; ?>
</div>

<?php if (!$contacts): ?>
    <p class="muted">No contacts yet.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Name</th><th>Email</th><th>Phone</th><th>Added</th>
            <?php if (Auth::check()): ?><th></th><?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($contacts as $c): ?>
            <tr>
                <td><?= e($c['name']) ?></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['phone']) ?></td>
                <td><?= e(date('d M Y', strtotime($c['created_at']))) ?></td>
                <?php if (Auth::check()): ?>
                    <td class="actions">
                        <a href="<?= e(url('contacts/edit', ['id' => $c['id']])) ?>">Edit</a>
                        <form method="post" action="<?= e(url('contacts/delete', ['id' => $c['id']])) ?>" class="inline">
                            <?= Csrf::field() ?>
                            <button type="submit" class="link danger">Delete</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
