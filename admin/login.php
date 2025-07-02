<?php
session_start();

$usuarios_validos = [
    'admin' => 'senha123',
    'erasmo' => 'denuncias2025'
];

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] === $senha) {
        $_SESSION['admin_logado'] = $usuario;
        header('Location: painel.php');
        exit();
    } else {
        $erro = "Credenciais inválidas.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="container-login">
        <h1>Painel Administrativo</h1>
        <p>Autenticação necessária para acesso restrito</p>

        <?php if ($erro): ?>
            <p style="color:red;"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Usuário:</label>
            <input type="text" name="usuario" required>
            
            <label>Senha:</label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
