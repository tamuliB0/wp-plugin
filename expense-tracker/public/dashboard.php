<?php
session_start();
require __DIR__ . "/helpers.php";
require __DIR__ . "/db.php";
requireLogin();

$listStmt = $pdo->prepare("SELECT expenses.*, categories.name AS category 
                    FROM expenses
                    JOIN categories
                    ON expenses.category_id = categories.id
                    WHERE expenses.user_id = :user_id
                    ORDER BY date DESC");
$listStmt->execute([":user_id" => $_SESSION["id"]]);
$expenses = $listStmt->fetchAll();
$fetchCatergoryStmt = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = :user_id");
$fetchCatergoryStmt->execute([":user_id" => $_SESSION["id"]]);
$categories = $fetchCatergoryStmt->fetchAll();
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
        .a { display:flex; justify-content:flex-end}
    </style>
</head>
<body>
    <h1>Dashboard</h1>
    <p> Welcome,<strong><?= htmlspecialchars($_SESSION["user"] ?? "") ?></strong> </p>
    <a href="/logout.php" class="a">Logout</a>
    <h3>Add new expense</h3>
    <?php if ($flash) : ?>
        <div class="flash <?= htmlspecialchars($flash["type"])?>">
            <?= htmlspecialchars($flash["message"])?>
    </div>
    <?php endif ; ?>
    <form method="POST" action="/actions/add_exp.php">
        <input type="text" name="description" placeholder="Expense name">
        <input type="number" name="amount" placeholder="amount">
        <input type="text" name="new_category" placeholder="add new category">
        <input type="date" name="date" placeholder="dd-mm-yyyy">
        <select name="category_id">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?= htmlspecialchars($category["id"]) ?>">
                    <?= htmlspecialchars($category["name"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Expense</button>
    </form>
    <a href="categories.php" class="a">Manage Categories</a>
    <table>
        <thead>
            <tr>
                <th>Expense name</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense) : ?>
            <tr>
                <td><?= htmlspecialchars($expense["description"]) ?></td>
                <td><?= number_format($expense["amount"], 2) ?></td>
                <td><?= htmlspecialchars($expense["category"]) ?></td>
                <td><?= htmlspecialchars($expense["date"]) ?></td>
                <td>
                    <a href="/edit.php?id=<?= $expense["id"] ?>">Edit</a>
                    <form method="POST" action="/actions/delete_exp.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($expense["id"])?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>