<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../../bd/conexao.php';
$conexao = conexao::getInstance();

$sql = 'SELECT * FROM anuncios';
$stmt = $conexao->prepare($sql);
$stmt->execute();
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anúncios Cadastrados | ZoomX</title>
    <!-- Google Fonts - Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --accent-color: #000;
            --border-color: #ddd;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .page-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
        }

        .page-header p {
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        .announcements-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .announcements-table thead {
            background-color: var(--accent-color);
            color: white;
        }

        .announcements-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .announcements-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .announcements-table tbody tr:last-child td {
            border-bottom: none;
        }

        .announcements-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .announcement-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }

        .announcement-description {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .action-btn.edit {
            background-color: var(--accent-color);
            color: white;
        }

        .action-btn.delete {
            background-color: var(--danger-color);
            color: white;
        }

        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #000;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin-bottom: 2rem;
            transition: all 0.3s;
        }

        .add-btn:hover {
            background-color: #666;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            .announcements-table {
                display: block;
                overflow-x: auto;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>
    <div class="container">
        <header class="page-header">
            <h1><i class="bi bi-megaphone-fill"></i> Anúncios Cadastrados</h1>
            <p>Gerencie todos os anúncios do sistema</p>
        </header>

        <a href="adicionar_anuncio.php" class="add-btn">
            <i class="bi bi-plus-lg"></i> Novo Anúncio
        </a>

        <table class="announcements-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Foto</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($anuncios as $anuncio) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($anuncio['anu_codigo']) . "</td>";
                    echo "<td>" . htmlspecialchars($anuncio['anu_titulo']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($anuncio['anu_foto']) . "' alt='Foto do anúncio' class='announcement-image'></td>";
                    echo "<td class='announcement-description' title='" . htmlspecialchars($anuncio['anu_descricao']) . "'>" . htmlspecialchars($anuncio['anu_descricao']) . "</td>";
                    echo "<td class='actions'>";
                    echo "<a href='editar_anuncio.php?id=" . htmlspecialchars($anuncio['anu_codigo']) . "' class='action-btn edit'><i class='bi bi-pencil-fill'></i></a>";
                    echo "<a href='excluir_anuncio.php?id=" . htmlspecialchars($anuncio['anu_codigo']) . "' class='action-btn delete' onclick='return confirm(\"Tem certeza que deseja excluir este anúncio?\")'><i class='bi bi-trash-fill'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Adicione aqui qualquer interação JavaScript necessária
    </script>
</body>

</html>