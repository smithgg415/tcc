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
$modelo = isset($_POST['modelo']) ? $_POST['modelo'] : null;
$placa = isset($_POST['placa']) ? $_POST['placa'] : null;
$ano = isset($_POST['ano']) ? $_POST['ano'] : null;
$cor = isset($_POST['cor']) ? $_POST['cor'] : null;
$fun_codigo = isset($_POST['fun_codigo']) ? $_POST['fun_codigo'] : null;

if($acao == 'adicionar') {
    $sql = "INSERT INTO motocicletas (mot_modelo, mot_placa, mot_ano, mot_cor, fun_codigo) VALUES (:modelo, :placa, :ano, :cor, :fun_codigo)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':modelo', $modelo);
    $stmt->bindParam(':placa', $placa);
    $stmt->bindParam(':ano', $ano);
    $stmt->bindParam(':cor', $cor);
    $stmt->bindParam(':fun_codigo', $fun_codigo);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Motocicleta adicionada com sucesso!';
        header('Location: ../admin/motocicletas/motocicletas.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao inserir motocicleta!</div>";
        header('Location: ../admin/motocicletas/adicionar_motocicleta.php');
        exit;
    }
}

if($acao == 'editar') {
    $sql = "UPDATE motocicletas SET mot_modelo = :modelo, mot_placa = :placa, mot_ano = :ano, mot_cor = :cor WHERE mot_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':modelo', $modelo);
    $stmt->bindParam(':placa', $placa);
    $stmt->bindParam(':ano', $ano);
    $stmt->bindParam(':cor', $cor);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Motocicleta editada com sucesso!';
        header('Location: ../admin/motocicletas/motocicletas.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao editar motocicleta!</div>";
        header('Location: ../admin/motocicletas/editar_motocicleta.php?id='.$id);
        exit;
    }
} elseif ($acao == 'excluir') {
    $sql = "DELETE FROM motocicletas WHERE mot_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Motocicleta excluída com sucesso!';
        header('Location: ../admin/motocicletas/motocicletas.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao excluir motocicleta!</div>";
        header('Location: ../admin/motocicletas/motocicletas.php');
        exit;
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>Ação inválida!</div>";
}