<?php
require '../bd/conexao.php';
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}

$conexao = conexao::getInstance();

$sqlSolicitacoes = "SELECT s.sol_codigo, s.sol_origem, s.sol_destino, s.sol_data, 
                           u.usu_nome, s.sol_status
                    FROM solicitacoes s
                    INNER JOIN usuarios u ON s.usu_codigo = u.usu_codigo
                    ORDER BY s.sol_data DESC
                    LIMIT 4";
$solicitacoes = $conexao->query($sqlSolicitacoes)->fetchAll(PDO::FETCH_ASSOC);

// Busca viagens recentes
$sqlViagens = "SELECT v.via_codigo, v.via_data, v.via_valor, 
                      u.usu_nome, f.fun_nome as mototaxista
               FROM viagens v
               INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
               INNER JOIN funcionarios f ON v.fun_codigo = f.fun_codigo
               ORDER BY v.via_data DESC
               LIMIT 4";
$viagens = $conexao->query($sqlViagens)->fetchAll(PDO::FETCH_ASSOC);

$sqlViagensAndamento = "SELECT v.via_codigo, v.via_data, v.via_origem, v.via_destino, v.sol_codigo,
                               u.usu_nome as cliente, f.fun_nome as mototaxista
                        FROM viagens v
                        INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
                        INNER JOIN funcionarios f ON v.fun_codigo = f.fun_codigo
                        WHERE v.via_status = 'em andamento'
                        ORDER BY v.via_data DESC";
$viagensAndamento = $conexao->query($sqlViagensAndamento)->fetchAll(PDO::FETCH_ASSOC);

$totalUsuarios = $conexao->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalMototaxistas = $conexao->query("SELECT COUNT(*) FROM funcionarios WHERE fun_ativo = 1 and fun_cargo = 'mototaxista'")->fetchColumn();
$faturamentoHoje = $conexao->query("SELECT SUM(via_valor) FROM viagens WHERE DATE(via_data) = CURDATE()")->fetchColumn() ?? 0;
$viagensAndamentoCount = count($viagensAndamento);

$sql = "SELECT * FROM motocicletas ORDER BY mot_codigo DESC LIMIT 5";
$motocicletas = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) as total FROM anuncios";
$anunciosPublicados = $conexao->query($sql)->fetch(PDO::FETCH_ASSOC);

$sql = 'SELECT COUNT(8) as total FROM avaliacoes';
$avaliacoes = $conexao->query($sql)->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin | Controle Tudo</title>

    <!-- Fontes e Ícones -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/index_admin.css">
</head>

