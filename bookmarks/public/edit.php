<?php 
require __DIR__ . "/db.php";
require __DIR__ . "/functions.php";

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    header("Location: /index.php");
    exit();
}
$id = (int) $_GET["id"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST["id"] ?? null);
    $title = trim($_POST["title"] ?? "");
    $url = trim($_POST["url"] ?? "");
    $notes = trim($_POST["notes"] ?? "");
    $selectedTag = $_POST["tags"] ?? [];

    $errors = validateBookmark($title, $url);
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE bookmarks SET title = :title, url = :url, notes = :notes WHERE id = :id");
        $stmt->execute([
            ":title" => $title,
            ":url" => $url,
            ":notes" => $notes,
            ":id" => $id
        ]);
        $stmt = $pdo->prepare("DELETE FROM bookmark_tags WHERE bookmark_id = ?");
        $stmt->execute([$id]);
        $Tagstmt = $pdo->prepare("INSERT INTO bookmark_tags (bookmark_id, tag_id) VALUES (?, ?)");
        foreach ($selectedTag as $tagId) {
            $Tagstmt->execute([$id, $tagId]);
        }
        header("Location: /index.php");
        exit(); 
    } else {
        $currentTags = $selectedTag;
    }
}
$stmt = $pdo->prepare("SELECT title, url, notes FROM bookmarks WHERE id = :id");
$stmt->execute([":id" => $id]);
$bookmark = $stmt->fetch();
if ($bookmark === false) {
    header("Location: /index.php");
    exit();
}
$stmt = $pdo->query("SELECT * FROM tags");
$tags = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT tag_id FROM bookmark_tags WHERE bookmark_id = ?");
$stmt->execute([$id]);
$currentTags = array_column($stmt->fetchAll(), "tag_id");
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
        value="<?= htmlspecialchars($title ?? $bookmark["title"]) ?>" required>

        <label for="url">Url:</label>
        <input type="text" name="url" id="url"
        value="<?= htmlspecialchars($url ?? $bookmark["url"])  ?>" required>

        <label for="notes">Notes:</label>
        <input type="text" name="notes" id="notes"
        value="<?= htmlspecialchars($notes ?? $bookmark["notes"]) ?>">

        <p>Select Tag:</p>
        <?php foreach ($tags as $tag) : ?>
            <label>
                <input type="checkbox" name="tags[]" 
                value="<?= $tag["id"] ?>" <?= in_array($tag["id"], $currentTags) ? "checked" : ""?>>
                <?=htmlspecialchars($tag["name"]) ?>
            </label>
        <?php endforeach; ?>
        <input type="submit" value="Save">
    </form>
</body>
</html>