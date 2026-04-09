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