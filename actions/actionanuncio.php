<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = isset($_POST['acao']) ? $_POST['acao'] : null;
$id = isset($_POST['id']) ? $_POST['id'] : 0;
$titulo = isset($_POST['titulo']) ? $_POST['titulo'] : null;
$foto = isset($_POST['foto']) ? $_POST['foto'] : null;
$descricao = isset($_POST['descricao']) ? $_POST['descricao'] : null;

if ($acao == 'adicionar') {
    $sql = "INSERT INTO anuncios (anu_titulo, anu_foto, anu_descricao) VALUES (:titulo, :foto, :descricao)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':descricao', $descricao);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Anúncio adicionado com sucesso!';
        header('Location: ../admin/anuncios/anuncios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao inserir anúncio!</div>";
        header('Location: ../admin/anuncios/adicionar_anuncio.php');
        exit;
    }
}

if ($acao == 'editar') {
    $sql = "UPDATE anuncios SET anu_titulo = :titulo, anu_foto = :foto, anu_descricao = :descricao WHERE anu_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Anúncio editado com sucesso!';
        header('Location: ../admin/anuncios/anuncios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao editar anúncio!</div>";
        header('Location: ../admin/anuncios/editar_anuncio.php?id=' . $id);
        exit;
    }
}

if ($acao == 'excluir') {
    $sql = "DELETE FROM anuncios WHERE anu_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Anúncio excluído com sucesso!';
        header('Location: ../admin/anuncios/anuncios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao excluir anúncio!</div>";
        header('Location: ../admin/anuncios/anuncios.php');
        exit;
    }
}

?>
