<?php
// Iniciar a sessão com tratamento de erros

session_start();
if (!isset($_SESSION['logado099'])  && $_SESSION['ativo'] != 1 && $_SESSION['tipo'] !== 'usuario') {
    header('Location: ../user/login.php');
    exit;
}

$usuario_id = filter_var($_SESSION['id'], FILTER_VALIDATE_INT);

require '../bd/conexao.php';
try {
    $conexao = conexao::getInstance();

    $sql = 'SELECT v.via_codigo, v.via_data, v.via_valor, v.via_formapagamento,
               s.sol_distancia, s.sol_servico, v.fun_codigo, f.fun_nome
        FROM viagens v
        INNER JOIN solicitacoes s ON v.sol_codigo = s.sol_codigo
        INNER JOIN funcionarios f ON v.fun_codigo = f.fun_codigo
        WHERE v.usu_codigo = :usu_codigo
        ORDER BY v.via_codigo DESC';


    $stm = $conexao->prepare($sql);
    $stm->bindParam(':usu_codigo', $usuario_id, PDO::PARAM_INT);
    $stm->execute();
    $viagens = $stm->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Erro de banco de dados: ' . $e->getMessage());
    $viagens = [];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Viagens | Mototáxi</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/historico.css">
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <header class="page-header">
            <h1>Seu Histórico de Viagens</h1>
            <p>Todas as suas corridas realizadas em um só lugar</p>
        </header>

        <div class="trips-grid">
            <?php if (!empty($viagens)): ?>
                <?php foreach ($viagens as $index => $viagem): ?>
                    <div class="trip-card">
                        <div class="trip-card__header">
                            <h2 class="trip-card__title">Viagem #<?= htmlspecialchars($viagem['via_codigo']) ?></h2>
                            <span class="trip-card__date">
                                <i class="far fa-calendar-alt"></i>
                                <?= date('d/m/Y \à\s H:i', strtotime($viagem['via_data'])) ?>
                            </span>
                        </div>
                        <div class="trip-card__body">
                            <div class="trip-info">
                                <span class="trip-info__label">
                                    <i class="fas fa-taxi"></i> Serviço
                                </span>
                                <span class="trip-info__value servico">
                                    <?= ucfirst(htmlspecialchars($viagem['sol_servico'])) ?>
                                </span>
                            </div>
                            <div class="trip-info">
                                <span class="trip-info__label">
                                    <i class="fas fa-user"></i> Funcionário
                                </span>
                                <span class="trip-info__value servico">
                                    <?= htmlspecialchars($viagem['fun_nome']) ?>
                                </span>

                            </div>

                            <div class="trip-info">
                                <span class="trip-info__label">
                                    <i class="fas fa-road"></i> Distância
                                </span>
                                <span class="trip-info__value">
                                    <?= number_format($viagem['sol_distancia'], 2, ',', '.') ?> km
                                </span>
                            </div>

                            <div class="trip-info">
                                <span class="trip-info__label">
                                    <i class="fas fa-wallet"></i> Pagamento
                                </span>
                                <span class="trip-info__value">
                                    <?php
                                    $paymentMethod = htmlspecialchars($viagem['via_formapagamento']);
                                    $icon = '';
                                    $text = '';

                                    switch ($paymentMethod) {
                                        case 'cartao':
                                            $icon = 'fa-credit-card';
                                            $text = 'Cartão';
                                            break;
                                        case 'dinheiro':
                                            $icon = 'fa-money-bill-wave';
                                            $text = 'Dinheiro';
                                            break;
                                        case 'pix':
                                            $icon = 'fa-qrcode';
                                            $text = 'Pix';
                                            break;
                                        default:
                                            $icon = 'fa-money-bill-wave';
                                            $text = $paymentMethod;
                                    }
                                    ?>
                                    <span class="payment-method">
                                        <i class="fas <?= $icon ?>"></i> <?= $text ?>
                                    </span>
                                </span>
                            </div>

                            <div class="trip-info">
                                <span class="trip-info__label">
                                    <i class="fas fa-tag"></i> Valor
                                </span>
                                <span class="trip-info__value trip-info__value--price">
                                    R$ <?= number_format($viagem['via_valor'], 2, ',', '.') ?>
                                </span>
                            </div>
                            <div class="trip-info">
                                <span class="trip-info__label">
                                    <i class="fas fa-info-circle"></i> Detalhes
                                </span>
                                <span class="trip-info__value">
                                    <a href="detalhes_viagem.php?id=<?= htmlspecialchars($viagem['via_codigo']) ?>" class="btn btn-primary">Ver Detalhes</a>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="empty-state__title">Nenhuma viagem registrada</h3>
                    <p class="empty-state__message">
                        Você ainda não realizou nenhuma viagem. Quando fizer sua primeira corrida,
                        ela aparecerá aqui no seu histórico.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>