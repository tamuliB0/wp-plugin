<?php 
require __DIR__ . "/db.php";
$stmt = $pdo->query("SELECT * FROM bookmarks");
$bookmarks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <ul>
        <?php foreach ($bookmarks as $bookmark) : ?>
            <li> 
                <?= htmlspecialchars($bookmark["title"]) ?> - 
                <a href="<?= htmlspecialchars($bookmark["url"])?>">
                    <?= htmlspecialchars($bookmark["url"])?>
                </a>
            </li>
        <?php endforeach ;?>
    </ul>
</body>
</html>