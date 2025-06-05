<?php
session_start();
require_once '../config/Database.php';

$database = new Database();
$conn = $database->getConnection();
?> 
