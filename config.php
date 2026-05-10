<?php

$db_name = "mysql:host=localhost;dbname=shop_db;charset=utf8mb4";
$username = "root";
$password = "";

try {
   $conn = new PDO($db_name, $username, $password);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   die("Database connection failed: " . $e->getMessage());
}

// Helper: sanitize string input (replaces deprecated FILTER_SANITIZE_STRING)
function clean($value) {
   return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

?>
