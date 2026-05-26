<?php 
require __DIR__ . "/bootstrap.php";

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    redirect("/dashboard.php");
}
$id = (int) $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["id"]) || !ctype_digit($_POST["id"])) {
        redirect("/dashboard.php");
    }
    $postId = (int) $_POST["id"];
    $row = fetchOrFail(
        $pdo,
        "SELECT is_recurring AS recur FROM expenses WHERE id = :id AND user_id = :user_id",
        array(
            ":id" => $postId,
            ":user_id" => $_SESSION["id"]
        ),
        "error",
        "No expense found",
        "/dashboard.php"
    );
    $currentStatus = (int) $row["recur"];
    $status = ($currentStatus === 1) ? 0 : 1;
    executeQuery(
        $pdo,
        "UPDATE expenses SET is_recurring = :status WHERE id = :id AND user_id = :user_id",
        array(
            ":status" => $status, 
            ":id" => $postId,
            ":user_id" => $_SESSION["id"]
        )
    );
    $message = $status ? "Expense is recurring monthly" : "Recurring is disabled";
    flashAndRedirect("success", $message, "/details.php?id=" . $postId);
}
$expense = fetchOrFail(
    $pdo,
    "SELECT expenses.*, categories.name AS category 
    FROM expenses
    JOIN categories
    ON expenses.category_id = categories.id 
    WHERE expenses.id = :id AND expenses.user_id = :user_id",
    array(
        ":id" => $id,
        ":user_id" => $_SESSION["id"]
    ),
    "error",
    "",
    "/dashboard.php"
);
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
    <h1>Expense details:</h1>
    <a href="/dashboard.php" style="display:inline-block; margin-bottom:15px">Go back</a>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?=htmlspecialchars($id)?>">
        <button type="submit"><?= $expense["is_recurring"] ? "Stop recurring" : "Set expense as recurring"?></button>
    </form>
    <?php if ($flash) : ?>
        <div class="flash <?= htmlspecialchars($flash["type"])?>">
            <?= htmlspecialchars($flash["message"])?>
        </div>
    <?php endif ; ?>

    <p><strong>Recurring:</strong> <?= htmlspecialchars($expense["is_recurring"] ? "Monthly" : "No")?></p>
    
    <p><strong>Expense: </strong><?= htmlspecialchars($expense["description"])?></p>
    <p><strong>Amount: </strong><?= htmlspecialchars("$" . $expense["amount"])?></p>
    <p><strong>Category: </strong><?= htmlspecialchars($expense["category"])?></p>
    <p><strong>Date: </strong><?= htmlspecialchars(formatDate($expense["date"]))?></p>
    <p><strong>Receipt: </strong></p>

    <?php if (!empty($expense["receipt"])): ?>
        <p>
            <a href="/uploads/<?= htmlspecialchars($expense["receipt"]) ?>" target="_blank"><?= htmlspecialchars($expense["receipt"])?></a>
        </p>
        <form method="POST" action="/actions/delete_attach.php">
            <input type="hidden" name="id" value="<?=htmlspecialchars($id)?>">
            <button type="submit">Delete</button>
        </form>
    <?php endif; ?>
</body>
</html>