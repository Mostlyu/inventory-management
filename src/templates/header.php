<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0;
               color: #16242b; background: #f4f5f2; line-height: 1.6; }
        nav { background: #0c6b77; padding: 0.8rem 1.5rem; display: flex; gap: 1.6rem; }
        nav a { color: #fff; text-decoration: none; font-weight: 600; font-size: 0.95rem; }
        nav a:hover { text-decoration: underline; }
        main { max-width: 820px; margin: 0 auto; padding: 1.5rem; }
        h1 { margin-top: 0; }
        ul { padding-left: 1.2rem; }
        li { margin: 0.35rem 0; }
        form { margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
        input, select, button { padding: 0.45rem 0.6rem; font-size: 0.95rem; }
        button { background: #0c6b77; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; margin: 1rem 0; background: #fff; }
        th, td { text-align: left; padding: 0.55rem 0.7rem; border-bottom: 1px solid #dde1db; }
        th { background: #e4efef; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; color: #3c4a51; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
<nav>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a href="/?action=users">Benutzer</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['username'])): ?>
        <span style="color:#cde; margin-left:auto;">
            <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
        </span>
        <a href="/?action=logout">Logout</a>
    <?php endif; ?>
    <a href="/">Kategorien</a>
    <a href="/?action=items">Artikel</a>
    <a href="/?action=stockmovements">Bestandsbewegungen</a>
</nav>
<main>
