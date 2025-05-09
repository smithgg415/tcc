<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    $_SESSION["usuario"] = false;
    $_SESSION['tipo'] = 'visitante';
    $_SESSION['ativo'] = 0;
    $_SESSION["id"] = 0;
    $_SESSION["nome"] = 'Visitante';
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/tcc/bd/conexao.php';
$conexao = conexao::getInstance();

$sql = ' SELECT a.*, u.usu_nome FROM avaliacoes a JOIN usuarios u ON a.usu_codigo = u.usu_codigo ORDER BY a.ava_codigo DESC LIMIT 3';
$stmt = $conexao->prepare($sql);
$stmt->execute();
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM anuncios ORDER BY anu_codigo DESC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grupos = array_chunk($anuncios, 4);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoomX - Serviço de Mototáxi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/homescreen.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">ZoomX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicos">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="user/login.php" class="btn btn-zoomx">Entrar</a>
                    </li>
                    <li class="nav-item ms-lg-3 d-none d-lg-block">
                        <a href="admin/login.php" class="btn btn-outline-light">Área Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Seu transporte rápido e seguro</h1>
            <p class="lead mb-5">Conectamos você aos melhores mototaxistas da cidade com apenas alguns cliques.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="user/registrar_se.php" class="btn btn-zoomx btn-lg">Cadastre-se</a>
                <a href="#como-funciona" class="btn btn-outline-light btn-lg">Saiba Mais</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Rápido</h3>
                    <p>Chegue ao seu destino em minutos, evitando o trânsito pesado da cidade.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Seguro</h3>
                    <p>Todos os nossos mototaxistas são verificados e treinados para sua segurança.</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Acessível</h3>
                    <p>Preços justos e competitivos para todas as regiões da cidade.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Como Funciona</h2>
                <p class="lead">Em 3 passos simples você está no seu destino</p>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <span class="text-primary fs-2">1</span>
                            </div>
                            <h3 class="h4 mt-4">Solicite</h3>
                            <p class="mb-0">Informe seu local de partida e destino através do nosso aplicativo ou site.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <span class="text-primary fs-2">2</span>
                            </div>
                            <h3 class="h4 mt-4">Análise</h3>
                            <p class="mb-0">Seu pedido será analisado pelo gestor, em seguida, uma notificação retornará para você</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <span class="text-primary fs-2">3</span>
                            </div>
                            <h3 class="h4 mt-4">Viaje</h3>
                            <p class="mb-0">Aguarde no endereço informado e curta a viagem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="carrossel_anuncios">
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
    </section>
    <section class="testimonials bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">O que dizem nossos clientes</h2>
                <p class="lead">Avaliações de quem já experimentou o ZoomX</p>
            </div>
            <div class="row">
                <?php if (!empty($avaliacoes)): ?>
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="mb-3">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++):
                                        $starClass = ($i <= $avaliacao['ava_nota']) ? 'text-warning' : 'text-muted';
                                    ?>
                                        <i class="fas fa-star <?= $starClass ?>"></i>
                                    <?php endfor; ?>
                                    <h6 class="mb-0 mt-2"><?= htmlspecialchars($avaliacao['usu_nome']) ?></h6>

                                </div>
                                <p class="mb-4">
                                    <?php
                                    if ($avaliacao['ava_comentario'] == null) {
                                        echo "Nenhum comentário deixado.";
                                    } else {
                                        echo htmlspecialchars($avaliacao['ava_comentario']);
                                    } ?>
                                </p>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            Avaliação feita em <?= date('d/m/Y', strtotime($avaliacao['ava_data_avaliacao'])) ?> às <?= date('H:i', strtotime($avaliacao['ava_data_avaliacao'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">Nenhum depoimento encontrado.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5 bg-dark text-white">
        <div class="container text-center py-4">
            <h2 class="display-6 fw-bold mb-4">Pronto para experimentar?</h2>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="btn btn-zoomx btn-lg">
                    <i class="fab fa-google-play me-2"></i> Google Play
                </a>
                <a href="#" class="btn btn-light btn-lg">
                    <i class="fab fa-app-store me-2"></i> App Store
                </a>
            </div>
        </div>
    </section>

    <section id="contato" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 class="display-6 fw-bold mb-4">Entre em Contato</h2>
                    <p class="lead mb-5">Tem dúvidas ou sugestões? Fale conosco!</p>
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Seu nome">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Seu e-mail">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Sua mensagem"></textarea>
                        </div>
                        <button type="submit" class="btn btn-zoomx">Enviar Mensagem</button>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light p-4 h-100">
                        <h3 class="h4 mb-4">Informações de Contato</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i> R. Paulo Sérgio Righetti, 45, Cidade Jardim, Presidente Venceslau - SP
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone me-2 text-primary"></i> (11) 1234-5678
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope me-2 text-primary"></i> contato@zoomx.com.br
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-clock me-2 text-primary"></i> Atendimento 24/7
                            </li>
                        </ul>
                        <div class="mt-4">
                            <h3 class="h4 mb-3">Redes Sociais</h3>
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h3 class="h4 mb-4">ZoomX</h3>
                    <p>Revolucionando o transporte urbano com agilidade, segurança e tecnologia.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="h5 mb-4">Links</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Início</a></li>
                        <li class="mb-2"><a href="#como-funciona" class="text-white text-decoration-none">Como Funciona</a></li>
                        <li class="mb-2"><a href="#servicos" class="text-white text-decoration-none">Serviços</a></li>
                        <li class="mb-2"><a href="#contato" class="text-white text-decoration-none">Contato</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="h5 mb-4">Legal</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Termos de Uso</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Política de Privacidade</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Cookies</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h3 class="h5 mb-4">Newsletter</h3>
                    <p>Assine para receber novidades e promoções.</p>
                    <form class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="Seu e-mail">
                        <button type="submit" class="btn btn-zoomx">Assinar</button>
                    </form>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2025 ZoomX. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>