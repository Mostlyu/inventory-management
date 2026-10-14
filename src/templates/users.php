<?php require __DIR__ . '/header.php'; ?>
<?php /** @var \App\Model\User[] $users */ ?>
<h1>Benutzer anlegen</h1>
<?php if (($_GET['error'] ?? '') === 'username_taken'): ?>
    <p style="color:#a23a2b;">Benutzername existiert bereits.</p>
<?php elseif (($_GET['error'] ?? '') === 'invalid'): ?>
    <p style="color:#a23a2b;">Bitte alle Felder korrekt ausfüllen.</p>
<?php elseif (($_GET['success'] ?? '') === '1'): ?>
    <p style="color:#0a555f;">Benutzer angelegt.</p>
<?php endif; ?>
<form method="POST" action="?action=user_create">
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <select name="role" required>
        <option value="lagermitarbeiter">Lagermitarbeiter</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Anlegen</button>
</form>
<h2>Benutzer</h2>
<table>
    <thead><tr><th>Benutzer</th><th>Rolle</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u->username) ?></td>
                <td><?= htmlspecialchars($u->role) ?></td>
                <td>
                    <?php if ($u->id !== ($_SESSION['user_id'] ?? 0)): ?>
                        <form method="POST" action="?action=user_delete"
                              onsubmit="return confirm('Benutzer wirklich löschen?');">
                            <input type="hidden" name="id" value="<?= $u->id ?>">
                            <button type="submit">Löschen</button>
                        </form>
                    <?php else: ?>
                        (admin)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>