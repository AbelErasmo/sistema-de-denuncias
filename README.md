# 📢 Sistema de Denúncias Anônimas

![Status](https://img.shields.io/badge/status-em%20desenvolvimento-yellow)
![Feito com](https://img.shields.io/badge/feito%20com-PHP%20%7C%20HTML%20%7C%20JS%20%7C%20MySQL-blue)
[![vercel](https://img.shields.io/badge/vercel-online-brightgreen)](https://sistema-de-denuncias-six.vercel.app)

Este sistema permite que cidadãos registrem denúncias de forma opcionalmente anônima, com a possibilidade de anexar evidências (imagens, vídeos, PDFs). Ele gera um protocolo exclusivo e protege o painel com login apenas para administradores.

---

## 🧰 Tecnologias

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8+, PDO para conexão segura
- **Banco de dados**: MySQL
- **Upload seguro**: `move_uploaded_file()` + validação de MIME e tamanho
- **Hospedagem frontend**: [Vercel]([https://vercel.com](https://sistema-de-denuncias-six.vercel.app/denuncia.html?))
- **Admin**: Login autenticado com `password_hash()` e `password_verify()`

---

## 🚀 Funcionalidades

- [x] Registro de denúncias com ou sem identificação
- [x] Upload individual de evidências com tipo e tamanho validados
- [x] Geração de protocolo único (ex: `DNC-20250701-5F3A9C`)
- [x] Painel administrativo protegido por sessão (`session_start`)
- [x] Estrutura escalável com tabelas: `users`, `denuncias`, `evidencias`

---

## 📂 Estrutura
📁 sistema-de-denuncias/ <br/>
  ├── config/ # Conexão com BD e criação de tabelas <br/>
  ├── css/ # Estilos customizados <br/>
  ├── js/ # Scripts de manipulação de inputs <br/>
  ├── uploads/evidencias/ # Pasta protegida para evidências <br/>
  ├── pages/ # Painel do admin e telas protegidas <br/>
  ├── denuncia.html # Formulário principal <br/>
  └── README.md

  
---

## 🛡️ Segurança

- Todos os dados tratados com `trim()` e sanitização
- Arquivos limitados por tipo (`jpg`, `png`, `mp4`, `pdf`) e tamanho (≤ 5MB)
- Não há conta para usuário comum — apenas o administrador acessa o painel
- Uploads são renomeados com `uniqid()` e armazenados em pasta isolada

---

## ⚙️ Instruções Locais

1. Clone o repositório:
   ```bash
   git clone https://github.com/AbelErasmo/sistema-de-denuncias.git

2. Execute o script config/db_connection.php para criar o banco de dados e as tabelas automaticamente.
<ul>
  <li>Acesse o formulário: http://localhost/sistema-de-denuncias/denuncia.html</li>
</ul>

3. Para login do admin:
  <ul>
    <li>Crie manualmente um admin na tabela users com password_hash()</li>
    <li>Acesse pages/login.php</li>
  </ul>

## 🔐 Exemplo de Protocolo Gerado
DNC-20250701-5F3A9C

## 👨‍💻 Autor
Desenvolvido por <br/> 
Erasmo Abel <br/>
Estudante de Ciência da Computação | Engajado em tecnologia com impacto social

## 📌 Licença
Este projeto é de código aberto. Contribuições são bem-vindas. Sinta-se livre para forkar, adaptar e implementar em causas que beneficiem a sociedade.







