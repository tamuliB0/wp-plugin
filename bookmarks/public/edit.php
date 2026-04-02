<?php 
require __DIR__ . "/db.php";
require __DIR__ . "/functions.php";

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    header("Location: /index.php");
    exit();
}
$id = (int) $_GET["id"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postId = ($_POST["id"] ?? null);
    if ($postId === null || !ctype_digit($postId)) {
        exit();
    }
    $postId = (int) $postId; 
    $title = trim($_POST["title"] ?? "");
    $url = trim($_POST["url"] ?? "");
    $notes = trim($_POST["notes"] ?? "");
    $selectedTags = $_POST["tags"] ?? [];
    $newTag = trim($_POST["new_tag"] ?? "");


    $errors = validateBookmark($title, $url);
    if (empty($errors)) {
        $updateBookmarkStmt = $pdo->prepare("UPDATE bookmarks SET title = :title, url = :url, notes = :notes WHERE id = :id");
        $updateBookmarkStmt->execute([
            ":title" => $title,
            ":url" => $url,
            ":notes" => $notes,
            ":id" => $postId
        ]);
        if ($newTag !== "") {
        $selectedTags[] = findOrCreateTag($pdo, $newTag);
        }
        saveBookmarkTags($pdo, $postId, $selectedTags);
        header("Location: /index.php");
        exit(); 
    } else {
        $currentTags = $selectedTags;
    }
}
$fetchStmt = $pdo->prepare("SELECT title, url, notes FROM bookmarks WHERE id = :id");
$fetchStmt->execute([":id" => $id]);
$bookmark = $fetchStmt->fetch();
if ($bookmark === false) {
    header("Location: /index.php");
    exit();
}
$fetchTagsStmt = $pdo->query("SELECT * FROM tags");
$tags = $fetchTagsStmt->fetchAll();

$currentTagStmt = $pdo->prepare("SELECT tag_id FROM bookmark_tags WHERE bookmark_id = :id");
$currentTagStmt->execute([":id" => $id]);
$currentTags = array_column($currentTagStmt->fetchAll(), "tag_id");
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
        <input type="hidden" name="id" value="<?= htmlspecialchars($postId ?? $id) ?>">

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
        <input type="text" name="new_tag" placeholder="enter new tag" value="<?= htmlspecialchars($newTag ?? "") ?>">
        <input type="submit" value="Save">
    </form>
</body>
</html>