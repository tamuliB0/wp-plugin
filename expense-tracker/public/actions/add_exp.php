<?php
session_start();
$dir = dirname(__DIR__);
require $dir . "/db.php";
require $dir . "/helpers.php";

requireLogin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/dashboard.php");
}
    $description = trim($_POST["description"] ?? "");
    $amount = is_numeric($_POST["amount"]) ? $_POST["amount"] : null;
    $newCategory = trim($_POST["new_category"] ?? "");
    $categoryId = $_POST["category_id"] ?? null;
    $date = $_POST["date"] ?? "";

    if ($description === "" || $amount <= 0 || $category === "" || $date === "") {
        setFlash("error", "Missing fields");
        redirect("/dashboard.php");
    }
    if ($newCategory !== "") {
        $checkStmt = $pdo->prepare("SELECT id from categories WHERE name = :name AND user_id = :user_id");
        $checkStmt->execute([
            ":name" => $newCategory,
            ":user_id" => $_SESSION["id"]
        ]);
        $category = $checkStmt->fetch();
        if ($category === false) {
            $insertStmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (:name, :user_id)");
            $insertStmt->execute([
                ":name" => $newCategory,
                ":user_id" => $_SESSION["id"]
            ]);
            $categoryId = $pdo->lastInsertId();
        } else {
            $categoryId = $category["id"];
        }
    } elseif (!empty($categoryId)) {
        $checkStmt = $pdo->prepare("SELECT id from categories WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([
            ":id" => $categoryId,
            ":user_id" => $_SESSION["id"]
        ]);
        $category = $checkStmt->fetch();
        if ($category === false) {
            setFlash("error", "Invalid category selected");
            redirect("/dashboard.php");
        } 
    }  else {
            setFlash("error", "Please select or add a category");
            redirect("/dashboard.php");
        }
    $insertExStmt = $pdo->prepare(
        "INSERT INTO expenses (amount, description, date, category_id, user_id) 
        VALUES (:amount, :description, :date, :category_id, :user_id)"
    );
    $insertExStmt->execute([
        ":amount" => $amount, 
        ":description" => $description, 
        ":date" => $date,
        ":category_id" => $categoryId,
        ":user_id" => $_SESSION["id"]
    ]);
    redirect("/dashboard.php");