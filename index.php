<?php
session_start();

require 'functions.php';

$mensagemErro = '';
$mensagemSucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $usuario = verificarLogin($email, $senha);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            header('Location: lista.php');
            exit;
        }  else {
            $mensagemErro = "Credenciais inválidas!";
        }
    } elseif (isset($_POST['register'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        if (registrarUsuario($nome, $email, $senha)) {
            $mensagemSucesso = "Registro bem-sucedido! Faça login.";
        } else {
            $mensagemErro = "Erro ao registrar usuário!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>ListIt</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container login-container">
            <form method="POST">
                <h2>Login</h2>
                <?php if (!empty($mensagemErro) && isset($_POST['login'])): ?>
                    <p class="message error-message"><?= $mensagemErro ?></p>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
        <div class="form-container register-container">
            <form method="POST">
                <h2>Registro</h2>
                <?php if (!empty($mensagemSucesso)): ?>
                    <p class="message success-message"><?= $mensagemSucesso ?></p>
                <?php endif; ?>
                <?php if (!empty($mensagemErro) && isset($_POST['register'])): ?>
                    <p class="message error-message"><?= $mensagemErro ?></p>
                <?php endif; ?>
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" name="register">Registrar</button>
            </form>
        </div>
    </div>
</body>
</html>
