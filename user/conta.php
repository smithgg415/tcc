<?php
require '../bd/conexao.php';
$conexao = conexao::getInstance();

session_start();
if (!isset($_SESSION['logado099'])  && $_SESSION['ativo'] != 1 && $_SESSION['tipo'] !== 'usuario') {
    header('Location: ../user/login.php');
    exit;
}


$sql = 'SELECT * FROM usuarios WHERE usu_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $_SESSION["id"]);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<div class='alert alert-danger' role='alert'>Usuário não encontrado!</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | Mototáxi</title>
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

        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-header {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(0, 0, 0, 0.2) 100%);
            z-index: 1;
        }

        .profile-header-content {
            position: relative;
            z-index: 2;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin-right: 1.5rem;
        }

        .profile-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .profile-info-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-info-item:last-child {
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
            font-size: 1.2rem;
        }

        .info-value {
            text-align: right;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-banned {
            color: #dc3545;
            font-weight: bold;
        }

        .btn-edit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-edit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-logout {
            background-color: transparent;
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-logout:hover {
            background-color: var(--accent-color);
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

        .btn-save {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .profile-header {
                text-align: center;
                padding: 1.5rem;
            }

            .profile-avatar {
                margin: 0 auto 1rem;
            }

            .profile-info-item {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-value {
                text-align: left;
            }
        }

        .btn-apagar {
            background-color: transparent;
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-header-content d-flex flex-column flex-md-row align-items-center">
                <div class="profile-avatar mb-3 mb-md-0">
                    <?= strtoupper(substr($usuario['usu_nome'], 0, 1)) ?>
                </div>
                <div class="text-center text-md-start">
                    <h1><?= htmlspecialchars($usuario['usu_nome']) ?></h1>
                    <p class="mb-0">
                        <span class="<?= $usuario['usu_ativo'] ? 'status-active' : 'status-banned' ?>">
                            <i class="bi <?= $usuario['usu_ativo'] ? 'bi-check-circle' : 'bi-slash-circle' ?>"></i>
                            <?= $usuario['usu_ativo'] ? 'Ativo' : 'Banido' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="profile-card">
                    <h2 class="mb-4"><i class="bi bi-person-lines-fill"></i> Informações do Perfil</h2>

                    <div class="profile-info-item">
                        <span class="info-label"><i class="bi bi-person"></i> Nome</span>
                        <span class="info-value"><?= htmlspecialchars($usuario['usu_nome']) ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="info-label"><i class="bi bi-envelope"></i> Email</span>
                        <span class="info-value"><?= htmlspecialchars($usuario['usu_email']) ?></span>
                    </div>

                    <div class="profile-info-item">
                        <span class="info-label"><i class="bi bi-telephone"></i> Telefone</span>
                        <span class="info-value"><?= htmlspecialchars($usuario['usu_telefone']) ?></span>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn-edit" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil"></i> Editar Perfil
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="profile-card text-center">
                    <h3 class="mb-4"><i class="bi bi-shield-lock"></i> Segurança</h3>
                    <p class="mb-4">Mantenha suas informações seguras e atualizadas regularmente.</p>
                    <button class="btn-logout mb-3" onclick="window.location.href='../actions/logout.php'">
                        <i class="bi bi-box-arrow-right"></i> Sair da Conta
                    </button>
                    <form action="../actions/actionusuario.php" method="POST" class="d-inline-block">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= $usuario['usu_codigo'] ?>">
                        <button type="submit" class="btn-apagar">
                            <i class="bi bi-trash"></i> Deletar Conta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../actions/actionusuario.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id" value="<?= $usuario['usu_codigo'] ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome"
                                value="<?= htmlspecialchars($usuario['usu_nome']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($usuario['usu_email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                value="<?= htmlspecialchars($usuario['usu_telefone']) ?>" required>
                        </div>
                        <input type="hidden" name="ativo" value="<?= $usuario['usu_ativo'] ?>">
                        <input type="hidden" name="updated_at" value="<?= date('Y-m-d H:i:s') ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-save">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('telefone').addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
        const formatarTelefone = (telefone) => {
            return telefone.replace(/\D/g, '').replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        };
        document.getElementById('telefone').addEventListener('input', function() {
            this.value = formatarTelefone(this.value);
        });
    </script>
</body>

</html>