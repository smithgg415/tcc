<?php
require '../bd/conexao.php';
session_start();
if (!isset($_SESSION['logado099'])  && $_SESSION['ativo'] != 1 && $_SESSION['tipo'] !== 'usuario') {
    header('Location: ../user/login.php');
    exit;
}


$conexao = conexao::getInstance();

$via_codigo = isset($_GET['via_codigo']) ? (int)$_GET['via_codigo'] : 0;
$usu_codigo = $_SESSION['usu_codigo'] ?? null;

$sql = "SELECT * FROM viagens WHERE via_codigo = :via_codigo";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':via_codigo', $via_codigo, PDO::PARAM_INT);
$stmt->execute();
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$viagem) {
    echo "<div class='alert alert-danger'>Viagem não encontrada!</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Viagem - ZoomX</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Google Fonts - Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/avaliar_viagem.css">
</head>

<body>
    <div class="container-fluid">
        <?php include '../components/header.php'; ?>
    </div>
    <form class="rating-form" action="../actions/actionavaliacao.php" method="POST">
        <div class="rating-header">
            <h2><i class="bi bi-star-fill"></i> Avaliar Viagem</h2>
            <p>Conte como foi sua experiência</p>
        </div>

        <div class="rating-content">
            <div class="trip-info">
                <p><strong>Viagem #<?= htmlspecialchars($via_codigo) ?></strong></p>
                <p>Sua opinião nos ajuda a melhorar nosso serviço</p>
            </div>

            <input type="hidden" name="acao" value="adicionar">
            <input type="hidden" name="via_codigo" value="<?= $via_codigo ?>">
            <input type="hidden" name="usu_codigo" value="<?= $_SESSION['id'] ?>">
            <input type="hidden" id="rating-value" name="nota" value="">

            <div class="rating-section">
                <label class="rating-label">Qual nota você dá para esta viagem?</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5">
                    <label for="star5" title="Excelente"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" title="Bom"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" title="Regular"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" title="Ruim"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" title="Péssimo"><i class="bi bi-star-fill"></i></label>
                </div>
            </div>

            <div class="comment-section">
                <label class="rating-label">Comentário (opcional)</label>
                <textarea name="comentario" placeholder="Conte mais detalhes sobre sua experiência..."></textarea>
            </div>

            <button type="submit" class="submit-btn">
                <i class="bi bi-send-fill"></i> ENVIAR AVALIAÇÃO
            </button>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('.star-rating input').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('rating-value').value = this.value;
            });
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            if (document.getElementById('rating-value').value === '') {
                e.preventDefault();
                alert('Por favor, selecione uma nota para a viagem');
            }
        });
    </script>
</body>

</html>