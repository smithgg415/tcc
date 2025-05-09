<?php require_once __DIR__ . '/../routes/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX</title>

    <!-- Fonte Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS + Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #000;
            --secondary: #fff;
            --accent: #007bff;
            --light-bg: #f8f9fa;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--light-bg);
            color: var(--primary);
            line-height: 1.6;
        }

        .navbar {
            background-color: var(--secondary);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-size: 1.8rem;
            color: var(--primary);
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link {
            color: var(--primary);
            font-size: 1rem;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .navbar-nav .nav-link:hover {
            color: var(--accent);
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>index.php">ZoomX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>user/index.php">
                            <i class="bi bi-house-door-fill"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>user/solicitar_corrida.php">
                            <i class="bi bi-bicycle"></i> MotoTáxi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>user/solicitar_entrega.php">
                            <i class="bi bi-box-seam"></i> MotoEntrega
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>user/historico.php">
                            <i class="bi bi-clock-history"></i> Histórico
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>user/conta.php">
                            <i class="bi bi-person-circle"></i> Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>actions/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
