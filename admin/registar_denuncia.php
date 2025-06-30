<?php
    session_start();
    require './config/db_connection.php';

    function sanitizeInput($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_string($value) ? trim($value) : $value;
        }
        return $sanitized;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $protocolo = gerarProtocolo();
        $input = sanitizeInput($_POST);
       
        $stm = $conn->prepare("INSERT INTO denuncias
        (protocolo, nome, email, telefone, tipo_denuncia, descricao, provincia, local_ocorrencia, data_ocorrencia)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $success = $stm->execute([
            $protocolo,
            $input['nome'] ?? null,
            $input['email'] ?? null,
            $input['telefone'] ?? null,
            $input['tipo_denuncia'],
            $input['descricao'],
            $input['provincia'],
            $input['local_ocorrencia'],
            $input['data_ocorrencia']
        ]);

        echo  $success ? "Denúncia registrada com sucesso!" : "Erro ao registrar denúncia.";
    }

    /**
     * Funcao para armazenar envidencias e renomear
     * 
     */
    if (!empty($_FILES['file']['name'][0])) {
        $total = count($_FILES['file']['name']);

        for ($i = 0; $i < $total; $i++) {
            $tmp     = $_FILES['file']['tmp_name'][$i];
            $nome    = basename($_FILES['file']['name'][$i]);
            $ext     = pathinfo($nome, PATHINFO_EXTENSION);
            $tipo    = mime_content_type($tmp);

            // Ex: evd_64fe45f91a73a.jpg
            $nomeFinal = uniqid('evd_') . '.' . $ext;
            $destino   = __DIR__ . '/uploads/evidencias/' . $nomeFinal;

            // Valida tipo e tamanho
            if (
                in_array($ext, ['jpg', 'jpeg', 'png', 'mp4', 'pdf']) &&
                $_FILES['file']['size'][$i] <= 5 * 1024 * 1024
            ) {
                if (move_uploaded_file($tmp, $destino)) {
                    // Salvar caminho no banco se quiseres:
                    $stmt = $conn->prepare("INSERT INTO evidencias (id_denuncia, caminho_arquivo, tipo_arquivo) VALUES (?, ?, ?)");
                    $stmt->execute([$id_protocolo, $nomeFinal, $tipo]);
                }
            }
        }
    }

    /** Funcao para gerar Token ou protocolo da denuncia 
     * Onde:
     * DNC = prefixo fixo para "Denúncia"
     * 20250701 = data da denúncia (AAAA-MM-DD)
     * 5F3A9C = código aleatório hexadecimal
     * 
     */

    function gerarProtocolo() {
            $data = date('Ymd');
            $codigo = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            return "DNC-$data-$codigo";
    }

?>