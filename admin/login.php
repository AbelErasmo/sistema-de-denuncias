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
    <link rel="stylesheet" href="../css/login.styles.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container-login">
         <div class="logotipo">
                <img src="../assets/logo.png" alt="Sistema de Denúncias">
            </a>
        </div>

        <div class="titulo">
            <h1>Autenticação</h1>
            <p>Acesso restrito</p>
        </div>

        <?php if ($erro): ?>
            <p style="color:red;"><?= $erro ?></p>
        <?php endif; ?>
        <form method="POST" action="">
           <div>
                <label for="usuario">Usuário:</label>
                <input type="text" class="obrigatorio" name="usuario" id="usuario">
           </div>
           <div>
                <label for="senha">Senha:</label>
                <input type="password" class="obrigatorio" name="senha" id="senha">
           </div>
            <button type="submit">Entrar</button>
        </form>

        <footer class="rodape">
            <p>&copy; 2025 Sistema de Denúncias. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a class="name" href="mailto:erasmosibinde@gmail.com">Erasmo Abel</a></p>
        </footer>
    </div>
    
    <script>
        const validacao = document.querySelector("form");

        if (validacao) {
            validacao.addEventListener("submit", (e) => {
                e.preventDefault();
                if (!login.validacao()) {
                    validacao.reset();
                }
            });
        }
        class Login {
            validacao() {
                const campos = document.querySelectorAll(".obrigatorio");
                let valido = true;

                campos.forEach(input => {
                    let erro = input.nextElementSibling;
                    if (!erro || !erro.classList.contains("error")) {
                        erro = document.createElement("span");
                        erro.className = "error";
                        input.insertAdjacentElement("afterend", erro);
                    }

                    if (!input.value.trim()) {
                        erro.textContent = "*Campo obrigatório";
                        input.classList.add("invalido");
                        valido = false;
                        input.focus();

                    } else {
                        erro.textContent = "";
                        input.classList.remove("invalido");
                    }
                    
                });

                return valido;
            }
        }
        const login = new Login();
    </script>
</body>
</html>
