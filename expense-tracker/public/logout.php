<?php
require __DIR__ . "/helpers.php";
session_start();
unset($_SESSION);
session_destroy();
redirect("/login.php");