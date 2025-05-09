<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: usuarios.php');
    exit();
}

$id = $_GET['id'];

$sql = 'SELECT * FROM usuarios WHERE usu_codigo = ?';
$stmt = $conexao->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: usuarios.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = 'UPDATE usuarios SET usu_ativo = 0 WHERE usu_codigo = ?';
    $stmt = $conexao->prepare($sql);
    
    if ($stmt->execute([$id])) {
        header('Location: usuarios.php?msg=Usuario banido com sucesso');
        exit();
    } else {
        $erro = 'Erro ao banir o usuário';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Confirmar Banimento</title>
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
        
        .navbar-brand, .nav-link, .dropdown-item, h1, h2, h3, h4, h5, h6 {
            font-family: 'Righteous', sans-serif;
        }
        
        .bg-custom {
            background-color: var(--accent-color);
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 2rem auto;
        }
        
        .btn-custom {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-custom:hover {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
        }
        
        .btn-outline-custom {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-outline-custom:hover {
            background-color: var(--accent-color);
            color: white;
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0"><i class="bi bi-slash-circle me-2"></i>Confirmar Banimento</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        
                        <div class="alert alert-warning">
                            <h4 class="alert-heading">Atenção!</h4>
                            <p>Você está prestes a banir o usuário abaixo. Esta ação irá revogar seu acesso ao sistema.</p>
                            <hr>
                            <p class="mb-0">Tem certeza que deseja continuar?</p>
                        </div>
                        
                        <div class="mb-4 p-3 border rounded">
                            <h5>Informações do Usuário</h5>
                            <ul class="list-unstyled">
                                <li><strong>ID:</strong> <?php echo $usuario['usu_codigo']; ?></li>
                                <li><strong>Nome:</strong> <?php echo $usuario['usu_nome']; ?></li>
                                <li><strong>Email:</strong> <?php echo $usuario['usu_email']; ?></li>
                                <li><strong>Telefone:</strong> <?php echo $usuario['usu_telefone']; ?></li>
                                <li><strong>Status atual:</strong> <span class="badge bg-<?php echo $usuario['usu_ativo'] ? 'success' : 'danger'; ?>">
                                    <?php echo $usuario['usu_ativo'] ? 'Ativo' : 'Banido'; ?>
                                </span></li>
                            </ul>
                        </div>
                        
                        <form method="POST" action="../../actions/actionusuario.php">
                            <input type="hidden" name="acao" value="banir">
                            <input type="hidden" name="id" value="<?php echo $usuario['usu_codigo']; ?>">
                            <div class="d-flex justify-content-between">
                                <a href="usuarios.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-slash-circle"></i> Confirmar Banimento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>