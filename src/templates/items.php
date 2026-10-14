<?php

use App\Controller\ItemController;
use App\Repository\ItemRepository;
use App\Repository\CategoryRepository;

require __DIR__ . '/header.php';

// this is a template file, so it expects to have $items and $categories available to it.
// These are provided by the ItemController's index() method,
// which calls the ItemRepository's findAll() method to get the items,
// and the CategoryRepository's findAll() method to get the categories.
/** @var \App\Model\Item[] $items */
/** @var \App\Model\Category[] $categories */

if (($_GET['error'] ?? '') === 'in_use'): ?>
    <p style="color:#a23a2b;">Kann nicht gelöscht werden — es hängen noch Einträge daran.</p>
<?php elseif (($_GET['error'] ?? '') === 'forbidden'): ?>
    <p style="color:#a23a2b;">Keine Berechtigung.</p>
<?php elseif (($_GET['success'] ?? '') === 'deleted'): ?>
    <p style="color:#0a555f;">Gelöscht.</p>
<?php endif;

?>

<h2>Neues Item</h2>

<?php if (($_GET['error'] ?? '') === 'sku'): ?>
    <p style="color:#a23a2b; font-weight:600;">
        Diese SKU existiert bereits. Bitte wählen Sie eine andere.
    </p>
<?php endif; ?>

<form method="POST" action="?action=item_create">
    <input type="text" name="name" placeholder="Itemname" required>
    <input type="text" name="description" placeholder="Beschreibung">
    <input type="text" name="sku" placeholder="SKU" required>
    <button type="submit">Erstellen</button>
    <select name="category_id" required>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
<?php endif; ?>
<form method="GET">
    <input type="hidden" name="action" value="items">
    <input type="text" name="search" placeholder="Suche Name/SKU"
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <select name="category">
        <option value="">Alle Kategorien</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c->id ?>"
                <?= (($_GET['category'] ?? '') == $c->id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c->name) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Suchen</button>
</form>

<h1>Items</h1>
<a href="?action=items_export">CSV Export</a>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Stock Level</th>
            <th>SKU</th>
            <th>Kategorie</th>
            <th>Beschreibung</th>
            <th>Aktion</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item->name) ?></td>
            <td><?= (int)($stockLevels[$item->id] ?? 0) ?></td>
            <td><?= htmlspecialchars($item->sku) ?></td>
            <td><?= htmlspecialchars($item->categoryName ?? '') ?></td>
            <td><?php if ($item->description): ?>
                <br><?= htmlspecialchars($item->description ?? '') ?>
            <?php endif; ?></td>
            <td>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <form method="POST" action="?action=item_delete"
                    onsubmit="return confirm('Item wirklich löschen?');">
                    <input type="hidden" name="id" value="<?= $item->id ?>">
                    <button type="submit">Löschen</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>

<?php require __DIR__ . '/footer.php'; ?>