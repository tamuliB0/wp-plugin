<?php 
require __DIR__ . "/db.php";
require __DIR__ . "/functions.php";

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    header("Location: /index.php");
    exit();
}
$id = (int) $_GET["id"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) $_POST["id"] ?? null;
    $title = trim($_POST["title"]) ?? "";
    $url = trim($_POST["url"]) ?? "";
    $notes = trim($_POST["notes"]) ?? "";

    $errors = validateBookmark($title, $url);
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE bookmarks SET title = :title, url = :url, notes = :notes WHERE id = :id");
        $stmt->execute([
            ":title" => $title,
            ":url" => $url,
            ":notes" => $notes,
            ":id" => $id
        ]);
        header("Location: /index.php");
        exit();
    }
}
$stmt = $pdo->prepare("SELECT title, url, notes FROM bookmarks WHERE id = :id");
$stmt->execute([":id" => $id]);
$bookmark = $stmt->fetch();
if ($bookmark === false) {
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit bookmarks</title>
</head>
<body>
    <?php if (!empty($errors)) :?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <h2>Edit bookmark</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" 
        value="<?= isset($title) ? htmlspecialchars($title) : htmlspecialchars($bookmark["title"]) ?>" required>

        <label for="url">Url:</label>
        <input type="text" name="url" id="url"
        value="<?= isset($url) ? htmlspecialchars($url) : htmlspecialchars($bookmark["url"])  ?>" required>

        <label for="notes">Notes:</label>
        <input type="text" name="notes" id="notes"
        value="<?= isset($notes) ? htmlspecialchars($notes) : htmlspecialchars($bookmark["notes"]) ?>">
        <input type="submit" value="Save">
    </form>
</body>
</html>