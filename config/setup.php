<?php
$servername = "localhost";
$username = "root";
$password = "";
header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criação da base de dados
    $conn->exec("CREATE DATABASE IF NOT EXISTS db_sistemaDenuncias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE db_sistemaDenuncias");

    // Cria a tabela de usuários
    $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_reparticao INT NOT NULL,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_reparticao) REFERENCES reparticao(id) ON DELETE CASCADE
            );";
    $conn->exec($sql);

    // Cria a tabela repartição para o tratamento discentralizado das denúncias de acordo com o tipo
    $sql = "CREATE TABLE IF NOT EXISTS reparticao (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome_reparticao VARCHAR(100) NOT NULL,
                provincia VARCHAR(255) NOT NULL,
                data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
    $conn->exec($sql);

    // Cria a tabela de denúncias
    $sql = "CREATE TABLE IF NOT EXISTS denuncias (
                id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
                protocolo VARCHAR(20) UNIQUE NOT NULL,
                nome VARCHAR(100) DEFAULT NULL,
                email VARCHAR(255) DEFAULT NULL,
                telefone VARCHAR(100) DEFAULT NULL,
                tipo_denuncia ENUM('corrupcao', 'sequestro', 'violencia', 'trafico', 'homicidio', 'abuso', 'assalto', 'fraude', 'descriminacao', 'outro') NOT NULL,
                descricao TEXT NOT NULL,
                provincia ENUM('Cidade de Maputo', 'Maputo Provincia', 'Gaza', 'Inhambane', 'Sofala', 'Manica', 'Tete', 'Zambezia', 'Nampula', 'Niassa', 'Cabo Delgado') NOT NULL,
                local_ocorrencia VARCHAR(100) NOT NULL,
                data_ocorrencia VARCHAR(100) NOT NULL,
                data_denuncia TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
    $conn->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS evidencias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_denuncia INT NOT NULL,
        caminho_arquivo VARCHAR(255) NOT NULL,
        tipo_arquivo VARCHAR(50),
        data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_denuncia) REFERENCES denuncias(id_denuncia) ON DELETE CASCADE
    );";
    $conn->exec($sql);

    $conn->exec("CREATE INDEX idx_denuncias_data ON denuncias(data_ocorrencia)");
    $conn->exec("CREATE INDEX idx_denuncias_prov_data ON denuncias(provincia, data_ocorrencia)");

    echo "Base de dados e tabelas criadas com sucesso! <br/>";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
?>
