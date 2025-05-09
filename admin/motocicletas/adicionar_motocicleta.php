<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();
session_start();
if (!isset($_SESSION["logado099"]) || $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
$sql = "SELECT fun_codigo, fun_nome FROM funcionarios WHERE fun_ativo = 1 AND fun_cargo = 'mototaxista'";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Motocicleta</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Righteous', sans-serif;
            background-color: #f0f0f0;
            color: #000;
            margin: 0;
        }

        main {
            max-width: 600px;
            margin: 3rem auto;
            background-color: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        label {
            display: block;
            margin-top: 1rem;
            font-size: 1.1rem;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
        }

        button {
            margin-top: 2rem;
            width: 100%;
            padding: 12px;
            background-color: #000;
            color: #f0f0f0;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.85;
        }

        .add-button {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <main>
        <h1>Adicionar Motocicleta</h1>
        <form action="../../actions/actionmotocicleta.php" method="POST">
            <label for="modelo">Modelo:</label>
            <input type="text" name="modelo" id="modelo" required>

            <label for="placa">Placa:</label>
            <input type="text" name="placa" id="placa" required maxlength="7">

            <label for="cor">Cor:</label>
            <select name="cor" id="cor" required>
                <option value="">Selecione a cor</option>
                <option value="preto">Preto</option>
                <option value="branco">Branco</option>
                <option value="vermelho">Vermelho</option>
                <option value="azul">Azul</option>
                <option value="verde">Verde</option>
                <option value="amarelo">Amarelo</option>
                <option value="cinza">Cinza</option>
                <option value="prata">Prata</option>
                <option value="roxo">Roxo</option>
                <option value="laranja">Laranja</option>
            </select>

            <label for="ano">Ano:</label>
            <select name="ano" id="ano" required>
                <option value="">Selecione o ano</option>
                <?php
                $anoAtual = date("Y");
                for ($ano = $anoAtual; $ano >= 1980; $ano--) {
                    echo "<option value=\"$ano\">$ano</option>";
                }
                ?>
            </select>

            <label for="fun_codigo">Mototaxista:</label>
            <select name="fun_codigo" id="fun_codigo" required>
                <option value="">Selecione o Dono</option>
                <?php foreach ($funcionarios as $funcionario): ?>
                    <option value="<?= $funcionario['fun_codigo'] ?>"><?= htmlspecialchars($funcionario['fun_nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="acao" value="adicionar">
            <button type="submit" class="add-button">Adicionar Motocicleta</button>
        </form>
    </main>
</body>

</html>