<?php require __DIR__ . '/header.php'; ?>
<h1>Login</h1>
<?php if (($_GET['error'] ?? '') === '1'): ?>
    <p style="color:#a23a2b; font-weight:600;">Ungültiger Benutzername oder Passwort.</p>
<?php endif; ?>
<form method="POST" action="?action=login_submit">
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <button type="submit">Anmelden</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>