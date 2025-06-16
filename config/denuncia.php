<?php

    // Criando uma base de dados e as tabelas se nao existirem

    function create_database( $db ) { 
       $sql = "CREATE DATABASE IF NOT EXISTS db_sistemaDenuncias;
        USE db_sistemaDenuncias";
      
        $db->query( $sql );
        

    }

    function db_create_table() {
        $sql = "CREATE DATABASE IF NOT EXISTS db_sistemaDenuncias;

        USE db_sistemaDenuncias;
        
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            senha VARCHAR(100) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            
            FOREIGN KEY (senha) REFERENCES denuncias(id_protocolo),
        );

        CREATE TABLE IF NOT EXISTS denuncias (
            id_protocolo INT(11) AUTO_INCREMENT GENERATED PRIMARY KEY NOT NULL UNIQUE,
            nome VARCHAR(100),
            email VARCHAR(100),
            telefone VARCHAR(15),
            tipo_denuncia ENUM() NOT NULL,
            descricao TEXT NOT NULL,
            provincia ENUM() NOT NULL,
            local_ocorrencia VARCHAR(100) NOT NULL,
            data_ocorrencia VARCHAR(100) NOT NULL, 
            data_denuncia TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
    }
    
?>