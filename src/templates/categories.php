<?php

use App\Controller\CategoryController;
use App\Repository\CategoryRepository;

require __DIR__ . '/header.php';

/** @var \App\Model\Category[] $categories */

if (($_GET['error'] ?? '') === 'in_use'): ?>
    <p style="color:#a23a2b;">Kann nicht gelöscht werden — es hängen noch Einträge daran.</p>
<?php elseif (($_GET['error'] ?? '') === 'forbidden'): ?>
    <p style="color:#a23a2b;">Keine Berechtigung.</p>
<?php elseif (($_GET['success'] ?? '') === 'deleted'): ?>
    <p style="color:#0a555f;">Gelöscht.</p>
<?php endif;
?>

<h2>Neue Kategorie</h2>
<?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
<?php endif; ?>
<form method="POST" action="?action=create">
    <input type="text" name="name" placeholder="Kategoriename" required>
    <input type="text" name="description" placeholder="Beschreibung">
    <button type="submit">Erstellen</button>
</form>

<h1>Kategorien</h1>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Beschreibung</th>
            <th>Aktion</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= htmlspecialchars($category->name)?></td>
                <td><?= htmlspecialchars($category->description) ?? '' ?></td>
                <td>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <form method="POST" action="?action=category_delete"
                        onsubmit="return confirm('Kategorie wirklich löschen?');">
                        <input type="hidden" name="id" value="<?= $category->id ?>">
                        <button type="submit">Löschen</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/footer.php'; ?>