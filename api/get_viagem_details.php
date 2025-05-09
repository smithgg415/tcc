<?php
session_start();
require '../bd/conexao.php';

if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Usuário não autorizado.'
    ]);
    exit;
}

if (isset($_GET['id_viagem']) && !empty($_GET['id_viagem'])) {
    $id_viagem = (int)$_GET['id_viagem'];

    $conexao = conexao::getInstance();
    $sql = 'SELECT v.via_codigo, v.via_data, v.via_valor, v.via_formapagamento, 
                   v.via_origem, v.via_destino, v.via_status, v.via_observacoes, v.ate_codigo,
                   u.usu_nome AS cliente, 
                   f1.fun_nome AS mototaxista,
                   f2.fun_nome AS atendente
            FROM viagens v
            INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
            INNER JOIN funcionarios f1 ON v.fun_codigo = f1.fun_codigo
            INNER JOIN funcionarios f2 ON v.ate_codigo = f2.fun_codigo
            WHERE v.via_codigo = :id_viagem
            LIMIT 1';

    $stm = $conexao->prepare($sql);
    $stm->bindValue(':id_viagem', $id_viagem, PDO::PARAM_INT);
    $stm->execute();

    if ($stm->rowCount() > 0) {
        $viagem = $stm->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'status' => 'success',
            'data' => $viagem
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Viagem não encontrada.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID da viagem não informado.'
    ]);
}
