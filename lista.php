<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require 'functions.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario_tipo = $_SESSION['usuario_tipo'];
$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    if ($usuario_tipo == 'admin') {
        if (isset($_POST['adicionar_item'])) {
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            if (adicionarItem($nome, $descricao)) {
                $mensagemSucesso = "Item adicionado com sucesso!";
            } else {
                $mensagemErro = "Erro ao adicionar item!";
            }
        } elseif (isset($_POST['remover_item'])) {
            $item_id = $_POST['item_id'];
            if (removerItem($item_id)) {
                $mensagemSucesso = "Item removido com sucesso!";
            } else {
                $mensagemErro = "Erro ao remover item!";
            }
        } elseif (isset($_POST['editar_item'])) {
            $item_id = $_POST['item_id'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            if (editarItem($item_id, $nome, $descricao)) {
                $mensagemSucesso = "Item editado com sucesso!";
            } else {
                $mensagemErro = "Erro ao editar item!";
            }
        }
    } else {
        if (isset($_POST['adicionar'])) {
            $item_id = $_POST['item_id'];
            $quantidade = $_POST['quantidade'];
            if (adicionarItemLista($usuario_id, $item_id, $quantidade)) {
                $mensagemSucesso = "Item adicionado à lista!";
            } else {
                $mensagemErro = "Erro ao adicionar item à lista!";
            }
        } elseif (isset($_POST['remover'])) {
            $lista_id = $_POST['lista_id'];
            if (removerItemLista($lista_id)) {
                $mensagemSucesso = "Item removido da lista!";
            } else {
                $mensagemErro = "Erro ao remover item da lista!";
            }
        } elseif (isset($_POST['editar'])) {
            $lista_id = $_POST['lista_id'];
            $quantidade = $_POST['quantidade'];
            if (editarItemLista($lista_id, $quantidade)) {
                $mensagemSucesso = "Item atualizado com sucesso!";
            } else {
                $mensagemErro = "Erro ao atualizar item!";
            }
        } elseif (isset($_POST['favoritar'])) {
            $lista_id = $_POST['lista_id'];
            if (favoritarItemLista($lista_id)) {
                $mensagemSucesso = "Item favoritado/desfavoritado!";
            } else {
                $mensagemErro = "Erro ao favoritar/desfavoritar item!";
            }
        } elseif (isset($_POST['remover_lista'])) {
            $confirmacao = $_POST['confirmacao'];
            $lista_id = $_POST['lista_id'];
            if ($confirmacao == 'sim') {
                if (removerLista($usuario_id)) {
                    $mensagemSucesso = "Lista removida com sucesso!";
                } else {
                    $mensagemErro = "Erro ao remover lista!";
                }
            }
        }
    }
}

$itens = obterItens();
$lista_itens = obterListaItens($usuario_id);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Compras</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="list-header">
        <h1>Lista de Compras </h1>
        <form method="POST" class="logout-form">
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>
        <p>Seja bem-vindo, <?php echo $_SESSION['usuario_nome']; ?></p>
    </header>
    <div class="container">
        <?php if ($usuario_tipo == 'admin'): ?>
            <div class="form-container">
                <h2>Gerenciar Itens</h2>
                <?php if (!empty($mensagemSucesso)): ?>
                    <p class="message success-message"><?= $mensagemSucesso ?></p>
                <?php endif; ?>
                <?php if (!empty($mensagemErro)): ?>
                    <p class="message error-message"><?= $mensagemErro ?></p>
                <?php endif; ?>
                <div class="admin-forms">
                    <div class="form-section">
                        <br>
                        <h3>Adicionar Item</h3>
                        <form method="POST">
                            <input type="text" name="nome" placeholder="Nome do Item" required><br>
                            <textarea name="descricao" placeholder="Descrição"></textarea><br>
                            <button type="submit" name="adicionar_item">Adicionar</button>
                        </form>
                    </div>
                    <div class="form-section">
                        <br>
                        <h3>Editar Item</h3>
                        <form method="POST">
                            <select name="item_id">
                                <?php foreach ($itens as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= $item['nome'] ?></option>
                                <?php endforeach; ?>
                            </select><br>
                            <input type="text" name="nome" placeholder="Novo Nome do Item" required><br>
                            <textarea name="descricao" placeholder="Nova Descrição"></textarea><br>
                            <button type="submit" name="editar_item">Editar</button>
                        </form>
                    </div>
                    <div class="form-section">
                        <br>
                        <h3>Remover Item</h3>
                        <form method="POST">
                            <select name="item_id">
                                <?php foreach ($itens as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= $item['nome'] ?></option>
                                <?php endforeach; ?>
                            </select><br>
                            <button type="submit" name="remover_item">Remover</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="form-container">
                <h2>Adicionar Item à Lista</h2>
                <?php if (!empty($mensagemSucesso)): ?>
                    <p class="message success-message"><?= $mensagemSucesso ?></p>
                <?php endif; ?>
                <?php if (!empty($mensagemErro)): ?>
                    <p class="message error-message"><?= $mensagemErro ?></p>
                <?php endif; ?>
                <form method="POST">
                    <select name="item_id" required>
                        <option value="">Selecione um item</option>
                        <?php foreach ($itens as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= $item['nome'] ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <input type="number" name="quantidade" placeholder="Quantidade" min="0" step="1" required><br>
                    <button type="submit" name="adicionar">Adicionar</button>
                    <br>
                </form>
                <h2>Minha Lista de Compras</h2>
                <?php if (count($lista_itens) > 0): ?>
                    <ul>
                        <?php foreach ($lista_itens as $item): ?>
                             <li>
                                <?= $item['nome'] ?> - <?= $item['quantidade'] ?>
                                <div class="item-actions" style="position: relative;">
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="lista_id" value="<?= $item['id'] ?>">
                                        <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" style="width: 55px;" min="0" step="1">
                                        <button type="submit" name="editar" class="edit-button" style="width: 30px; ">✏️</button>
                                    </form>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="lista_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="remover" class="remove-button" style="width: 30px;" >🗑️</button>
                                    </form>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="lista_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="favoritar" class="favorite-button" style="width: 30px;" ><?= $item['favorito'] ? '★' : '☆' ?></button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($usuario_tipo != 'admin'): ?>
                        <button onclick="confirmarRemocaoLista(<?= $usuario_id ?>)">Remover todos os itens</button>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="message error-message">Não há itens na sua lista.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($usuario_tipo != 'admin'): ?>
     <div id="confirmacao-popup" class="confirmacao-popup" style="display: none;">
        <div class="confirmacao-popup-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirmação de Remoção<br>______________________</h3>
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="lista_id" id="confirmacao-lista-id">
                    <input type="hidden" name="confirmacao" value="sim">
                    <button type="button" class="btn btn-secondary" onclick="fecharPopup()">Não</button>
                    <button type="submit" name="remover_lista" class="btn btn-danger">Sim</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script>
        function confirmarRemocaoLista(listaId) {
            document.getElementById('confirmacao-lista-id').value = listaId;
            document.getElementById('confirmacao-popup').style.display = 'block';
        }

        function fecharPopup() {
            document.getElementById('confirmacao-popup').style.display = 'none';
        }
    </script>
</body>
</html>
