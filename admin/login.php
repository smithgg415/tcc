<?php
session_start();
require '../bd/conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $senha = trim($_POST["password"] ?? '');

    if (!empty($email) && !empty($senha)) {
        $conexao = conexao::getInstance();

        $sql = "SELECT fun_codigo, fun_email, fun_senha FROM funcionarios WHERE fun_email = :email LIMIT 1";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(':email', $email);

        if ($stm->execute() && $stm->rowCount() === 1) {
            $funcionario = $stm->fetch(PDO::FETCH_ASSOC);

            if ($senha === trim($funcionario["fun_senha"])) {
                $_SESSION["logado099"] = true;
                $_SESSION['tipo'] = 'atendente';
                $_SESSION["ativo"] = 1; 
                $_SESSION["fun_codigo"] = $funcionario["fun_codigo"];
                $_SESSION["nome"] = $funcionario["fun_nome"];

                header("Location: index.php");
                exit;
            }
        }
    }

    $_SESSION["erro_login"] = "Email ou senha inválidos.";
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX - Admin</title>
    <!-- Google Fonts - Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --accent-color: #000;
            --border-color: #ddd;
            --error-color: #d32f2f;
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-image: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        }

        .admin-login-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .admin-login-header {
            background-color: var(--accent-color);
            color: white;
            padding: 25px;
            text-align: center;
            border-bottom: 4px solid rgba(255, 255, 255, 0.1);
        }

        .admin-login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .admin-login-header p {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .admin-login-form {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: normal;
            font-size: 0.9rem;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-family: 'Righteous', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
            background-color: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: white;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05);
        }

        .btn-admin {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: 'Righteous', sans-serif;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .btn-admin:hover {
            background-color: #222;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-login-footer {
            text-align: center;
            padding: 15px;
            border-top: 1px solid var(--border-color);
            font-size: 0.8rem;
            color: #777;
        }

        .error-message {
            color: white;
            background-color: var(--error-color);
            border: 1px solid #b71c1c;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
            display: <?php echo isset($_SESSION["erro_login"]) ? 'block' : 'none'; ?>;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .security-notice {
            font-size: 0.75rem;
            color: #777;
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
        }

        @media (max-width: 480px) {
            .admin-login-container {
                border-radius: 0;
                box-shadow: none;
                border: none;
            }
            
            body {
                padding: 0;
                background: white;
            }
        }
    </style>
</head>

<body>
    <div class="admin-login-container">
        <div class="admin-login-header">
            <h1>ZOOMX ADMIN</h1>
            <p>Área restrita aos administradores</p>
        </div>

        <div class="admin-login-form">
            <?php if(isset($_SESSION["erro_login"])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION["erro_login"]; unset($_SESSION["erro_login"]); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Administrativo</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="admin@zoomx.com.br">
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="btn-admin">Acessar Painel</button>
                
                <div class="security-notice">
                    <i class="fas fa-lock"></i> Todas as atividades são monitoradas
                </div>
            </form>
        </div>

        <div class="admin-login-footer">
            &copy; <?php echo date('Y'); ?> ZoomX - Todos os direitos reservados
        </div>
    </div>

    <script>
        // Mostrar/ocultar senha
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
        
        // Foco no campo email ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>

</html>