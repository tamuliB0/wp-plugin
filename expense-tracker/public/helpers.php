<?php

function redirect(string $path): void 
{
    header("Location: $path");
    exit();
}

function setFlash(string $type, string $message): void
{
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message
    ];
}

function flash(): ?array
{
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $flash;
}

function flashAndRedirect(string $type, string $message, string $path): void
{
    setFlash($type, $message);
    redirect($path);
}

function isLoggedIn(): bool 
{
    return (isset($_SESSION["id"]));
}

function requireLogin(): void 
{
    if (!isLoggedIn()) {
        redirect("/login.php"); 
    }
}

function requirePost(string $path): void
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirect($path);
    }
}

function formatDate(string $date): string
{
    $date = new DateTime($date);
    return $date->format("d-m-Y");
}

function getCategoryId(PDO $pdo, string $newCategory, ?string $categoryId, int $userId): ?int
{
    if ($newCategory !== "") {
        $checkStmt = $pdo->prepare("SELECT id from categories WHERE name = :name AND user_id = :user_id");
        $checkStmt->execute([
            ":name" => $newCategory,
            ":user_id" => $userId
        ]);
        $category = $checkStmt->fetch();
        if ($category === false) {
            $insertStmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (:name, :user_id)");
            $insertStmt->execute([
                ":name" => $newCategory,
                ":user_id" => $userId
            ]);
            return (int) $pdo->lastInsertId();
        } else {
            return (int) $category["id"];
            }
    } elseif (!empty($categoryId)) {
        $checkStmt = $pdo->prepare("SELECT id from categories WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([
            ":id" => $categoryId,
            ":user_id" => $userId
        ]);
        $category = $checkStmt->fetch();
        if ($category === false) {
            flashAndRedirect("error", "Invalid category selected", "/dashboard.php");
        }
        return (int) $category["id"];
    }  else {
        flashAndRedirect("error", "Please select or add a category", "/dashboard.php");
        }
}

function handleFileUpload(array $file, 
    string $uploads,
    int $maxSize = 2 * 1024 * 1024, 
    array $allowedType = ["image/jpeg", "image/png", "application/pdf"]
): string
{
    if ($file["error"] !== UPLOAD_ERR_OK) {
        flashAndRedirect("error", "File upload failed", "/dashboard.php");
    }
    if ($file["size"] > $maxSize) {
        flashAndRedirect("error", "File size exceeded max limit", "/dashboard.php");
    } 
    if (!in_array($file["type"], $allowedType)) {
        flashAndRedirect("error", "File type not supported", "/dashboard.php");
    }
    $filename = $file["name"];
    if (!move_uploaded_file($file["tmp_name"], $uploads . $filename)) {
        flashAndRedirect("error", "Failed to save file", "/dashboard.php");
    }
    return $filename;
}

function fetchOrFail(PDO $pdo, string $sql, array $params, string $type, string $message, string $path): array
{
    $stmt = executeQuery($pdo, $sql, $params);
    $result = $stmt->fetch();
    if ($result === false) {
        flashAndRedirect($type, $message, $path);
    }
    return $result;
}

function executeQuery(PDO $pdo, string $sql, array $params = []): PDOStatement
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt;
}

function validateRequiredFields(array $fields, string $message, string $path): void
{
    foreach($fields as $value) {
        if ($value === "" || $value === null) {
            flashAndRedirect("error", $message, $path);
        }
    }
}