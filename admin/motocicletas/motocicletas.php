<?php
session_start();
if (!isset($_SESSION["logado099"]) || $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../../bd/conexao.php';
$conexao = conexao::getInstance();

$sql = "SELECT * FROM motocicletas m INNER JOIN funcionarios f ON m.fun_codigo = f.fun_codigo";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$motocicletas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motocicletas - Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Righteous', sans-serif;
            background-color: #f0f0f0;
            color: #000;
        }

        main {
            padding: 2rem;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #000;
            color: #f0f0f0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .actions a {
            color: #000;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }

        .add-button {
            display: inline-block;
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: #000;
            color: #f0f0f0;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1rem;
        }

        .add-button:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>

    <?php include '../../components/header_admin.php'; ?>

    <main>
        <h1>Motocicletas Cadastradas</h1>

        <table>
            <thead>
                <tr>
                    <th>Modelo</th>
                    <th>Placa</th>
                    <th>Cor</th>
                    <th>Ano</th>
                    <th>Mototaxista</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <a href="adicionar_motocicleta.php" class="add-button">Adicionar Motocicleta</a>

                <?php foreach ($motocicletas as $motocicleta): ?>
                    <tr>
                        <td><?= htmlspecialchars($motocicleta['mot_modelo']) ?></td>
                        <td><?= htmlspecialchars($motocicleta['mot_placa']) ?></td>
                        <td><?= htmlspecialchars($motocicleta['mot_cor']) ?></td>
                        <td><?= htmlspecialchars($motocicleta['mot_ano']) ?></td>
                        <td><?= htmlspecialchars($motocicleta['fun_nome']) ?></td>
                        <td class="actions">
                            <a href="editar_motocicleta.php?id=<?= $motocicleta['mot_codigo'] ?>">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="../../actions/actionmotocicleta.php" method="POST" style="display:inline;">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id" value="<?= $motocicleta['mot_codigo'] ?>">
                                <button type="submit" onclick="verificarAcao()" style="background:none; border:none; color:#000; cursor:pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($motocicletas)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Nenhuma motocicleta cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <script>
        function verificarAcao() {
            if (!confirm("Tem certeza que deseja excluir esta motocicleta?")) {
                event.preventDefault();
            }
        }
    </script>
</body>

</html>