<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid py-4">
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-speedometer2 me-2"></i> Painel de Controle</h1>
                    <p class="mb-0">Bem-vindo, <?= $_SESSION['nome'] ?? 'Administrador' ?>! Aqui está o resumo das atividades.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex justify-content-between align-items-center">
                    <span class="badge bg-light text-dark fs-6">
                        <i class="bi bi-calendar-check me-1"></i> <?= date('d/m') ?>
                    </span>
                    <div class="column">
                        <p class="message_clime">
                            Clima em Presidente Venceslau:
                        </p>
                        <div class="card small-weather-card shadow-sm">
                            <div class="card-body d-flex align-items-center p-2">
                                <i id="weather-icon" class="bi me-2" style="font-size: 20px;"></i>
                                <div>
                                    <h6 id="current-temp" class="mb-0">Carregando...</h6>
                                    <small id="current-desc" class="text-muted">Previsão</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-2 col-4">
                <div class="card card-custom stat-card">
                    <i class="bi bi-people card-icon"></i>
                    <h5 class="card-title">Usuários</h5>
                    <div class="stat-value"><?= $totalUsuarios ?></div>
                    <a href="usuarios/usuarios.php" class="btn btn-sm btn-outline-dark">Gerenciar</a>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card card-custom stat-card">
                    <i class="bi bi-bicycle card-icon"></i>
                    <h5 class="card-title">Mototaxistas</h5>
                    <div class="stat-value"><?= $totalMototaxistas ?></div>
                    <a href="funcionarios/funcionarios.php" class="btn btn-sm btn-outline-dark">Gerenciar</a>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card card-custom stat-card">
                    <i class="bi bi-graph-up card-icon"></i>
                    <h5 class="card-title">Faturamento Hoje</h5>
                    <div class="stat-value">R$ <?= number_format($faturamentoHoje, 2, ',', '.') ?></div>
                    <a href="relatorios.php" class="btn btn-sm btn-outline-dark">Relatórios</a>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card card-custom stat-card">
                    <i class="bi bi-clock-history card-icon"></i>
                    <h5 class="card-title">Em Andamento</h5>
                    <div class="stat-value"><?= $viagensAndamentoCount ?></div>
                    <a href="#viagens-andamento" class="btn btn-sm btn-outline-dark">Ver</a>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card card-custom stat-card">
                    <i class="bi bi-file-earmark-text card-icon"></i>
                    <h5 class="card-title">Anúncios</h5>
                    <div class="stat-value"><?= $anunciosPublicados['total'] ?></div>
                    <a href="anuncios/anuncios.php" class="btn btn-sm btn-outline-dark">Gerenciar</a>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card card-custom stat-card">
                    <i class="bi bi-star card-icon"></i>
                    <h5 class="card-title">Avaliações</h5>
                    <div class="stat-value"><?= $avaliacoes['total'] ?></div>
                    <a href="avaliacoes/avaliacoes.php" class="btn btn-sm btn-outline-dark">Gerenciar</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-list-check me-2"></i> Últimas Solicitações
                        </div>
                        <a href="solicitacoes.php" class="btn btn-sm btn-light">
                            Ver todas <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($solicitacoes) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Trajeto</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($solicitacoes as $solicitacao): ?>
                                            <tr>
                                                <td>#<?= $solicitacao['sol_codigo'] ?></td>
                                                <td><?= htmlspecialchars($solicitacao['usu_nome']) ?></td>
                                                <td>
                                                    <small class="text-muted"><?= htmlspecialchars(substr($solicitacao['sol_origem'], 0, 15)) ?>...</small>
                                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                    <small class="text-muted"><?= htmlspecialchars(substr($solicitacao['sol_destino'], 0, 15)) ?>...</small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = strtolower($solicitacao['sol_status']);

                                                    if ($status === 'pendente') {
                                                        echo "<form action='solicitacoes.php' method='get' style='display:inline;'> <button type='submit' class='btn btn-warning'><i class='bi bi-hourglass-split me-1'></i> Pendente</button></form>";
                                                    } else {
                                                        $statusClass = 'status-' . $status;
                                                        echo "<span class='status-badge {$statusClass}'>";
                                                        switch ($status) {
                                                            case 'aceito':
                                                                echo "<i class='bi bi-check-circle me-1'></i>";
                                                                break;
                                                            case 'recusado':
                                                                echo "<i class='bi bi-x-circle me-1'></i>";
                                                                break;
                                                        }
                                                        echo ucfirst($solicitacao['sol_status']) . "</span>";
                                                    }
                                                    ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                <i class="bi bi-info-circle me-2"></i> Nenhuma solicitação recente encontrada.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-geo-alt me-2"></i> Últimas Viagens
                        </div>
                        <a href="viagens.php" class="btn btn-sm btn-light">
                            Ver todas <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($viagens) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Mototaxista</th>
                                            <th>Data</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($viagens as $viagem): ?>
                                            <tr>
                                                <td>#<?= $viagem['via_codigo'] ?></td>
                                                <td><?= htmlspecialchars($viagem['usu_nome']) ?></td>
                                                <td><?= htmlspecialchars($viagem['mototaxista']) ?></td>
                                                <td><?= date('d/m H:i', strtotime($viagem['via_data'])) ?></td>
                                                <td class="fw-bold text-success">R$ <?= number_format($viagem['via_valor'], 2, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                <i class="bi bi-info-circle me-2"></i> Nenhuma viagem recente encontrada.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4" id="viagens-andamento">
            <div class="col-6">
                <div class="card card-custom">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-clock-history me-2"></i> Viagens em Andamento
                        </div>
                        <span class="badge bg-light text-dark"><?= $viagensAndamentoCount ?> ativas</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($viagensAndamento) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Mototaxista</th>
                                            <th>Trajeto</th>
                                            <th>Iniciada em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($viagensAndamento as $viagem): ?>
                                            <tr>
                                                <td>#<?= $viagem['via_codigo'] ?></td>
                                                <td><?= htmlspecialchars($viagem['cliente']) ?></td>
                                                <td><?= htmlspecialchars($viagem['mototaxista']) ?></td>
                                                <td>
                                                    <small class="text-muted"><?= htmlspecialchars(substr($viagem['via_origem'], 0, 20)) ?>...</small>
                                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                    <small class="text-muted"><?= htmlspecialchars(substr($viagem['via_destino'], 0, 20)) ?>...</small>
                                                </td>
                                                <td><?= date('d/m H:i', strtotime($viagem['via_data'])) ?></td>
                                                <td>
                                                    <form action="../actions/actionsolicitacao_admin.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="acao" value="finalizar">
                                                        <input type="hidden" name="via_codigo" value="<?= htmlspecialchars($viagem['via_codigo']) ?>">
                                                        <button type="submit" class="btn btn-action btn-finalizar" onclick="return confirm('Tem certeza que deseja finalizar esta viagem?');">
                                                            <i class="bi bi-check-circle me-1"></i> Finalizar
                                                        </button>
                                                    </form>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                <i class="bi bi-info-circle me-2"></i> Nenhuma viagem em andamento no momento.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-6" id="motocicletas">
                <div class="card card-custom">
                    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-motorcycle me-2"></i> Motocicletas
                        </div>
                        <a href="motocicletas/motocicletas.php" class="btn btn-sm btn-light">
                            Gerenciar <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($motocicletas) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Modelo</th>
                                            <th>Placa</th>
                                            <th>Cor</th>
                                            <th>Ano</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($motocicletas as $motocicleta): ?>
                                            <tr>
                                                <td>#<?= $motocicleta['mot_codigo'] ?></td>
                                                <td><?= htmlspecialchars($motocicleta['mot_modelo']) ?></td>
                                                <td><?= htmlspecialchars($motocicleta['mot_placa']) ?></td>
                                                <td><?= htmlspecialchars($motocicleta['mot_cor']) ?></td>
                                                <td><?= htmlspecialchars($motocicleta['mot_ano']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                <i class="bi bi-info-circle me-2"></i> Nenhuma motocicleta cadastrada.
                            </div>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../api/buscar_clima.js"></script>
    <script>
        setInterval(() => {
            window.location.reload();
        }, 5000);
    </script>
</body>

</html>