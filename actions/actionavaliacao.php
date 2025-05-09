<?php
session_start();
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = $_POST['acao'] ?? null;
$id = $_POST['id'] ?? 0;
$usu_codigo = $_POST['usu_codigo'] ?? null;
$via_codigo = $_POST['via_codigo'] ?? null;
$nota = $_POST['nota'] ?? null;
$comentario = $_POST['comentario'] ?? null;
$data_avaliacao = date('Y-m-d H:i:s');

if ($acao === 'adicionar') {
    $sql = "INSERT INTO avaliacoes (usu_codigo, via_codigo, ava_nota, ava_comentario, ava_data_avaliacao) 
            VALUES (:usu_codigo, :via_codigo, :nota, :comentario, :data_avaliacao)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':usu_codigo', $usu_codigo);
    $stmt->bindParam(':via_codigo', $via_codigo);
    $stmt->bindParam(':nota', $nota);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':data_avaliacao', $data_avaliacao);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Avaliação criada com sucesso!';
        header('Location: ../user/index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao avaliar!';
        header('Location: ../user/avaliar_viagem.php');
        exit;
    }

} elseif ($acao === 'editar') {
    $sql = "UPDATE avaliacoes 
            SET ava_nota = :nota, ava_comentario = :comentario 
            WHERE ava_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nota', $nota);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Avaliação editada com sucesso!';
        header('Location: ../user/conta.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao editar avaliação!';
        header("Location: ../user/editar_avaliacao.php?id=$id");
        exit;
    }

} elseif ($acao === 'excluir') {
    $sql = "DELETE FROM avaliacoes WHERE ava_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Avaliação excluída com sucesso!';
        header('Location: ../user/detalhes_viagem.php?id=' . $via_codigo);
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao excluir avaliação!';
        header('Location: ../user/detalhes_viagem.php?id=' . $via_codigo);
        exit;
    }
}
