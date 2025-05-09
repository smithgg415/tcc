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
    <title>Cadastrar Anúncio | ZoomX</title>
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
            min-height: 100vh;
            }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
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
            margin-bottom: 0.5rem;
        }

        .form-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .form-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            color: #555;
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

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Righteous', sans-serif;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background-color: #222;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .preview-container {
            margin-top: 1rem;
            text-align: center;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            border: 1px dashed var(--border-color);
            display: none;
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .form-container {
                border-radius: 0;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .form-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../../components/header_admin.php'; ?>

    <div class="form-container">
        <div class="form-header">
            <h1><i class="bi bi-megaphone-fill"></i> Novo Anúncio</h1>
            <p>Preencha os campos abaixo para cadastrar um novo anúncio</p>
        </div>

        <form class="form-body" action="../../actions/actionanuncio.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="adicionar">

            <div class="form-group">
                <label for="titulo" class="form-label">Título do Anúncio</label>
                <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Digite o título do anúncio" required>
            </div>

            <div class="form-group">
                <label for="foto" class="form-label">URL da Imagem</label>
                <input type="text" name="foto" id="foto" class="form-control" placeholder="https://exemplo.com/imagem.jpg" required>
                <div class="preview-container">
                    <img id="imagePreview" class="image-preview" alt="Pré-visualização da imagem">
                </div>
            </div>

            <div class="form-group">
                <label for="descricao" class="form-label">Descrição do Anúncio</label>
                <textarea name="descricao" id="descricao" class="form-control" placeholder="Descreva detalhes sobre o anúncio..." required></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-save-fill"></i> Cadastrar Anúncio
            </button>
        </form>
    </div>

    <script>
        document.getElementById('foto').addEventListener('input', function () {
            const preview = document.getElementById('imagePreview');
            const url = this.value.trim();

            if (url) {
                preview.src = url;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            const titulo = document.getElementById('titulo').value.trim();
            const foto = document.getElementById('foto').value.trim();
            const descricao = document.getElementById('descricao').value.trim();

            if (!titulo || !foto || !descricao) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    </script>
</body>

</html>
