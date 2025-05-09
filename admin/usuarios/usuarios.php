<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
$sql = 'SELECT * FROM usuarios ORDER BY usu_codigo DESC';
$stmt = $conexao->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Gerenciar Usuários</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --accent-color: #000;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .navbar-brand,
        .nav-link,
        .dropdown-item,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Righteous', sans-serif;
        }

        .bg-custom {
            background-color: var(--accent-color);
        }

        .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: var(--accent-color);
            color: white;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .btn-outline-custom {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-outline-custom:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-custom {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-custom:hover {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-banned {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-people-fill me-2"></i>
                                Gerenciar Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear-fill me-2"></i>
                                Configurações
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Usuários Cadastrados</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo $usuario['usu_codigo']; ?></td>
                                            <td><?php echo $usuario['usu_nome']; ?></td>
                                            <td><?php echo $usuario['usu_email']; ?></td>
                                            <td><?php echo $usuario['usu_telefone']; ?></td>
                                            <td>
                                                <span class="<?php echo $usuario['usu_ativo'] ? 'status-active' : 'status-banned'; ?>">
                                                    <?php echo $usuario['usu_ativo'] ? 'Ativo' : 'Banido'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="editar_usuario.php?id=<?php echo $usuario['usu_codigo']; ?>" class="btn btn-sm btn-outline-custom" title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="excluir_usuario.php?id=<?php echo $usuario['usu_codigo']; ?>" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                    <?php if ($usuario['usu_ativo']): ?>
                                                        <a href="banir_usuario.php?id=<?php echo $usuario['usu_codigo']; ?>" class="btn btn-sm btn-outline-warning" title="Banir">
                                                            <i class="bi bi-slash-circle"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <form action="../../actions/actionusuario.php" method="post">
                                                            <input type="hidden" name="acao" value="desbanir">
                                                            <input type="hidden" name="id" value="<?php echo $usuario['usu_codigo']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Desbanir">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>

                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Ativa tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>

</html>