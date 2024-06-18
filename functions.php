<?php
require 'banco.php';

function verificarLogin($email, $senha) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        return $usuario;
    }
    return false;
}

function registrarUsuario($nome, $email, $senha) {
    global $pdo;
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    return $stmt->execute([$nome, $email, $hash]);
}

function adicionarItem($nome, $descricao) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO itens (nome, descricao) VALUES (?, ?)");
    return $stmt->execute([$nome, $descricao]);
}

function editarItem($item_id, $nome, $descricao) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE itens SET nome = ?, descricao = ? WHERE id = ?");
    return $stmt->execute([$nome, $descricao, $item_id]);
}

function removerItem($item_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM itens WHERE id = ?");
    return $stmt->execute([$item_id]);
}

function obterItens() {
    global $pdo;
    return $pdo->query("SELECT * FROM itens")->fetchAll(PDO::FETCH_ASSOC);
}

function adicionarItemLista($usuario_id, $item_id, $quantidade) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO lista_itens (usuario_id, item_id, quantidade) VALUES (?, ?, ?)");
    return $stmt->execute([$usuario_id, $item_id, $quantidade]);
}

function editarItemLista($lista_id, $quantidade) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE lista_itens SET quantidade = ? WHERE id = ?");
    return $stmt->execute([$quantidade, $lista_id]);
}

function removerItemLista($lista_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM lista_itens WHERE id = ?");
    return $stmt->execute([$lista_id]);
}

function favoritarItemLista($lista_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE lista_itens SET favorito = NOT favorito WHERE id = ?");
    return $stmt->execute([$lista_id]);
}

function obterListaItens($usuario_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT lista_itens.id, itens.nome, lista_itens.quantidade, lista_itens.favorito FROM lista_itens JOIN itens ON lista_itens.item_id = itens.id WHERE lista_itens.usuario_id = ? ORDER BY lista_itens.favorito DESC, itens.nome ASC");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function removerLista($usuario_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM lista_itens WHERE usuario_id = ?");
    return $stmt->execute([$usuario_id]);
}

?>
