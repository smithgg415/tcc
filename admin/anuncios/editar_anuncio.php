<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../../bd/conexao.php';

$conexao = conexao::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: anuncios.php');
    exit;
}

$sql = 'SELECT * FROM anuncios WHERE anu_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$anuncio = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Anúncio | Painel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f0f0f0;
            --text: #000;
            --card: #fff;
            --border: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg);
            color: var(--text);
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: var(--card);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background-color: var(--text);
            color: #fff;
            padding: 1.5rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.8rem;
        }

        .form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Righteous', sans-serif;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--text);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--text);
            color: #fff;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #222;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../../components/header_admin.php'; ?>
<div class="container">
    <div class="header">
        <h1>Editar Anúncio</h1>
    </div>
    <form class="form" action="../../actions/actionanuncio.php" method="POST">
        <input type="hidden" name="acao" value="editar">
        <input type="hidden" name="id" value="<?= $anuncio['anu_codigo'] ?>">

        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" class="form-control" value="<?= htmlspecialchars($anuncio['anu_titulo']) ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4" class="form-control" required><?= htmlspecialchars($anuncio['anu_descricao']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="imagem">URL da Imagem</label>
            <input type="text" id="foto" name="foto" class="form-control" value="<?= htmlspecialchars($anuncio['anu_foto']) ?>">
        </div>
        <button type="submit" class="btn">Salvar Alterações</button>
    </form>
</div>

</body>
</html>
