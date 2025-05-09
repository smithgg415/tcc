<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if (!$id) {
    echo "ID do funcionário não fornecido.";
    exit;
}

// Buscar informações do funcionário
$sqlFuncionario = "SELECT fun_nome, fun_cargo FROM funcionarios WHERE fun_codigo = :id";
$stmt = $conexao->prepare($sqlFuncionario);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$funcionario) {
    echo "Funcionário não encontrado.";
    exit;
}

if ($funcionario['fun_cargo'] == 'Atendente') {
    $sqlQuantidade = "SELECT COUNT(*) as quantidade FROM viagens WHERE ate_codigo = :id AND via_status = 'finalizada'";
    $sqlInformacoes = "
        SELECT v.*, a.ava_nota, a.ava_comentario
        FROM viagens v
        LEFT JOIN avaliacoes a ON v.via_codigo = a.via_codigo
        WHERE v.ate_codigo = :id AND v.via_status = 'finalizada'
    ";
    $sqlMedia = "SELECT AVG(ava_nota) as media FROM avaliacoes a 
                 JOIN viagens v ON a.via_codigo = v.via_codigo 
                 WHERE v.ate_codigo = :id";
    $tipoRelatorio = "Viagens Gerenciadas";
} else {
    $sqlQuantidade = "SELECT COUNT(*) as quantidade FROM viagens WHERE fun_codigo = :id AND via_status = 'finalizada'";
    $sqlInformacoes = "
        SELECT v.*, a.ava_nota, a.ava_comentario
        FROM viagens v
        LEFT JOIN avaliacoes a ON v.via_codigo = a.via_codigo
        WHERE v.fun_codigo = :id AND v.via_status = 'finalizada'
    ";
    $sqlMedia = "SELECT AVG(ava_nota) as media FROM avaliacoes a 
                 JOIN viagens v ON a.via_codigo = v.via_codigo 
                 WHERE v.fun_codigo = :id";
    $tipoRelatorio = "Viagens Realizadas";
}

// Executar consultas
$stmt = $conexao->prepare($sqlQuantidade);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conexao->prepare($sqlInformacoes);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$viagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conexao->prepare($sqlMedia);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$media = $stmt->fetch(PDO::FETCH_ASSOC);

$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$porPagina = 5;
$offset = ($pagina - 1) * $porPagina;

// Modificar a query para incluir LIMIT e OFFSET
$sqlInformacoes .= " LIMIT :limit OFFSET :offset";

// Contar total de viagens para paginação
$sqlTotal = ($funcionario['fun_cargo'] == 'Atendente')
    ? "SELECT COUNT(*) as total FROM viagens WHERE ate_codigo = :id AND via_status = 'finalizada'"
    : "SELECT COUNT(*) as total FROM viagens WHERE fun_codigo = :id AND via_status = 'finalizada'";

$stmt = $conexao->prepare($sqlTotal);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$totalViagens = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalViagens / $porPagina);

if ($pagina < 1) $pagina = 1;
if ($pagina > $totalPaginas) $pagina = $totalPaginas;

