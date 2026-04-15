<?php
session_start();
require __DIR__ . "/helpers.php";
require __DIR__ . "/db.php";
requireLogin();

$stmt = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = :user_id");
$stmt->execute([":user_id" => $_SESSION["id"]]);
$categories = $stmt->fetchAll();
$flash = flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; padding: 2rem; }
        form { margin-bottom: 10px;}
        h1   { font-size: 1.6rem; margin-bottom: 1.25rem; }
        table { width: 100%; border-collapse: collapse;background: #e2e2e2; border-radius: 8px;box-shadow: 0 2px 6px rgba(0,0,0,.1); overflow: hidden; }
        th { text-align: left;padding: .75rem 1rem; font-size: .9rem; }
        td { padding: .65rem 1rem; border-bottom: 1px solid #eee; color: #333; }
        tr:last-child td { border-bottom: none; }
        .flash { margin-bottom: 10px; }
        .flash.error { background-color:  #f2f0f0; color: #f43838}
        .flash.success { background-color:  #b5d0b8; color: #13521f}
    </style>
</head>
<body>
    <h1>Your categories</h1>
    <a href="/dashboard.php" style="display:inline-block; margin-bottom:15px">Go back</a>
    <?php if ($flash) : ?>
        <div class="flash <?= htmlspecialchars($flash["type"])?>">
            <?= htmlspecialchars($flash["message"])?>
        </div>
    <?php endif ; ?>
    <?php foreach ($categories as $category): ?>
    <form method="POST" action="/actions/rename_category.php">
        <input type="hidden" name="id" value="<?= $category["id"] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($category["name"]) ?>">
        <button type="submit">Rename</button>
    </form>
    <form method="POST" action="/actions/delete_category.php">
        <input type="hidden" name="id" value="<?= $category["id"] ?>">
        <button type="submit">Delete</button>
    </form>
    <?php endforeach; ?>

</body>
</html>