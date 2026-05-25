<?php
session_start();
require __DIR__ . "/helpers.php";
require __DIR__ . "/db.php";
requireLogin();

$currentMonth = $_GET["month"] ?? date("Y-m");
$date = new DateTime($currentMonth . "-01");
$startDate = $date->format("Y-m-01");
$endDate = $date->format("Y-m-t");

$prevDate = clone $date;
$prevDate->modify("-1 month");
$prevMonth = $prevDate->format("Y-m");

$nextDate = clone $date;
$nextDate->modify("+1 month");
$nextMonth = $nextDate->format("Y-m");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST["amount"] ?? null;
    $month = $_POST["month"] ?? $currentMonth;
    $categoryId = $_POST["category_id"] ?? null;

    if ($amount === "" || $month === "" || $categoryId === "") {
        setFlash("error", "All fields are required");
        redirect("/budget.php");
    }
    if (!is_numeric($amount) || $amount <= 0) {
        setFlash("error", "Amount must be greater than 0");
        redirect("/budget.php");
    }
    $month = date("Y-m-01", strtotime($month));

    $checkStmt = $pdo->prepare("SELECT id from categories WHERE id = :id AND user_id = :user_id");
    $checkStmt->execute([
        ":id" => $categoryId,
        ":user_id" => $_SESSION["id"]
    ]);
    $row = $checkStmt->fetch();
    if ($row === false) {
        setFlash("error", "Invalid category");
        redirect("/budget.php");
    }
    $insertBudgetStmt = $pdo->prepare("INSERT INTO budgets (amount, month, user_id, category_id) 
                                        VALUES (:amount, :month, :user_id, :category_id)");
    $insertBudgetStmt->execute([
        ":amount" => $amount,
        ":month" => $month,
        ":user_id" => $_SESSION["id"],
        ":category_id" => $categoryId
    ]);
    redirect("/budget.php");
}
$stmt = $pdo->prepare("SELECT expenses.*, categories.name AS category FROM expenses 
                        JOIN categories 
                        ON expenses.category_id = categories.id
                        WHERE expenses.user_id = :user_id
                        AND expenses.date BETWEEN :start AND :end");
$stmt->execute([
    ":user_id" => $_SESSION["id"],
    ":start" => $startDate,
    ":end" => $endDate    
]);
$expenses = $stmt->fetchAll();

$totalStmt = $pdo->prepare("SELECT SUM(amount) AS total FROM expenses 
                            WHERE user_id = :user_id AND expenses.date BETWEEN :start AND :end");
$totalStmt->execute([
    ":user_id" => $_SESSION["id"],
    ":start" => $startDate,
    ":end" => $endDate
]);
$total = $totalStmt->fetch()["total"] ?? 0;

$fetchCategoryStmt = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = :user_id");
$fetchCategoryStmt->execute([":user_id" => $_SESSION["id"]]);
$categories = $fetchCategoryStmt->fetchAll();

$budgetStmt = $pdo->prepare("SELECT categories.id AS category_id, categories.name AS category_name,
                budgets.amount AS budget_amount, budgets.month, COALESCE(SUM(expenses.amount), 0) AS spent
                FROM budgets 
                JOIN categories 
                ON budgets.category_id = categories.id
                LEFT JOIN expenses 
                ON expenses.category_id = budgets.category_id
                AND expenses.user_id = budgets.user_id AND
                expenses.date >= budgets.month AND expenses.date <= LAST_DAY(budgets.month)
                WHERE budgets.user_id = :user_id
                AND budgets.month = :month
                GROUP BY budgets.id, categories.id, categories.name, budgets.amount, budgets.month");
$budgetStmt->execute([
    ":user_id" => $_SESSION["id"],
    ":month" => $startDate
]);
$budgets = $budgetStmt->fetchAll();

$selectedCategory = $_POST["category_id"] ?? null; 
$flash = flash();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Budget</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; padding: 2rem; }
        form { margin-top: 10px;margin-bottom: 10px;}
        h1   { font-size: 1.6rem; margin-bottom: 1.25rem; }
        table { width: 100%; border-collapse: collapse;background: #e2e2e2; border-radius: 8px;box-shadow: 0 2px 6px rgba(0,0,0,.1); overflow: hidden; }
        th { text-align: left;padding: .75rem 1rem; font-size: .9rem; }
        td { padding: .65rem 1rem; border-bottom: 1px solid #eee; color: #333; }
        tr:last-child td { border-bottom: none; }
        .flash { margin-bottom: 10px; }
        .flash.error { background-color:  #f2f0f0; color: #f43838}
        .flash.success { background-color:  #b5d0b8; color: #13521f}
        .a { display:flex; justify-content:space-between; margin-top: 10px;margin-bottom: 10px;}
    </style>
</head>
<body>
    <h2>Add budget</h2>
        <?php if ($flash) : ?>
        <div class="flash <?= htmlspecialchars($flash["type"])?>">
            <?= htmlspecialchars($flash["message"])?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="number" name="amount" placeholder="amount">
        <input type="month" name="month" value="<?= htmlspecialchars($currentMonth)?>">
        <select name="category_id">
            <option value="">Select category:</option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= htmlspecialchars($category["id"]) ?>"
                    <?= $selectedCategory == $category["id"] ? "selected" : ""?>><?= htmlspecialchars($category["name"]) ?>
                    </option>
                <?php endforeach; ?>
        </select>
        <button type="submit">Add budget</button>
    </form>
    <a href="/dashboard.php">Go back</a><br><br>

    <h3> Budget overview for <?= $date->format("F, Y")?> :</h3>

    <label><strong>Total Expenses</strong>: $<?= htmlspecialchars($total)?></label><br><br>

    <?php if (empty($budgets)) : ?>
        <p>No budgets set</p>
    <?php else : ?>
        <?php foreach ($budgets as $budget) : ?>
            <p><strong><?= htmlspecialchars($budget["category_name"])?></strong>: spent $<?= number_format($budget["spent"],2)?> out of $<?= number_format($budget["budget_amount"],2)?></p>
        <?php endforeach; ?>
    <?php endif; ?>
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
                <td><?= "$" . number_format($expense["amount"], 2) ?></td>
                <td><?= htmlspecialchars($expense["category"]) ?></td>
                <td><?= htmlspecialchars(formatDate($expense["date"])) ?></td>
                <td>
                    <form method="POST" action="/actions/delete_exp.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($expense["id"])?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="GET">
        <label>Select month:</label>
        <input type="month" name="month" value="<?= htmlspecialchars($currentMonth) ?>">
        <button type="submit">Go</button>
    </form>
    <div class="a">
        <a href="?month=<?=$prevMonth?>">Previous month</a>
        <a href="?month=<?=$nextMonth?>">Next month</a>
    </div>
</body>
</html>