$stmt = $conexao->prepare($sqlInformacoes);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$viagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Viagens</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --header-bg: #333;
            --header-text: #fff;
            --table-header: #444;
            --table-row-even: #fff;
            --table-row-odd: #f9f9f9;
            --table-border: #ddd;
            --star-color: #FFD700;
            --rating-bg: #f8f8f8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .report-header {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .report-summary {
            display: flex;
            justify-content: space-between;
            background-color: var(--table-row-even);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
        }

        .summary-item {
            text-align: center;
            flex: 1;
            min-width: 150px;
            margin: 5px;
        }

        .summary-item h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .summary-item p {
            font-size: 24px;
            font-weight: bold;
        }

        .stars {
            color: var(--star-color);
            font-size: 24px;
            letter-spacing: 2px;
        }

        .table-container {
            overflow-x: auto;
            background-color: var(--table-row-even);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--table-border);
        }

        th {
            background-color: var(--table-header);
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: var(--table-row-even);
        }

        tr:nth-child(odd) {
            background-color: var(--table-row-odd);
        }

        tr:hover {
            background-color: #e9e9e9;
        }

        .rating {
            display: flex;
            align-items: center;
        }

        .rating-value {
            margin-left: 8px;
            font-weight: bold;
        }

        .comment {
            background-color: var(--rating-bg);
            padding: 8px;
            border-radius: 4px;
            margin-top: 5px;
            font-style: italic;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        .print-btn {
            display: inline-block;
            background-color: var(--table-header);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            font-family: 'Righteous', sans-serif;
            transition: background-color 0.3s;
        }

        .print-btn:hover {
            background-color: #333;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .container,
            .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .print-btn {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .report-summary {
                flex-direction: column;
            }

            .summary-item {
                margin-bottom: 15px;
            }
        }

        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }

        .atendente-badge {
            background-color: #4a6baf;
            color: white;
        }

        .mototaxista-badge {
            background-color: #e67e22;
            color: white;
        }

        .report-subtitle {
            font-size: 18px;
            margin-bottom: 10px;
            color: #ddd;
        }

        .pagination {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--table-border);
            border-radius: 4px;
            text-decoration: none;
            color: var(--text-color);
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: var(--table-header);
            color: white;
            border-color: var(--table-header);
        }

        .pagination .current {
            background-color: var(--table-header);
            color: white;
            border-color: var(--table-header);
        }

        .pagination .disabled {
            color: #999;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <div class="container">
        <div class="report-header">
            <h1 class="report-title">Relatório de Viagens
                <span class="role-badge <?= ($funcionario['fun_cargo'] == 'Atendente') ? 'atendente-badge' : 'mototaxista-badge' ?>">
                    <?= ucfirst($funcionario['fun_cargo']) ?>
                </span>
            </h1>
            <p class="report-subtitle"><?= $tipoRelatorio ?></p>
            <p>Funcionário: <?php echo htmlspecialchars($funcionario['fun_nome']); ?></p>
            <p>Contratado em:
                <?php
                $sqlContratado = "SELECT fun_data_contratacao FROM funcionarios WHERE fun_codigo = :id";
                $stmt = $conexao->prepare($sqlContratado);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $contratado = $stmt->fetch(PDO::FETCH_ASSOC);
                echo date('d/m/Y', strtotime($contratado['fun_data_contratacao']));
                ?>
            </p>
            <p>Data de Geração: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>


        <div class="report-summary">
            <div class="summary-item">
                <h3><?= ($funcionario['fun_cargo'] == 'Atendente') ? 'Viagens Gerenciadas' : 'Viagens Realizadas' ?></h3>
                <p><?php echo $resultado['quantidade']; ?></p>
            </div>
            <div class="summary-item">
                <h3>Faturamento Total</h3>
                <p>
                    <?php
                    $sqlFaturamento = ($funcionario['fun_cargo'] == 'Atendente')
                        ? "SELECT SUM(via_valor) as faturamento FROM viagens WHERE ate_codigo = :id AND via_status = 'finalizada'"
                        : "SELECT SUM(via_valor) as faturamento FROM viagens WHERE fun_codigo = :id AND via_status = 'finalizada'";
                    $stmt = $conexao->prepare($sqlFaturamento);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $faturamento = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "R$ " . number_format($faturamento['faturamento'], 2, ',', '.');
                    ?>
                </p>
            </div>
            <div class="summary-item">
                <h3>Avaliação Média</h3>
                <div class="stars">
                    <?php
                    if ($media['media']) {
                        $rounded = round($media['media'] * 2) / 2;
                        $fullStars = floor($rounded);
                        $halfStar = ($rounded - $fullStars) > 0;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                        echo str_repeat('★', $fullStars);
                        echo $halfStar ? '½' : '';
                        echo str_repeat('☆', $emptyStars);

                        echo '<span class="rating-value">(' . number_format($media['media'], 1) . ')</span>';
                    } else {
                        echo '☆☆☆☆☆ <span class="rating-value">(N/A)</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="summary-item">
                <button class="print-btn" onclick="window.print()">Imprimir Relatório</button>
            </div>
        </div>

        <div class="table-container">
            <?php if (count($viagens) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Forma de Pagamento</th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Avaliação</th>
                            <th>Status</th>
                            <?php if ($funcionario['fun_cargo'] == 'Atendente'): ?>
                                <th>Mototaxista</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($viagens as $viagem): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($viagem['via_data']); ?></td>
                                <td>R$ <?php echo number_format($viagem['via_valor'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($viagem['via_formapagamento']); ?></td>
                                <td><?php echo htmlspecialchars($viagem['via_origem']); ?></td>
                                <td><?php echo htmlspecialchars($viagem['via_destino']); ?></td>
                                <td>
                                    <?php if ($viagem['ava_nota']): ?>
                                        <div class="rating">
                                            <?php
                                            $nota = $viagem['ava_nota'];
                                            echo str_repeat('★', floor($nota));
                                            echo ($nota - floor($nota) >= 0.5) ? '½' : '';
                                            echo str_repeat('☆', 5 - ceil($nota));
                                            ?>
                                            <span class="rating-value"><?php echo number_format($nota, 1); ?></span>
                                        </div>
                                        <?php if ($viagem['ava_comentario']): ?>
                                            <div class="comment"><?php echo htmlspecialchars($viagem['ava_comentario']); ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        Sem avaliação
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($viagem['via_status']); ?></td>
                                <?php if ($funcionario['fun_cargo'] == 'Atendente'): ?>
                                    <td>
                                        <?php
                                        if ($viagem['fun_codigo']) {
                                            $sqlMototaxista = "SELECT fun_nome FROM funcionarios WHERE fun_codigo = :id";
                                            $stmt = $conexao->prepare($sqlMototaxista);
                                            $stmt->bindParam(':id', $viagem['fun_codigo'], PDO::PARAM_INT);
                                            $stmt->execute();
                                            $mototaxista = $stmt->fetch(PDO::FETCH_ASSOC);
                                            echo htmlspecialchars($mototaxista['fun_nome']);
                                        } else {
                                            echo "Não atribuído";
                                        }
                                        ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($totalPaginas > 1): ?>
                    <div class="pagination">
                        <?php if ($pagina > 1): ?>
                            <a href="?id=<?= $id ?>&page=1">«</a>
                            <a href="?id=<?= $id ?>&page=<?= $pagina - 1 ?>">‹</a>
                        <?php else: ?>
                            <span class="disabled">«</span>
                            <span class="disabled">‹</span>
                        <?php endif; ?>

                        <?php
                        $inicio = max(1, $pagina - 2);
                        $fim = min($totalPaginas, $pagina + 2);

                        for ($i = $inicio; $i <= $fim; $i++): ?>
                            <a href="?id=<?= $id ?>&page=<?= $i ?>" <?= ($i == $pagina) ? 'class="current"' : '' ?>>
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="?id=<?= $id ?>&page=<?= $pagina + 1 ?>">›</a>
                            <a href="?id=<?= $id ?>&page=<?= $totalPaginas ?>">»</a>
                        <?php else: ?>
                            <span class="disabled">›</span>
                            <span class="disabled">»</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>
                        Nenhuma viagem encontrada para este
                        <?php
                        if ($funcionario['fun_cargo'] == 'Atendente') {
                            echo "atendente.";
                        } else {
                            echo "mototaxista.";
                        }
                        ?>
                    </p>
                </div>

            <?php endif; ?>
        </div>
    </div>
</body>

</html>