<?php
require '../bd/conexao.php';
$conexao = conexao::getInstance();

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID inválido']);
    exit;
}

$sql = "SELECT sol_status FROM solicitacoes WHERE sol_codigo = :id LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Solicitação não encontrada']);
    exit;
}

$status = $solicitacao['sol_status'];

if ($status === 'pendente') {
    echo json_encode(['status' => 'pendente']);
} else {
    $mensagem = $status === 'aceita' ? 'Sua solicitação foi aceita.' : 'Sua solicitação foi recusada.';
    echo json_encode(['status' => $status, 'mensagem' => $mensagem]);
}
