<?php
require '../../bd/conexao.php';
session_start();
if (!isset($_SESSION["logado099"]) || $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
$conexao = conexao::getInstance();

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$sql = "SELECT * FROM motocicletas WHERE mot_codigo = :id LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$motocicleta = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$motocicleta) {
    header('Location: motocicletas.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Motocicleta - ZoomX</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Righteous', cursive;
        }

        body {
            background-color: #f0f0f0;
            color: #000;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            color: #000;
            font-size: 28px;
            letter-spacing: 1px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
            text-transform: uppercase;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #000;
            outline: none;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        button {
            flex: 1;
            background-color: #000;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        button:hover {
            background-color: #333;
        }

        .btn-cancel {
            background-color: #e0e0e0;
            color: #000;
            text-align: center;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #d5d5d5;
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Editar Motocicleta</h1>
                <p class="subtitle">Atualize os dados da motocicleta</p>
            </div>

            <form action="../../actions/actionmotocicleta.php" method="POST">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" value="<?= $motocicleta['mot_codigo']; ?>">

                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($motocicleta['mot_modelo']); ?>" required placeholder="Ex: Honda CG 160">
                </div>

                <div class="form-group">
                    <label for="placa">Placa</label>
                    <input type="text" name="placa" id="placa" value="<?= htmlspecialchars($motocicleta['mot_placa']); ?>" required maxlength="7" placeholder="Ex: ABC1D23">
                </div>

                <div class="form-group">
                    <label for="cor">Cor</label>
                    <input type="text" name="cor" id="cor" value="<?= htmlspecialchars($motocicleta['mot_cor']); ?>" required placeholder="Ex: Vermelha">
                </div>

                <div class="form-group">
                    <label for="ano">Ano</label>
                    <select name="ano" id="ano" required>
                        <?php
                        $anoAtual = date("Y");
                        for ($ano = $anoAtual; $ano >= 1980; $ano--) {
                            $selected = ($ano == $motocicleta['mot_ano']) ? 'selected' : '';
                            echo "<option value=\"$ano\" $selected>$ano</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit">Atualizar</button>
                    <a href="motocicletas.php" class="btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>