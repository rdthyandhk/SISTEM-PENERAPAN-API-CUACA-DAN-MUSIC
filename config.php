<?php
// Ubah sesuai MySQL kamu
const DB_HOST='localhost', DB_NAME='cuaca_musik', DB_USER='root', DB_PASS='';
session_start();
$db=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$_SESSION['csrf']=$_SESSION['csrf']??bin2hex(random_bytes(16));
function e($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function need_login(){if(empty($_SESSION['uid'])){header('Location: login.php');exit;}}
