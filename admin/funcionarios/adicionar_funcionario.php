<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Funcionário</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000;
            --secondary: #fff;
            --accent: #007bff;
            --light-bg: #f0f0f0;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--light-bg);
            color: var(--primary);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--secondary);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            font-family: 'Righteous', sans-serif;

        }

        button {
            background-color: var(--accent);
            color: var(--secondary);
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .hidden-field {
            display: none;
        }

        .form-group {
            margin-bottom: 15px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 15px;
            }

            form {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Registrar Funcionário</h1>
        <form action="../../actions/actionfuncionario.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" id="telefone" required>
            </div>

            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <select name="cargo" id="cargo" onchange="toggleCNHField()" required>
                    <option value="atendente">Atendente</option>
                    <option value="mototaxista">Mototaxista</option>
                </select>
            </div>

            <div class="form-group hidden-field" id="cnh-field">
                <label for="cnh">CNH:</label>
                <input type="text" name="cnh" id="cnh" maxlength="11" placeholder="Número da CNH" required>
            </div>

            <button type="submit">Registrar</button>
            <input type="hidden" name="acao" value="adicionar">
            <input type="hidden" name="ativo" value="1">
            <input type="hidden" name="data_contratacao" value="<?php echo date('Y-m-d'); ?>">
        </form>
    </div>

    <script>
        function toggleCNHField() {
            const cargo = document.getElementById("cargo").value;
            const cnhField = document.getElementById("cnh-field");

            if (cargo === "mototaxista") {
                cnhField.classList.remove("hidden-field");
            } else {
                cnhField.classList.add("hidden-field");
            }
        }
    </script>
</body>

</html>