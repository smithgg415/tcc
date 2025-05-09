<?php
session_start();
require '../bd/conexao.php';
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}


$conexao = conexao::getInstance();

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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin | Todas as Viagens</title>
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
        }

        .table-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 2rem;
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
            font-weight: 500;
        }

        .status-concluido {
            color: #28a745;
            font-weight: bold;
        }

        .status-cancelado {
            color: #dc3545;
            font-weight: bold;
        }

        .status-andamento {
            color: #ffc107;
            font-weight: bold;
        }

        .badge-pagamento {
            background-color: var(--secondary-color);
            color: white;
            font-weight: normal;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .btn-view {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-view:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .search-container {
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .dashboard-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="container-fluid py-4">
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-geo-alt"></i> Todas as Viagens</h1>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-primary p-2">
                        Total: <?= count($viagens) ?> viagens
                    </span>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="search-container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Pesquisar viagens..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button class="btn btn-success" onclick="exportToExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Cliente</th>
                            <th>Mototaxista</th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Valor</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($viagens as $viagem): ?>
                            <tr>
                                <td>#<?= $viagem['via_codigo'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($viagem['via_data'])) ?></td>
                                <td><?= htmlspecialchars($viagem['cliente']) ?></td>
                                <td><?= htmlspecialchars($viagem['mototaxista']) ?></td>
                                <td><?= htmlspecialchars(substr($viagem['via_origem'], 0, 15)) ?>...</td>
                                <td><?= htmlspecialchars(substr($viagem['via_destino'], 0, 15)) ?>...</td>
                                <td class="fw-bold">R$ <?= number_format($viagem['via_valor'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge-pagamento">
                                        <?php
                                        switch ($viagem['via_formapagamento']) {
                                            case 'cartao':
                                                echo '<i class="bi bi-credit-card"></i> Cartão';
                                                break;
                                            case 'dinheiro':
                                                echo '<i class="bi bi-cash"></i> Dinheiro';
                                                break;
                                            case 'pix':
                                                echo '<i class="bi bi-qr-code"></i> Pix';
                                                break;
                                            default:
                                                echo $viagem['via_formapagamento'];
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'status-' . strtolower($viagem['via_status']);
                                    echo "<span class='{$statusClass}'>";
                                    switch (strtolower($viagem['via_status'])) {
                                        case 'concluido':
                                            echo '<i class="bi bi-check-circle"></i> Concluído';
                                            break;
                                        case 'cancelado':
                                            echo '<i class="bi bi-x-circle"></i> Cancelado';
                                            break;
                                        case 'andamento':
                                            echo '<i class="bi bi-arrow-repeat"></i> Em andamento';
                                            break;
                                        default:
                                            echo $viagem['via_status'];
                                    }
                                    echo "</span>";
                                    ?>
                                </td>
                                <td>
                                    <button class="btn-view" onclick="viewDetails(<?= $viagem['via_codigo'] ?>)">
                                        <i class="bi bi-eye"></i> Detalhes
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Detalhes da Viagem</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function viewDetails(id) {
            fetch(`../api/get_viagem_details.php?id_viagem=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const viagem = data.data;
                        const modalBody = document.getElementById('modalBody');

                        modalBody.innerHTML = `
                    <p><strong>ID:</strong> #${viagem.via_codigo}</p>
                    <p><strong>Atendente:</strong> ${viagem.atendente}</p>
                    <p><strong>Cliente:</strong> ${viagem.cliente}</p>
                    <p><strong>Origem:</strong> ${viagem.via_origem}</p>
                    <p><strong>Destino:</strong> ${viagem.via_destino}</p>
                    <p><strong>Data/Hora:</strong> ${new Date(viagem.via_data).toLocaleString('pt-BR')}</p>
                    <p><strong>Valor:</strong> ${viagem.via_valor}</p>
                    <p><strong>Forma de Pagamento:</strong> ${viagem.via_formapagamento}</p>
                    <p><strong>Status:</strong> ${viagem.via_status}</p>
                    <p><strong>Mototaxista:</strong> ${viagem.mototaxista}</p>

                `;

                        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                        modal.show();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar os detalhes da viagem:', error);
                    alert('Erro ao carregar os detalhes da viagem.');
                });
        }


        function exportToExcel() {
            Swal.fire({
                title: 'Exportar para Excel',
                text: 'Deseja exportar todos os registros para Excel?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Exportar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../exports/export_viagens.php';
                }
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });
    </script>
</body>

</html>