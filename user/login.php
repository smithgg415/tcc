<?php
session_start();
require '../bd/conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $senha = trim($_POST["password"] ?? '');

    if (!empty($email) && !empty($senha)) {
        $conexao = conexao::getInstance();

        // Modifiquei a query para incluir usu_ativo
        $sql = "SELECT usu_codigo, usu_nome, usu_senha, usu_ativo FROM usuarios WHERE usu_email = :email LIMIT 1";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(':email', $email);

        if ($stm->execute() && $stm->rowCount() === 1) {
            $usuario = $stm->fetch(PDO::FETCH_ASSOC);

            if ($usuario['usu_ativo'] == 0) {
                $_SESSION["erro_login"] = "Conta banida";
                $_SESSION["banido"] = true;
                header("Location: login.php");
                exit;
            }

            if ($senha === trim($usuario["usu_senha"])) {
                $_SESSION["logado099"] = true;
                $_SESSION['tipo'] = 'usuario';
                $_SESSION['ativo'] = $usuario['usu_ativo'];
                $_SESSION["id"] = $usuario["usu_codigo"];
                $_SESSION["nome"] = $usuario["usu_nome"];

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
    <title>ZoomX - Login</title>
    <!-- Google Fonts - Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 para pop-ups bonitos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>ZoomX</h1>
            <p>Acesse sua conta de usuário</p>
        </div>

        <div class="login-form">
            <?php if (isset($_SESSION["erro_login"])): ?>
                <div class="error-message">
                    <?php
                    echo $_SESSION["erro_login"];
                    unset($_SESSION["erro_login"]);
                    ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="Digite seu email">
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Digite sua senha">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>
                <div class="error-message <?php echo isset($_SESSION["erro_login"]) ? 'show' : ''; ?>">
                    Usuário ou senha incorretos.
                </div>
                <button type="submit" class="btn">Entrar</button>
            </form>
        </div>

        <div class="login-footer">
            <p>Não tem uma conta? <a href="registrar_se.php">Registre-se</a></p>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        // Pop-up para usuário banido
        <?php if (isset($_SESSION["banido"])): ?>
            Swal.fire({
                title: 'Conta Banida',
                html: `Sua conta foi banida do sistema ZoomX.<br><br>
                      Caso acredite que isso foi um erro, entre em contato com nosso suporte.<br>`,
                icon: 'error',
                confirmButtonColor: '#000',
                confirmButtonText: 'Entendi',
                allowOutsideClick: false
            });
            <?php unset($_SESSION["banido"]); ?>
        <?php endif; ?>
    </script>
</body>

</html>