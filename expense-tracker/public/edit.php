<?php
session_start();
require __DIR__ . "/db.php";
require __DIR__ . "/helpers.php";

requireLogin();
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    redirect("/dashboard.php");
}
$id = (int) $_GET["id"];
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ":id" => $id,
    ":user_id" => $_SESSION["id"]
]);
$expense = $stmt->fetch();
if (!$expense) {
    redirect("/dashboard.php");
}
$fetchCategoryStmt = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = :user_id");
$fetchCategoryStmt->execute([":user_id" => $_SESSION["id"]]);
$categories = $fetchCategoryStmt->fetchAll();
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
    <h1>Edit your expense</h1>
    <?php if ($flash) : ?>
        <div class="flash <?= htmlspecialchars($flash["type"])?>">
            <?= htmlspecialchars($flash["message"])?>
    </div>
    <?php endif ; ?>
    <form method="POST" action="/actions/update_exp.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($expense["id"])?>">
        <input type="text" name="description" value="<?= htmlspecialchars($expense["description"] ?? "") ?>"
        placeholder="Expense name">
        <input type="number" name="amount" value="<?= htmlspecialchars($expense["amount"] ?? "") ?>"
        placeholder="amount">
        <input type="text" name="new_category" value="" placeholder="add new category">
        <input type="date" name="date" value="<?= htmlspecialchars($expense["date"] ?? "") ?>"
        placeholder="dd-mm-yyyy">
        <select name="category_id">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= htmlspecialchars($category["id"]) ?>"
                <?=$category["id"] == $expense["category_id"] ? "selected" : ""?>>
                    <?= htmlspecialchars($category["name"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Update</button>
    </form>
</body>
</html>