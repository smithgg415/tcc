<?php
require '../bd/conexao.php';
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'atendente') {
    header('Location: login.php');
    exit;
}

$conexao = conexao::getInstance();

// Consulta para obter todas as viagens
$sql = 'SELECT v.via_codigo, v.via_data, v.via_valor, v.via_formapagamento, 
               v.via_origem, v.via_destino, v.via_status, v.via_observacoes,
               u.usu_nome AS cliente, f.fun_nome AS mototaxista
        FROM viagens v
        INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
        INNER JOIN funcionarios f ON v.fun_codigo = f.fun_codigo
        ORDER BY v.via_data DESC';
$stm = $conexao->prepare($sql);
$stm->execute();
$viagens = $stm->fetchAll(PDO::FETCH_ASSOC);

// Define os headers para download do arquivo Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="viagens_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Função para formatar o status
function formatStatus($status) {
    switch(strtolower($status)) {
        case 'concluido': return 'Concluído';
        case 'cancelado': return 'Cancelado';
        case 'andamento': return 'Em andamento';
        default: return ucfirst($status);
    }
}

// Função para formatar a forma de pagamento
function formatPagamento($pagamento) {
    switch(strtolower($pagamento)) {
        case 'cartao': return 'Cartão';
        case 'dinheiro': return 'Dinheiro';
        case 'pix': return 'Pix';
        default: return ucfirst($pagamento);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #000;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data/Hora</th>
                <th>Cliente</th>
                <th>Mototaxista</th>
                <th>Origem</th>
                <th>Destino</th>
                <th>Valor (R$)</th>
                <th>Pagamento</th>
                <th>Status</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($viagens as $viagem): ?>
                <tr>
                    <td><?= $viagem['via_codigo'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($viagem['via_data'])) ?></td>
                    <td><?= htmlspecialchars($viagem['cliente']) ?></td>
                    <td><?= htmlspecialchars($viagem['mototaxista']) ?></td>
                    <td><?= htmlspecialchars($viagem['via_origem']) ?></td>
                    <td><?= htmlspecialchars($viagem['via_destino']) ?></td>
                    <td><?= number_format($viagem['via_valor'], 2, ',', '.') ?></td>
                    <td><?= formatPagamento($viagem['via_formapagamento']) ?></td>
                    <td><?= formatStatus($viagem['via_status']) ?></td>
                    <td><?= htmlspecialchars($viagem['via_observacoes']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>