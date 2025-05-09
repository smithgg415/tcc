<?php
require '../bd/conexao.php';
$conexao = conexao::getInstance();

session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}

$sql = 'SELECT * FROM funcionarios WHERE fun_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $_SESSION["fun_codigo"]);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$funcionario) {
    echo "<div class='alert alert-danger' role='alert'>Funcionário não encontrado!</div>";
    exit;
}

// Busca últimas viagens do funcionário
$sqlViagens = "SELECT v.via_codigo, v.via_data, v.via_valor, v.via_status, 
                      u.usu_nome as cliente, v.via_origem, v.via_destino
               FROM viagens v
               INNER JOIN usuarios u ON v.usu_codigo = u.usu_codigo
               WHERE v.fun_codigo = :id
               ORDER BY v.via_data DESC
               LIMIT 5";
$stmtViagens = $conexao->prepare($sqlViagens);
$stmtViagens->bindParam(':id', $_SESSION["id"]);
$stmtViagens->execute();
$viagens = $stmtViagens->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Mototaxista | <?= htmlspecialchars($funcionario['fun_nome']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --primary-color: #000;
            --secondary-color: #333;
            --accent-color: #e63946;
            --card-bg: #fff;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .dashboard-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(0, 0, 0, 0.2) 100%);
            z-index: 1;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-right: 1.5rem;
        }

        .card-custom {
            background-color: var(--card-bg);
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
            height: 100%;
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }

        .card-header-custom {
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            font-weight: bold;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-completed {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .table-custom {
            width: 100%;
        }

        .table-custom thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table-custom th {
            border-bottom: none;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 0.5rem;
        }

        .btn-action {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-action:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            color: white;
        }
        .btn-logout {
            background-color:red;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-logout:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            color: white;
        }

        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            border: none;
        }

        .form-control {
            border-radius: 50px;
            padding: 0.75rem 1.25rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .dashboard-header {
                text-align: center;
            }

            .profile-avatar {
                margin: 0 auto 1rem;
            }

            .info-item {
                flex-direction: column;
                gap: 0.3rem;
            }
        }

    </style>
</head>

<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid py-4">
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8 d-flex align-items-center">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($funcionario['fun_nome'], 0, 1)) ?>
                    </div>
                    <div>
                        <h2><?= htmlspecialchars($funcionario['fun_nome']) ?></h2>
                        <p class="mb-0">
                            <span class="badge bg-secondary">
                                <i class="bi bi-bicycle"></i> <?= htmlspecialchars($funcionario['fun_cargo']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <i class="bi bi-person-badge"></i> Meus Dados
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-person"></i> Nome</span>
                            <span><?= htmlspecialchars($funcionario['fun_nome']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-envelope"></i> Email</span>
                            <span><?= htmlspecialchars($funcionario['fun_email']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-telephone"></i> Telefone</span>
                            <span><?= htmlspecialchars($funcionario['fun_telefone']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-calendar"></i> Cadastro</span>
                            <span><?= date('d/m/Y', strtotime($funcionario['fun_data_contratacao'])) ?></span>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn-action" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <a href="../actions/logout.php" class="btn-logout">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <i class="bi bi-clock-history"></i> Últimas Corridas
                    </div>
                    <div class="card-body">
                        <?php if (count($viagens) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-custom">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Data</th>
                                            <th>Cliente</th>
                                            <th>Trajeto</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($viagens as $viagem): ?>
                                            <tr>
                                                <td>#<?= $viagem['via_codigo'] ?></td>
                                                <td><?= date('d/m H:i', strtotime($viagem['via_data'])) ?></td>
                                                <td><?= htmlspecialchars($viagem['cliente']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars(substr($viagem['via_origem'], 0, 10)) ?>...
                                                    <i class="bi bi-arrow-right"></i>
                                                    <?= htmlspecialchars(substr($viagem['via_destino'], 0, 10)) ?>...
                                                </td>
                                                <td class="fw-bold">R$ <?= number_format($viagem['via_valor'], 2, ',', '.') ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'status-' . strtolower($viagem['via_status']);
                                                    echo "<span class='status-badge $statusClass'>";
                                                    switch (strtolower($viagem['via_status'])) {
                                                        case 'concluido':
                                                            echo '<i class="bi bi-check-circle"></i> Concluída';
                                                            break;
                                                        case 'andamento':
                                                            echo '<i class="bi bi-arrow-repeat"></i> Em andamento';
                                                            break;
                                                        case 'cancelado':
                                                            echo '<i class="bi bi-x-circle"></i> Cancelada';
                                                            break;
                                                        default:
                                                            echo $viagem['via_status'];
                                                    }
                                                    echo "</span>";
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <a href="viagens.php" class="btn-action">
                                    <i class="bi bi-list-ul"></i> Ver todas
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Nenhuma corrida registrada recentemente.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-check-circle"></i> Concluídas</h5>
                        <h3 class="card-text">
                            <?= $conexao->query("SELECT COUNT(*) FROM viagens WHERE ate_codigo = {$_SESSION['fun_codigo']} AND via_status = 'concluido'")->fetchColumn() ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-arrow-repeat"></i> Em Andamento</h5>
                        <h3 class="card-text">
                            <?= $conexao->query("SELECT COUNT(*) FROM viagens WHERE ate_codigo = {$_SESSION['fun_codigo']} AND via_status = 'andamento'")->fetchColumn() ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-currency-dollar"></i> Ganhos Hoje</h5>
                        <h3 class="card-text">
                            R$ <?= number_format($conexao->query("SELECT SUM(via_valor) FROM viagens WHERE ate_codigo = {$_SESSION['fun_codigo']} AND DATE(via_data) = CURDATE()")->fetchColumn() ?? 0, 2, ',', '.') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-graph-up"></i> Total Ganhos</h5>
                        <h3 class="card-text">
                            R$ <?= number_format($conexao->query("SELECT SUM(via_valor) FROM viagens WHERE ate_codigo = {$_SESSION['fun_codigo']}")->fetchColumn() ?? 0, 2, ',', '.') ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../actions/actionfuncionario.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id" value="<?= $funcionario['fun_codigo'] ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome"
                                value="<?= htmlspecialchars($funcionario['fun_nome']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($funcionario['fun_email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                value="<?= htmlspecialchars($funcionario['fun_telefone']) ?>" required>
                        </div>
                        <input type="hidden" name="ativo" value="<?= $funcionario['fun_ativo'] ?>">
                        <input type="hidden" name="data_contratacao" value="<?= $funcionario['fun_data_contratacao'] ?>">
                        <input type="hidden" name="cnh" value="<?= $funcionario['fun_cnh'] ?>">
                        <input type="hidden" name="cargo" value="<?= $funcionario['fun_cargo'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>

</html>