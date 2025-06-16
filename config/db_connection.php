<?php

// Configuração de base de dados
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);
if($conn->connect_error) {
    die("Falha na conexão". $conn->connect_error);
} else {
   echo"Conexão com banco de dados estabelecida com sucesso!";
}

if($conn->query($sql) === TRUE) {
    echo "Base de dados criado com sucesso";
} else {
    echo "Erro ao criar base de dados: " . $conn->error;
}


$conn->close();

?>

