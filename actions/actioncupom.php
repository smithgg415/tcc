<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = $_POST['acao'] ?? null;
$codigo = $_POST['codigo'] ?? null;
$descricao = $_POST['descricao'] ?? null;
$tipo_desconto = $_POST['tipo_desconto'] ?? null;
$valor_desconto = $_POST['valor_desconto'] ?? 0;
$valor_minimo = $_POST['valor_minimo'] ?? 0;
$quantidade_uso = $_POST['quantidade_uso'] ?? 1;
$validade_inicio = $_POST['validade_inicio'] ?? null;
$validade_fim = $_POST['validade_fim'] ?? null;
$ativo = $_POST['ativo'] ?? 1;
$cup_updated_at = date('Y-m-d H:i:s');
$cup_created_at = date('Y-m-d H:i:s');

if ($acao === 'adicionar') {
    $sql = "INSERT INTO cupons (
        cup_codigo, cup_descricao, cup_tipo_desconto, cup_valor_desconto,
        cup_valor_minimo, cup_quantidade_uso, cup_validade_inicio, cup_validade_fim,
        cup_ativo, cup_criado_em
    ) VALUES (
        :cup_codigo, :cup_descricao, :cup_tipo_desconto, :cup_valor_desconto,
        :cup_valor_minimo, :cup_quantidade_uso, :cup_validade_inicio, :cup_validade_fim,
        :cup_ativo, :cup_created_at
    )";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':tipo_desconto', $tipo_desconto);
    $stmt->bindParam(':valor_desconto', $valor_desconto);
    $stmt->bindParam(':valor_minimo', $valor_minimo);
    $stmt->bindParam(':quantidade_uso', $quantidade_uso);
    $stmt->bindParam(':validade_inicio', $validade_inicio);
    $stmt->bindParam(':validade_fim', $validade_fim);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':created_at', $created_at);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Cupom adicionado com sucesso!';
        header('Location: ../admin/cupons/cupons.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao adicionar cupom!';
        header('Location: ../admin/cupons/novo_cupom.php');
        exit;
    }
}

// EDITAR CUPOM
if ($acao === 'editar') {
    $sql = "UPDATE cupons SET 
        cup_descricao = :cup_descricao,
        cup_tipo_desconto = :cup_tipo_desconto,
        cup_valor_desconto = :cup_valor_desconto,
        cup_valor_minimo = :cup_valor_minimo,
        cup_quantidade_uso = :cup_quantidade_uso,
        cup_validade_inicio = :cup_validade_inicio,
        cup_validade_fim = :cup_validade_fim,
        cup_ativo = :cup_ativo
        WHERE cup_codigo = :cup_codigo";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':tipo_desconto', $tipo_desconto);
    $stmt->bindParam(':valor_desconto', $valor_desconto);
    $stmt->bindParam(':valor_minimo', $valor_minimo);
    $stmt->bindParam(':quantidade_uso', $quantidade_uso);
    $stmt->bindParam(':validade_inicio', $validade_inicio);
    $stmt->bindParam(':validade_fim', $validade_fim);
    $stmt->bindParam(':ativo', $ativo);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Cupom atualizado com sucesso!';
        header('Location: ../admin/cupons/cupons.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao atualizar cupom!';
        header("Location: ../admin/cupons/editar_cupom.php?cup_codigo=$codigo");
        exit;
    }
}

if ($acao === 'excluir') {
    $sql = "DELETE FROM cupons WHERE cup_codigo = :cup_codigo";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':cup_codigo', $cup_codigo);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Cupom excluído com sucesso!';
        header('Location: ../admin/cupons/cupons.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao excluir cupom!';
        header('Location: ../admin/cupons/cupons.php');
        exit;
    }
}

if ($acao === 'ativar') {
    $sql = "UPDATE cupons SET cup_ativo = 1 WHERE cup_codigo = :cup_codigo";
} elseif ($acao === 'desativar') {
    $sql = "UPDATE cupons SET cup_ativo = 0 WHERE cup_codigo = :cup_codigo";
}

if ($acao === 'ativar' || $acao === 'desativar') {
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':cup_codigo', $cup_codigo);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Cupom atualizado com sucesso!';
        header('Location: ../admin/cupons/cupons.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao atualizar status do cupom!';
        header('Location: ../admin/cupons/cupons.php');
        exit;
    }
}
