<?php
//ob_start();

try {
    $dbConfig = require 'config/db.php';

    $connection = new PDO(
        "mysql:dbname={$dbConfig['name']}; host={$dbConfig['host']}",
        "{$dbConfig['username']}",
        "{$dbConfig['password']}");

    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}