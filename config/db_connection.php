<?php
$host = "localhost";
$db = "db_sistemaDenuncias";
$user = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na conexão com a base de dados: ' . $e->getMessage()
    ]);
    exit;
}
