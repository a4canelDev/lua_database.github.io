<?php
session_start();

// Проверяем, вошел ли пользователь
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Пользователь уже вошел, перенаправляем на главную страницу
    header("location: main.php");
    exit;
}

// Подключаемся к базе данных
$servername = "localhost";
$username = "a0908526_lua_database";
$password = "ПАРОЛ19219219mЬ";
$dbname = "a0908526_lua_database";

$conn =
