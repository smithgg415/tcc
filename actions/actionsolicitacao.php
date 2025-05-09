<?php
session_start();
if (!isset($_SESSION['logado099'])  && $_SESSION['ativo'] != 1 && $_SESSION['tipo'] !== 'usuario') {
    header('Location: ../user/login.php');
    exit;
}
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = isset($_POST['acao']) ? $_POST['acao'] : null;
$usuario_id = isset($_POST['usu_codigo']) ? $_POST['usu_codigo'] : 0;
$origem = isset($_POST['origem']) ? $_POST['origem'] : null;
$destino = isset($_POST['destino']) ? $_POST['destino'] : null;
$valor = isset($_POST['valor']) ? $_POST['valor'] : 0;
$formapagamento = isset($_POST['forma_pagamento']) ? $_POST['forma_pagamento'] : null;
$distancia = isset($_POST['distancia']) ? $_POST['distancia'] : 0;
$largura = isset($_POST['largura']) ? $_POST['largura'] : null;
$comprimento = isset($_POST['comprimento']) ? $_POST['comprimento'] : null;
$peso = isset($_POST['peso']) ? $_POST['peso'] : null;
$servico = isset($_POST['servico']) ? $_POST['servico'] : null;
$observacao = isset($_POST['observacao']) ? $_POST['observacao'] : null;

if ($acao == 'adicionar') {
    $sql = "INSERT INTO solicitacoes (sol_origem, sol_destino, sol_valor, sol_formapagamento, sol_distancia, sol_data, usu_codigo, sol_largura, sol_comprimento, sol_peso, sol_status, sol_servico, sol_observacoes) 
            VALUES (:origem, :destino, :valor, :formapagamento, :distancia, NOW(), :usuario_id, :largura, :comprimento, :peso, 'pendente', :servico, :observacao)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':origem', $origem);
    $stmt->bindParam(':destino', $destino);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':formapagamento', $formapagamento);
    $stmt->bindParam(':distancia', $distancia);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':largura', $largura);
    $stmt->bindParam(':comprimento', $comprimento);
    $stmt->bindParam(':peso', $peso);
    $stmt->bindParam(':servico', $servico);
    $stmt->bindParam(':observacao', $observacao);

    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Solicitação de corrida realizada com sucesso!';
        header('Location: ../user/solicitacao_pendente.php?id=' . $conexao->lastInsertId());
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao realizar solicitação!</div>";
        if ($servico == 'entrega') {
            header('Location: ../user/solicitar_entrega.php');
        } else {
            header('Location: ../user/solicitar_corrida.php');
        }
        exit;
    }
}
if($acao == 'cancelar') {
    $sol_codigo = isset($_POST['sol_codigo']) ? $_POST['sol_codigo'] : 0;
    $sql = "UPDATE solicitacoes SET sol_status = 'cancelada' WHERE sol_codigo = :sol_codigo";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':sol_codigo', $sol_codigo, PDO::PARAM_INT);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Solicitação cancelada com sucesso!';
        header('Location: ../user/index.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao cancelar solicitação!</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>Ação inválida!</div>";
}
