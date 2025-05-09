<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();

session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}

// Consulta otimizada com ordenação
$sql = 'SELECT * 
        FROM funcionarios 
        ORDER BY fun_nome ASC';
$stmt = $conexao->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários | Painel Admin</title>
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

        .admin-container {
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .page-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-add {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-add:hover {
            background-color: #d62c3a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }

        .table-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom thead {
            background-color: var(--primary-color);
            color: white;
            position: sticky;
            top: 0;
        }

        .table-custom th {
            padding: 1rem;
            text-align: left;
            border: none;
            font-weight: 500;
        }

        .table-custom td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            vertical-align: middle;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .status-active {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-inactive {
            background-color: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .action-btns {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
        }

        .btn-edit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-toggle, .btn-toggle-relatorio {
            text-decoration: none;
            background-color: transparent;
            color: var(--text-color);
            border: 1px solid rgba(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
        }

        .btn-toggle:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .search-container {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-family: 'Righteous', sans-serif;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 0 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.25rem;
            }

            .table-container {
                padding: 1rem;
            }

            .table-custom th,
            .table-custom td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }

            .action-btns {
                flex-direction: column;
                gap: 0.3rem;
            }

            .btn-edit,
            .btn-toggle {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <div class="admin-container container-fluid">
        <div class="page-header">
            <h1>
                <i class="bi bi-people-fill"></i>
                Gerenciar Funcionários
            </h1>
            <a href="adicionar_funcionario.php" class="btn-add">
                <i class="bi bi-plus-lg"></i>
                Novo Funcionário
            </a>
        </div>

        <div class="table-container">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Pesquisar funcionários..." id="searchInput">
                <button class="btn-edit" onclick="filterTable()">
                    <i class="bi bi-search"></i>
                    Buscar
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Cargo</th>
                            <th>Status</th>
                            <th>Ações</th>
                            <th>Relatório</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($funcionario['fun_codigo']) ?></td>
                                <td><?= htmlspecialchars($funcionario['fun_nome']) ?></td>
                                <td><?= htmlspecialchars($funcionario['fun_telefone']) ?></td>
                                <td><?= htmlspecialchars($funcionario['fun_cargo']) ?></td>
                                <td>
                                    <span class="status-badge <?= $funcionario['fun_ativo'] ? 'status-active' : 'status-inactive' ?>">
                                        <i class="bi <?= $funcionario['fun_ativo'] ? 'bi-check-circle' : 'bi-slash-circle' ?>"></i>
                                        <?= $funcionario['fun_ativo'] ? 'Ativo' : 'Indisponível' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="editar_funcionario.php?id=<?= htmlspecialchars($funcionario['fun_codigo']) ?>" class="btn-edit">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </a>

                                        <form method="post" action="../../actions/actionfuncionario.php" class="action-form">
                                            <input type="hidden" name="acao" value="ativar_desativar">
                                            <input type="hidden" name="fun_codigo" value="<?= $funcionario['fun_codigo'] ?>">
                                            <input type="hidden" name="ativo" value="<?= $funcionario['fun_ativo'] ? 0 : 1 ?>">

                                            <button type="submit" class="btn-toggle">
                                                <i class="bi <?= $funcionario['fun_ativo'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                                <?= $funcionario['fun_ativo'] ? 'Desativar' : 'Ativar' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <a href="quantidade_viagens.php?id=<?= htmlspecialchars($funcionario['fun_codigo']) ?>" class="btn-toggle-relatorio">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Relatório do Funcionário
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Função para filtrar a tabela
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', filterTable);

        function toggleStatus(id, newStatus) {
            Swal.fire({
                title: 'Confirmar ação',
                text: newStatus ? 'Deseja ativar este funcionário?' : 'Deseja desativar este funcionário?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: newStatus ? '#28a745' : '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../actions/actionfuncionario.php?acao=toggle_status&id=${id}&status=${newStatus}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Sucesso!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Erro!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>