<?php

use App\Controller\StockMovementsController;
use App\Repository\StockMovementsRepository;
use App\Repository\ItemRepository;

require __DIR__ . '/header.php';

/** @var \App\Model\StockMovement[] $stockMovements */
/** @var \App\Model\Item[] $items */

?>
<h2>Neue Bestandsbewegung</h2>

<?php if (($_GET['error'] ?? '') === 'stock'): ?>
    <p style="color:#a23a2b; font-weight:600;">
        Nicht genug Bestand für diese Buchung.
    </p>
<?php endif; ?>

<?php if (($_GET['error'] ?? '') === 'quantity'): ?>
    <p style="color:#a23a2b; font-weight:600;">
        Ungültige Menge.
    </p>
<?php endif; ?>

<form method="POST" action="?action=stockmovement_create">
    <input type="text" name="quantity" placeholder="Menge" required>
    <select name="type" required>
        <option value="in">Eingang</option>
        <option value="out">Ausgang</option>
    </select>
    <input type="text" name="note" placeholder="Beschreibung">
    <button type="submit">Erstellen</button>
    <select name="item_id" required>
        <?php foreach ($items as $i): ?>
            <option value="<?= $i->id ?>"><?= htmlspecialchars($i->name) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<h1>Bestandsbewegungen</h1>
<table>
    <thead>
        <tr>
            <th>Artikel</th>
            <th>Typ</th>
            <th>Menge</th>
            <th>Notiz</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stockMovements as $movement): ?>
            <tr>
                <td><?= htmlspecialchars($movement->itemName ?? '') ?></td>
                <td><?= htmlspecialchars($movement->type) ?></td>
                <td><?= (int)$movement->quantity ?></td>
                <td><?= htmlspecialchars($movement->note ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php require __DIR__ . '/footer.php'; ?>