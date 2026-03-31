<?php 
require __DIR__ . "/db.php";
require __DIR__ . "/functions.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
    $url = isset($_POST["url"]) ? trim($_POST["url"]) : "";
    $notes = isset($_POST["notes"]) ? trim($_POST["notes"]) : "";

    $errors = validateBookmark($title, $url);

    if (empty($errors)) {
        $stmt = $pdo->prepare( "INSERT INTO bookmarks (title, url, notes) VALUES (:title, :url, :notes)");
        $stmt->execute([
            ":title" => $title,
            ":url" => $url,
            ":notes" => $notes
        ]);
        header("Location: /index.php");
        exit();
    }
}
$stmt = $pdo->query("SELECT * FROM bookmarks ORDER BY created_at DESC");
$bookmarks = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarks</title>
</head>
<body>
    <ul>
        <?php foreach ($bookmarks as $bookmark) : ?>
            <li> 
                <div>
                    <?= htmlspecialchars($bookmark["title"]) ?> - 
                    <a href="<?= htmlspecialchars($bookmark["url"])?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($bookmark["url"])?></a>
                </div>
                <div>
                    <a href="edit.php?id=<?= htmlspecialchars($bookmark["id"]) ?>">Edit</a>

                    <form method="POST" action="delete.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($bookmark["id"]) ?>"> 
                        <input type="submit" value="Delete">
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (!empty($errors)) :?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <h2>Add new bookmark</h2>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="url">Url:</label>
        <input type="text" name="url" id="url" required>

        <label for="notes">Notes:</label>
        <input type="text" name="notes" id="notes">
        <input type="submit" value="Add Bookmark">
    </form>
</body>
</html>