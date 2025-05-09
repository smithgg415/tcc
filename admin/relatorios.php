<?php
session_start();
if (!isset($_SESSION["logado099"]) || $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../bd/conexao.php';

$conexao = conexao::getInstance();

// Filtros de data padrão (últimos 30 dias)
$dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d', strtotime('-30 days'));
$dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');

// Validar datas
if ($dataInicio > $dataFim) {
    $temp = $dataInicio;
    $dataInicio = $dataFim;
    $dataFim = $temp;
}

// Consulta usuários
$sqlUsuarios = "SELECT COUNT(*) as total, 
                SUM(CASE WHEN usu_ativo = 1 THEN 1 ELSE 0 END) as ativos,
                SUM(CASE WHEN usu_ativo = 0 THEN 1 ELSE 0 END) as banidos,
                SUM(CASE WHEN DATE(usu_created_at) BETWEEN :dataInicio AND :dataFim THEN 1 ELSE 0 END) as novos
                FROM usuarios";
$stmtUsuarios = $conexao->prepare($sqlUsuarios);
$stmtUsuarios->bindParam(':dataInicio', $dataInicio);
$stmtUsuarios->bindParam(':dataFim', $dataFim);
$stmtUsuarios->execute();
$dadosUsuarios = $stmtUsuarios->fetch(PDO::FETCH_ASSOC);

// Consulta corridas
$sqlCorridas = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN via_status = 'finalizada' THEN 1 ELSE 0 END) as finalizadas,
                SUM(CASE WHEN via_status = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                SUM(CASE WHEN via_status = 'em andamento' THEN 1 ELSE 0 END) as em_andamento,
                AVG(via_valor) as valor_medio,
                SUM(via_valor) as faturamento_total
                FROM viagens
                WHERE via_data BETWEEN :dataInicio AND :dataFim";
$stmtCorridas = $conexao->prepare($sqlCorridas);
$stmtCorridas->bindParam(':dataInicio', $dataInicio);
$stmtCorridas->bindParam(':dataFim', $dataFim);
$stmtCorridas->execute();
$dadosCorridas = $stmtCorridas->fetch(PDO::FETCH_ASSOC);

// Formatando valores para evitar NULL
$valorMedio = $dadosCorridas['valor_medio'] ? number_format($dadosCorridas['valor_medio'], 2, ',', '.') : '0,00';
$faturamentoTotal = $dadosCorridas['faturamento_total'] ? number_format($dadosCorridas['faturamento_total'], 2, ',', '.') : '0,00';

// Usuários mais ativos
$sqlUsuariosAtivos = "SELECT u.usu_nome, COUNT(v.via_codigo) as total_corridas, SUM(v.via_valor) as total_gasto
                     FROM usuarios u
                     LEFT JOIN viagens v ON u.usu_codigo = v.usu_codigo
                     WHERE v.via_data BETWEEN :dataInicio AND :dataFim
                     GROUP BY u.usu_codigo
                     ORDER BY total_corridas DESC
                     LIMIT 5";
$stmtUsuariosAtivos = $conexao->prepare($sqlUsuariosAtivos);
$stmtUsuariosAtivos->bindParam(':dataInicio', $dataInicio);
$stmtUsuariosAtivos->bindParam(':dataFim', $dataFim);
$stmtUsuariosAtivos->execute();
$usuariosAtivos = $stmtUsuariosAtivos->fetchAll(PDO::FETCH_ASSOC);

// Mototaxistas mais ativos
$sqlMototaxistasAtivos = "SELECT f.fun_nome, COUNT(v.via_codigo) as total_corridas, 
                         SUM(v.via_valor) as total_faturado, AVG(a.ava_nota) as media_avaliacao
                         FROM funcionarios f
                         LEFT JOIN viagens v ON f.fun_codigo = v.fun_codigo
                         LEFT JOIN avaliacoes a ON v.via_codigo = a.via_codigo
                         WHERE v.via_data BETWEEN :dataInicio AND :dataFim
                         AND f.fun_cargo = 'mototaxista'
                         GROUP BY f.fun_codigo
                         ORDER BY total_corridas DESC
                         LIMIT 5";
$stmtMototaxistasAtivos = $conexao->prepare($sqlMototaxistasAtivos);
$stmtMototaxistasAtivos->bindParam(':dataInicio', $dataInicio);
$stmtMototaxistasAtivos->bindParam(':dataFim', $dataFim);
$stmtMototaxistasAtivos->execute();
$mototaxistasAtivos = $stmtMototaxistasAtivos->fetchAll(PDO::FETCH_ASSOC);

// Dados para gráfico de receita mensal (últimos 12 meses)
$sqlReceita = "SELECT 
               DATE_FORMAT(via_data, '%Y-%m') as mes,
               SUM(via_valor) as total,
               COUNT(*) as total_corridas
               FROM viagens
               WHERE via_status = 'finalizada'
               AND via_data BETWEEN DATE_FORMAT(NOW() - INTERVAL 12 MONTH, '%Y-%m-01') AND LAST_DAY(NOW())
               GROUP BY DATE_FORMAT(via_data, '%Y-%m')
               ORDER BY mes ASC";
$stmtReceita = $conexao->prepare($sqlReceita);
$stmtReceita->execute();
$receitaMensal = $stmtReceita->fetchAll(PDO::FETCH_ASSOC);

// Dados para gráfico de status de corridas
$sqlStatusCorridas = "SELECT 
                      via_status, 
                      COUNT(*) as total
                      FROM viagens
                      WHERE via_data BETWEEN :dataInicio AND :dataFim
                      GROUP BY via_status";
$stmtStatus = $conexao->prepare($sqlStatusCorridas);
$stmtStatus->bindParam(':dataInicio', $dataInicio);
$stmtStatus->bindParam(':dataFim', $dataFim);
$stmtStatus->execute();
$statusCorridas = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);

