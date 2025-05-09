<?php
require '../../bd/conexao.php';
$conexao = conexao::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: funcionarios.php');
    exit;
}

$sql = 'SELECT * FROM funcionarios WHERE fun_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário | ZoomX</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --accent-color: #000;
            --border-color: #ddd;
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
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-header {
            background-color: var(--accent-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .form-header h1 {
            font-size: 1.8rem;
        }

        .form-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Righteous', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: #222;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .form-container {
                border-radius: 0;
            }

            .form-body {
                padding: 1.5rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>
    <div class="form-container">
        <div class="form-header">
            <h1>Editar Funcionário</h1>
        </div>
        <form class="form-body" action="../../actions/actionfuncionario.php" method="POST">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?php echo $funcionario['fun_codigo']; ?>">
            <input type="hidden" name="ativo" value="<?php echo $funcionario['fun_ativo']; ?>">

            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($funcionario['fun_nome']); ?>" required>
            </div>

            <div class="form-group">
                <label for="cpf">Telefone:</label>
                <input type="text" name="telefone" id="telefone" class="form-control" value="<?php echo htmlspecialchars($funcionario['fun_telefone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($funcionario['fun_email']); ?>" required>
            </div>
            <?php if ($funcionario['fun_cargo'] == 'mototaxista'): ?>
                <div class="form-group">
                    <label for="cnh">CNH:</label>
                    <input type="text" name="cnh" id="cnh" class="form-control" value="<?php echo htmlspecialchars($funcionario['fun_cnh']); ?>" required>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <select name="cargo" id="cargo" class="form-control" required>
                    <option value="Atendente" <?php echo ($funcionario['fun_cargo'] == 'Atendente') ? 'selected' : ''; ?>>Atendente</option>
                    <option value="Mototaxista" <?php echo ($funcionario['fun_cargo'] == 'Mototaxista') ? 'selected' : ''; ?>>Mototáxista</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Atualizar</button>
        </form>
    </div>

</body>

</html>