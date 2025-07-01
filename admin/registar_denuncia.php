<?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');

    header('Content-Type: application/json');
    $conn = require '../config/db_connection.php';

    if (!$conn) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Conexão falhou.']);
        exit;
    }
    
    function sanitizeInput($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_string($value) ? trim($value) : $value;
        }
        return $sanitized;
    }

   try {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {        
            $protocolo = gerarProtocolo();
            $input = sanitizeInput($_POST);
        
            $stm = $conn->prepare("INSERT INTO denuncias(protocolo, nome, email, telefone, tipo_denuncia, descricao, provincia, local_ocorrencia, data_ocorrencia)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $success = $stm->execute([
                $protocolo,
                $input['nome'] ?? null,
                $input['endereco'] ?? null,
                $input['telefone'] ?? null,
                $input['tipo_denuncia'],
                $input['descricao'],
                $input['provincia'],
                $input['local_ocorrencia'],
                $input['data_ocorrencia'] = str_replace('T', ' ', $input['data_ocorrencia'])
            ]);

            if ($success) {
                $id_denuncia = $conn->lastInsertId();

                // Verifica se ao menos um arquivo foi enviado
                if (!empty($_FILES['file']['name']) && is_array($_FILES['file']['name'])) {
                    $total = count($_FILES['file']['name']);

                    for ($i = 0; $i < $total; $i++) {
                        if ($_FILES['file']['error'][$i] !== 0) {
                            continue; // pula arquivos com erro (ex: nenhum arquivo selecionado)
                        }

                        $tmp      = $_FILES['file']['tmp_name'][$i];
                        $nome     = basename($_FILES['file']['name'][$i]);
                        $ext      = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
                        $tipo     = mime_content_type($tmp);
                        $tamanho  = $_FILES['file']['size'][$i];

                        // Gera nome único
                        $nomeFinal = uniqid('evd_') . '.' . $ext;
                        $destino   = __DIR__ . '/../uploads/evidencias/' . $nomeFinal;

                        // Validação de tipo e tamanho
                        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'mp4', 'pdf'];
                        $tamanhoMaxMB = 5 * 1024 * 1024;

                        if (in_array($ext, $extensoes_permitidas) && $tamanho <= $tamanhoMaxMB) {
                            if (!move_uploaded_file($tmp, $destino)) {
                                error_log("Falha ao mover arquivo para: $destino");
                                continue;
                            }

                            // Salva no banco a referência da evidência
                            $stmt = $conn->prepare("INSERT INTO evidencias(id_denuncia, caminho_arquivo, tipo_arquivo) VALUES (?, ?, ?)");
                            $stmt->execute([$id_denuncia, $nomeFinal, $tipo]);
                        }
                    }
                }
                echo json_encode([
                    'status' => 'success',
                    'protocolo' => $protocolo
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao registrar denúncia.'
                ]);
            }
        } else {
            echo json_encode([
                "status" => "info", 
                "message" => "Este endpoint aceita apenas POST."
            ]);
        }
   } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro inesperado: ' . $e->getMessage()
        ]);
   }

    /** Função para gerar Token ou protocolo da denuncia 
     * Onde:
     * DNC = prefixo fixo para "Denúncia"
     * 20250701 = data da denúncia (AAAAMMDD)
     * 5F3A9C = código aleatório hexadecimal
     * 
     */
    function gerarProtocolo() {
        $data = date('Ymd');
        $codigo = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        return "DNC-$data-$codigo";
    }
?>