// Horários de pico
$sqlHorariosPico = "SELECT 
                    HOUR(via_data) as hora,
                    COUNT(*) as total_corridas
                    FROM viagens
                    WHERE via_data BETWEEN :dataInicio AND :dataFim
                    GROUP BY HOUR(via_data)
                    ORDER BY total_corridas DESC
                    LIMIT 5";
$stmtHorariosPico = $conexao->prepare($sqlHorariosPico);
$stmtHorariosPico->bindParam(':dataInicio', $dataInicio);
$stmtHorariosPico->bindParam(':dataFim', $dataFim);
$stmtHorariosPico->execute();
$horariosPico = $stmtHorariosPico->fetchAll(PDO::FETCH_ASSOC);

// Rotas mais populares
$sqlRotasPopulares = "SELECT 
                      CONCAT(via_origem, ' → ', via_destino) as rota,
                      COUNT(*) as total_viagens,
                      AVG(via_valor) as valor_medio
                      FROM viagens
                      WHERE via_data BETWEEN :dataInicio AND :dataFim
                      GROUP BY via_origem, via_destino
                      ORDER BY total_viagens DESC
                      LIMIT 5";
$stmtRotasPopulares = $conexao->prepare($sqlRotasPopulares);
$stmtRotasPopulares->bindParam(':dataInicio', $dataInicio);
$stmtRotasPopulares->bindParam(':dataFim', $dataFim);
$stmtRotasPopulares->execute();
$rotasPopulares = $stmtRotasPopulares->fetchAll(PDO::FETCH_ASSOC);

// Preparar dados para os gráficos
$labelsMeses = [];
$valoresReceita = [];
$valoresCorridas = [];
foreach ($receitaMensal as $item) {
    $labelsMeses[] = date('M/Y', strtotime($item['mes']));
    $valoresReceita[] = (float)$item['total'];
    $valoresCorridas[] = (int)$item['total_corridas'];
}

$labelsStatus = [];
$valoresStatus = [];
$coresStatus = [
    'finalizada' => 'rgba(40, 167, 69, 0.8)',
    'cancelada' => 'rgba(220, 53, 69, 0.8)',
    'em andamento' => 'rgba(255, 193, 7, 0.8)',
    'pendente' => 'rgba(108, 117, 125, 0.8)'
];

foreach ($statusCorridas as $item) {
    $labelsStatus[] = ucfirst($item['via_status']);
    $valoresStatus[] = (int)$item['total'];
}

