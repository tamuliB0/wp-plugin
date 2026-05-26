<?php
session_start();
require __DIR__ . "/helpers.php";
unset($_SESSION);
session_destroy();
redirect("/login.php");