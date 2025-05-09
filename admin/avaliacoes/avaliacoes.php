<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
$sql = "SELECT a.*, u.usu_nome FROM avaliacoes a
        JOIN usuarios u ON a.usu_codigo = u.usu_codigo
        ORDER BY a.ava_codigo DESC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações | ZoomX Admin</title>
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
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
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

        .evaluations-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .evaluations-table thead {
            background-color: var(--accent-color);
            color: white;
        }

        .evaluations-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .evaluations-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .evaluations-table tbody tr:last-child td {
            border-bottom: none;
        }

        .evaluations-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--accent-color);
        }

        .rating-stars {
            display: flex;
            gap: 0.2rem;
        }

        .star {
            color: var(--warning-color);
            font-size: 1.2rem;
        }

        .empty-star {
            color: #ddd;
        }

        .comment {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .no-comment {
            color: var(--info-color);
            font-style: italic;
        }

        .negative-comment {
            color: var(--danger-color);
        }

        .positive-comment {
            color: var(--success-color);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            border: none;
            background: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .action-btn.view {
            color: var(--info-color);
        }

        .action-btn.delete {
            color: var(--danger-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            .evaluations-table {
                display: block;
                overflow-x: auto;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>
    <div class="container">
        <header class="page-header">
            <h1><i class="bi bi-star-fill"></i> Avaliações</h1>
            <p>Visualize todas as avaliações dos usuários do sistema</p>
        </header>

        <table class="evaluations-table">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Nota</th>
                    <th>Comentário</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($avaliacoes as $avaliacao): ?>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($avaliacao['usu_nome'], 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($avaliacao['usu_nome']) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $avaliacao['ava_nota'] ? '-fill' : '' ?> star"></i>
                                <?php endfor; ?>
                                <span style="margin-left: 0.5rem;"><?= $avaliacao['ava_nota'] ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if ($avaliacao['ava_comentario'] == null): ?>
                                <span class="comment no-comment">Nenhum comentário</span>
                            <?php elseif ($avaliacao['ava_nota'] < 3): ?>
                                <span class="comment negative-comment"><?= htmlspecialchars($avaliacao['ava_comentario']) ?></span>
                            <?php else: ?>
                                <span class="comment positive-comment"><?= htmlspecialchars($avaliacao['ava_comentario']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>