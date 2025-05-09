<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = $_POST['acao'] ?? null;
$via_codigo = isset($_POST['via_codigo']) ? (int) $_POST['via_codigo'] : 0;
$id_solicitacao = isset($_POST['id_solicitacao']) ? (int) $_POST['id_solicitacao'] : 0;
$status = $_POST['status'] ?? ($acao === 'aceitar' ? 'aceita' : 'recusada');
$funcionario_codigo = $_POST['funcionario_codigo'] ?? null;
$ate_codigo = $_SESSION['fun_codigo'] ?? null;  

if ($acao === 'finalizar' && $via_codigo > 0) {
    try {
        $sqlBuscarFuncionario = "SELECT fun_codigo FROM viagens WHERE via_codigo = :via_codigo LIMIT 1";
        $stmt = $conexao->prepare($sqlBuscarFuncionario);
        $stmt->bindParam(':via_codigo', $via_codigo, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && isset($resultado['fun_codigo'])) {
            $funcionario_codigo = $resultado['fun_codigo'];

            $sqlAtivar = 'UPDATE funcionarios SET fun_ativo = 1 WHERE fun_codigo = :funcionario_codigo';
            $stmt = $conexao->prepare($sqlAtivar);
            $stmt->bindParam(':funcionario_codigo', $funcionario_codigo, PDO::PARAM_INT);
            $stmt->execute();

            $sql = "UPDATE viagens SET via_status = 'finalizada' WHERE via_codigo = :via_codigo";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':via_codigo', $via_codigo, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['mensagem'] = 'Viagem finalizada com sucesso!';
        } else {
            $_SESSION['mensagem'] = 'Funcionário não encontrado para esta viagem.';
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro ao finalizar viagem: ' . $e->getMessage();
    }

    header('Location: ../admin/index.php');
    exit;
}


if (!in_array($acao, ['aceitar', 'recusar']) || $id_solicitacao <= 0) {
    $_SESSION['mensagem'] = 'Ação inválida ou ID da solicitação ausente.';
    header('Location: ../admin/solicitacoes.php');
    exit;
}

try {
    $sql = "UPDATE solicitacoes SET sol_status = :status WHERE sol_codigo = :id_solicitacao";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id_solicitacao', $id_solicitacao);
    $stmt->execute();
} catch (PDOException $e) {
    $_SESSION['mensagem'] = 'Erro ao atualizar status da solicitação: ' . $e->getMessage();
    header('Location: ../admin/solicitacoes.php');
    exit;
}
if ($acao === 'aceitar') {
    if (!$funcionario_codigo) {
        $_SESSION['mensagem'] = 'Você deve selecionar um mototaxista para aceitar a solicitação.';
        header('Location: ../admin/solicitacoes.php');
        exit;
    }

    try {
        $sql_solicitacao = "SELECT * FROM solicitacoes WHERE sol_codigo = :id_solicitacao LIMIT 1";
        $stmt_solicitacao = $conexao->prepare($sql_solicitacao);
        $stmt_solicitacao->bindParam(':id_solicitacao', $id_solicitacao);
        $stmt_solicitacao->execute();
        $solicitacao = $stmt_solicitacao->fetch(PDO::FETCH_ASSOC);

        if ($solicitacao) {
            $sql = 'UPDATE funcionarios set fun_ativo = 0 WHERE fun_codigo = :funcionario_codigo';
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':funcionario_codigo', $funcionario_codigo);
            $stmt->execute();
            $sql_viagem = "INSERT INTO viagens (
                fun_codigo, sol_codigo, usu_codigo, ate_codigo, via_origem, via_destino, 
                via_valor, via_formapagamento, via_data, via_servico, via_status, via_observacoes
            ) VALUES (
                :fun_codigo, :sol_codigo, :usu_codigo, :ate_codigo, :via_origem, :via_destino, 
                :via_valor, :via_formapagamento, NOW(), :via_servico, 'em andamento', :via_observacoes
            )";

            $stmt_viagem = $conexao->prepare($sql_viagem);
            $stmt_viagem->bindParam(':fun_codigo', $funcionario_codigo);
            $stmt_viagem->bindParam(':sol_codigo', $id_solicitacao);
            $stmt_viagem->bindParam(':usu_codigo', $solicitacao['usu_codigo']);
            $stmt_viagem->bindParam(':ate_codigo', $ate_codigo);
            $stmt_viagem->bindParam(':via_origem', $solicitacao['sol_origem']);
            $stmt_viagem->bindParam(':via_destino', $solicitacao['sol_destino']);
            $stmt_viagem->bindParam(':via_valor', $solicitacao['sol_valor']);
            $stmt_viagem->bindParam(':via_formapagamento', $solicitacao['sol_formapagamento']);
            $stmt_viagem->bindParam(':via_servico', $solicitacao['sol_servico']);
            $stmt_viagem->bindParam(':via_observacoes', $solicitacao['sol_observacao']);
            $stmt_viagem->execute();
        }

        $_SESSION['mensagem'] = 'Solicitação aceita com sucesso!';
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro ao aceitar solicitação: ' . $e->getMessage();
    }

    header('Location: ../admin/solicitacoes.php');
    exit;
}

if ($acao === 'recusar') {
    $_SESSION['mensagem'] = 'Solicitação recusada com sucesso!';
    header('Location: ../admin/solicitacoes.php');
    exit;
}