// Horários de pico formatados
$horariosFormatados = [];
foreach ($horariosPico as $horario) {
    $hora = $horario['hora'];
    $periodo = ($hora >= 5 && $hora < 12) ? 'manhã' : (($hora >= 12 && $hora < 18) ? 'tarde' : 'noite');
    $horariosFormatados[] = [
        'hora' => sprintf('%02d:00', $hora),
        'total' => $horario['total_corridas'],
        'periodo' => $periodo
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX - Painel de Relatórios</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --accent-color: #000000;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --primary-color: #007bff;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--accent-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .stat-card {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: white;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #6c757d;
        }

        .badge-periodo {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 10px;
        }

        .manha {
            background-color: #fff3cd;
            color: #856404;
        }

        .tarde {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .noite {
            background-color: #d4edda;
            color: #155724;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .progress-bar {
            border-radius: 4px;
        }

        .filter-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
            color: var(--accent-color);
        }

        .valor-positivo {
            color: var(--success-color);
        }

        .valor-negativo {
            color: var(--danger-color);
        }

        .valor-neutro {
            color: var(--warning-color);
        }

        .star-rating {
            color: #FFD700;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="section-title"><i class="fas fa-chart-pie"></i> Painel de Relatórios</h1>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="filter-card">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim) ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="window.print()" class="btn btn-outline-dark w-100">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="?data_inicio=<?= date('Y-m-01') ?>&data_fim=<?= date('Y-m-t') ?>" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-calendar-alt"></i> Este Mês
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumo Geral -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="section-title"><i class="fas fa-tachometer-alt"></i> Resumo Geral</h4>
            </div>

            <!-- Cartão de Usuários -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="stat-value"><?= htmlspecialchars($dadosUsuarios['total']) ?></div>
                    <div class="stat-label">Total de Usuários</div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" style="width: <?= ($dadosUsuarios['total'] > 0) ? round(($dadosUsuarios['ativos'] / $dadosUsuarios['total']) * 100) : 0 ?>%"></div>
                        <div class="progress-bar bg-danger" style="width: <?= ($dadosUsuarios['total'] > 0) ? round(($dadosUsuarios['banidos'] / $dadosUsuarios['total']) * 100) : 0 ?>%"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($dadosUsuarios['ativos']) ?> ativos
                            </small>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-danger">
                                <i class="fas fa-ban"></i> <?= htmlspecialchars($dadosUsuarios['banidos']) ?> banidos
                            </small>
                        </div>
                    </div>
                    <?php if ($dadosUsuarios['novos'] > 0): ?>
                        <div class="mt-2 text-info">
                            <small><i class="fas fa-user-plus"></i> <?= htmlspecialchars($dadosUsuarios['novos']) ?> novos</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cartão de Corridas -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-taxi fa-2x"></i>
                    </div>
                    <div class="stat-value"><?= htmlspecialchars($dadosCorridas['total'] ?? 0) ?></div>
                    <div class="stat-label">Total de Corridas</div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" style="width: <?= ($dadosCorridas['total'] > 0) ? round(($dadosCorridas['finalizadas'] / $dadosCorridas['total']) * 100) : 0 ?>%"></div>
                        <div class="progress-bar bg-danger" style="width: <?= ($dadosCorridas['total'] > 0) ? round(($dadosCorridas['canceladas'] / $dadosCorridas['total']) * 100) : 0 ?>%"></div>
                        <div class="progress-bar bg-warning" style="width: <?= ($dadosCorridas['total'] > 0) ? round(($dadosCorridas['em_andamento'] / $dadosCorridas['total']) * 100) : 0 ?>%"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <small class="text-success">
                                <i class="fas fa-check"></i> <?= htmlspecialchars($dadosCorridas['finalizadas'] ?? 0) ?>
                            </small>
                        </div>
                        <div class="col-4 text-center">
                            <small class="text-danger">
                                <i class="fas fa-times"></i> <?= htmlspecialchars($dadosCorridas['canceladas'] ?? 0) ?>
                            </small>
                        </div>
                        <div class="col-4 text-end">
                            <small class="text-warning">
                                <i class="fas fa-spinner"></i> <?= htmlspecialchars($dadosCorridas['em_andamento'] ?? 0) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cartão de Faturamento -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <div class="stat-value">R$ <?= $faturamentoTotal ?></div>
                    <div class="stat-label">Faturamento Total</div>
                    <div class="mt-3">
                        <small><i class="fas fa-arrow-right-arrow-left"></i> Valor médio por corrida: R$ <?= $valorMedio ?></small>
                    </div>
                </div>
            </div>

            <!-- Cartão de Horários de Pico -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div class="stat-label">Horários de Pico</div>
                    <?php if (!empty($horariosFormatados)): ?>
                        <?php foreach ($horariosFormatados as $horario): ?>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>
                                    <i class="fas fa-clock"></i> <?= htmlspecialchars($horario['hora']) ?>
                                    <span class="badge-periodo <?= htmlspecialchars($horario['periodo']) ?>">
                                        <?= htmlspecialchars($horario['periodo']) ?>
                                    </span>
                                </span>
                                <span class="text-muted"><?= htmlspecialchars($horario['total']) ?> corridas</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted mt-2">Nenhum dado disponível</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Gráficos Principais -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="section-title"><i class="fas fa-chart-line"></i> Análise Gráfica</h4>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line"></i> Receita e Volume de Corridas (Últimos 12 meses)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoReceita"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie"></i> Status das Corridas</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-user-tie"></i> Top 5 Usuários Mais Ativos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuário</th>
                                        <th>Corridas</th>
                                        <th>Total Gasto</th>
                                        <th>Média/Corrida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuariosAtivos as $usuario): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($usuario['usu_nome']) ?></td>
                                            <td><?= htmlspecialchars($usuario['total_corridas']) ?></td>
                                            <td>R$ <?= number_format($usuario['total_gasto'], 2, ',', '.') ?></td>
                                            <td>R$ <?= number_format($usuario['total_gasto'] / $usuario['total_corridas'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($usuariosAtivos)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Nenhum dado encontrado no período selecionado</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-motorcycle"></i> Top 5 Mototaxistas Mais Ativos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mototaxista</th>
                                        <th>Corridas</th>
                                        <th>Faturamento</th>
                                        <th>Avaliação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mototaxistasAtivos as $mototaxista): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($mototaxista['fun_nome']) ?></td>
                                            <td><?= htmlspecialchars($mototaxista['total_corridas']) ?></td>
                                            <td>R$ <?= number_format($mototaxista['total_faturado'], 2, ',', '.') ?></td>
                                            <td>
                                                <?php if ($mototaxista['media_avaliacao']): ?>
                                                    <div class="star-rating">
                                                        <?php
                                                        $nota = round($mototaxista['media_avaliacao']);
                                                        echo str_repeat('★', $nota) . str_repeat('☆', 5 - $nota);
                                                        ?>
                                                        <small>(<?= number_format($mototaxista['media_avaliacao'], 1) ?>)</small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Sem avaliações</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($mototaxistasAtivos)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Nenhum dado encontrado no período selecionado</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rotas e Detalhes -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-route"></i> Rotas Mais Populares</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rota</th>
                                        <th>Viagens</th>
                                        <th>Valor Médio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rotasPopulares as $rota): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rota['rota']) ?></td>
                                            <td><?= htmlspecialchars($rota['total_viagens']) ?></td>
                                            <td>R$ <?= number_format($rota['valor_medio'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($rotasPopulares)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Nenhuma rota popular encontrada</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Métricas de Desempenho</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6>Taxa de Finalização</h6>
                                        <?php if ($dadosCorridas['total'] > 0): ?>
                                            <?php
                                            $taxaFinalizacao = ($dadosCorridas['finalizadas'] / $dadosCorridas['total']) * 100;
                                            $classeTaxa = ($taxaFinalizacao > 80) ? 'valor-positivo' : (($taxaFinalizacao > 60) ? 'valor-neutro' : 'valor-negativo');
                                            ?>
                                            <div class="display-4 <?= $classeTaxa ?>"><?= number_format($taxaFinalizacao, 1) ?>%</div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-success" style="width: <?= $taxaFinalizacao ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $dadosCorridas['finalizadas'] ?> de <?= $dadosCorridas['total'] ?> corridas</small>
                                        <?php else: ?>
                                            <div class="text-muted">N/A</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6>Taxa de Cancelamento</h6>
                                        <?php if ($dadosCorridas['total'] > 0): ?>
                                            <?php
                                            $taxaCancelamento = ($dadosCorridas['canceladas'] / $dadosCorridas['total']) * 100;
                                            $classeTaxa = ($taxaCancelamento < 5) ? 'valor-positivo' : (($taxaCancelamento < 15) ? 'valor-neutro' : 'valor-negativo');
                                            ?>
                                            <div class="display-4 <?= $classeTaxa ?>"><?= number_format($taxaCancelamento, 1) ?>%</div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-danger" style="width: <?= $taxaCancelamento ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $dadosCorridas['canceladas'] ?> de <?= $dadosCorridas['total'] ?> corridas</small>
                                        <?php else: ?>
                                            <div class="text-muted">N/A</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6>Novos Usuários</h6>
                                        <div class="display-4"><?= htmlspecialchars($dadosUsuarios['novos'] ?? 0) ?></div>
                                        <small class="text-muted">no período selecionado</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6>Faturamento Médio Diário</h6>
                                        <?php
                                        $dias = (strtotime($dataFim) - strtotime($dataInicio)) / (60 * 60 * 24) + 1;
                                        $faturamentoDiario = ($dadosCorridas['faturamento_total'] ?? 0) / $dias;
                                        ?>
                                        <div class="display-4">R$ <?= number_format($faturamentoDiario, 2, ',', '.') ?></div>
                                        <small class="text-muted">em <?= $dias ?> dias</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0/dist/chartjs-plugin-annotation.min.js"></script>

    <script>
        const ctxStatus = document.getElementById('graficoStatus').getContext('2d');
        const graficoStatus = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labelsStatus) ?>,
                datasets: [{
                    label: 'Status das Corridas',
                    data: <?= json_encode($valoresStatus) ?>,
                    backgroundColor: [
                        <?= implode(',', array_map(function ($status) use ($coresStatus) {
                            return "'" . ($coresStatus[$status] ?? 'rgba(108, 117, 125, 0.8)') . "'";
                        }, array_map('strtolower', $labelsStatus))) ?>
                    ],
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#000',
                            font: {
                                family: 'Righteous',
                                size: 14
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value, ctx) => {
                            const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${percentage}%`;
                        },
                        font: {
                            weight: 'bold',
                            size: 16
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // === GRÁFICO RECEITA (LINE) ===
        Chart.register(window['chartjs-plugin-annotation']); // Registrar plugin de anotação

        const ctxReceita = document.getElementById('graficoReceita').getContext('2d');
        const graficoReceita = new Chart(ctxReceita, {
            type: 'line',
            data: {
                labels: <?= json_encode($labelsMeses) ?>,
                datasets: [{
                    label: 'Receita (R$)',
                    data: <?= json_encode($valoresReceita) ?>,
                    borderColor: 'rgba(40, 167, 69, 0.8)',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }, {
                    label: 'Volume de Corridas',
                    data: <?= json_encode($valoresCorridas) ?>,
                    borderColor: 'rgba(255, 193, 7, 0.8)',
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#000',
                            font: {
                                family: 'Righteous',
                                size: 14
                            }
                        }
                    },
                    datalabels: {
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 14
                        }
                    },
                    annotation: {
                        annotations: {
                            linhaMeta: {
                                type: 'line',
                                yMin: 1000,
                                yMax: 1000,
                                borderColor: 'red',
                                borderWidth: 2,
                                borderDash: [6, 6],
                                label: {
                                    content: 'Meta R$ 1.000,00',
                                    enabled: true,
                                    position: 'end',
                                    backgroundColor: 'red',
                                    color: '#fff',
                                    font: {
                                        family: 'Righteous',
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback(value) {
                                return value.toLocaleString('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                });
                            }
                        },
                        title: {
                            display: true,
                            text: 'Valor em Reais (R$)',
                            color: '#000',
                            font: {
                                family: 'Righteous',
                                size: 14
                            }
                        }
                    },
                    x: {
                        ticks: {
                            color: '#000',
                            font: {
                                family: 'Righteous',
                                size: 12
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>



</body>

</html>