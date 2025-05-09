<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/tcc/bd/conexao.php';
$conexao = conexao::getInstance();

$sql = "SELECT * FROM anuncios ORDER BY anu_codigo DESC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Quebrar os anúncios em grupos de 4
$grupos = array_chunk($anuncios, 4);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anúncios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-custom {
            height: 200px;
            overflow: hidden;
            border-radius: 12px;
        }

        .card-custom img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .carousel-item {
            transition: transform 1s ease-in-out;
        }
    </style>
</head>
<body style="background-color: #f0f0f0;">

<div class="container py-5">
    <h2 class="text-center mb-4">Parceiros do ZOOMX</h2>
    <div id="carouselAnuncios" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">

            <?php foreach ($grupos as $index => $grupo): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="row g-3">
                        <?php foreach ($grupo as $anuncio): ?>
                            <div class="col-md-3">
                                <div class="card card-custom shadow-sm">
                                    <?php if (!empty($anuncio['anu_foto'])): ?>
                                        <img src="<?= htmlspecialchars($anuncio['anu_foto']) ?>" alt="Anúncio" class="card-img-top">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300x200?text=Sem+Imagem" alt="Sem Imagem" class="card-img-top">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselAnuncios" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselAnuncios" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
