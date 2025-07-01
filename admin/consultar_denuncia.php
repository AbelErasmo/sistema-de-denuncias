<?php
$conn = require '../config/db_connection.php';

header('Content-Type: application/json');

if(!$conn) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'error',
        'message' => 'A conexão com a base de dados falhou'
    ]);
    exit();
}

try {
   if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['protocolo'])) {
        $protocolo = $_GET['protocolo'];

        if (!preg_match('/^DNC-\d{8}-[A-F0-9]{6}$/i', $protocolo)) {
            http_response_code(500);
             echo json_encode([
                'status' => 'bad_request',
                'message' => 'Formato de protocolo invalido.'
            ]);
            exit();
        }

        // Valida a data contida no protocolo
        $dataTexto = substr($protocolo, 4, 8);
        $dataValida = DateTime::createFromFormat('Ymd', $dataTexto);
        if (!$dataValida || $dataValida->format('Ymd') !== $dataTexto) {
            http_response_code(400);
            echo json_encode([
                'status' => 'bad_request',
                'message' => 'Data inválida no protocolo.'
            ]);
            exit();
        }

        $sql = "SELECT * FROM denuncias WHERE protocolo = :protocolo";
        $stmt = $conn->prepare($sql);
        $stmt = $conn->prepare($sql);
        $stmt->execute(['protocolo' => $protocolo]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if($dados) {
            echo json_encode([
                'status' => 'success',
                'dados' => $dados
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'not_found',
                'message' => 'Nenhuma denúncia encontrada com este protocolo.'
            ]);
        }
   } else {
        http_response_code(400);
        echo json_encode([
            'status' => 'bad_request',
            'message' => 'Protocolo não informado.'
        ]);
   }

} catch (Exception $e) {
   http_response_code(500);
   echo json_encode([
        'error' => 'eror',
        'message' => 'Erro inesperado!' . $e->getMessage()
   ]);
}