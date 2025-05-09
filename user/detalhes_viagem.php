<?php
session_start();
if (!isset($_SESSION['logado099'])  && $_SESSION['ativo'] != 1 && $_SESSION['tipo'] !== 'usuario') {
    header('Location: ../user/login.php');
    exit;
}


require '../bd/conexao.php';
$conexao = conexao::getInstance();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$sql = 'SELECT * FROM viagens WHERE via_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$viagem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$viagem) {
    echo "<div class='alert alert-danger' role='alert'>Viagem não encontrada!</div>";
    exit;
}

$sql = 'SELECT * FROM avaliacoes WHERE via_codigo = :id';
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Viagem | ZoomX</title>
    
    <!-- Google Fonts - Righteous -->
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/detalhes_viagem.css"
</head>

<body>
    <div class="container">
        <header class="trip-header">
            <h1>Detalhes da Viagem</h1>
            <p>Viagem #<?= htmlspecialchars($viagem['via_codigo']) ?></p>
        </header>
        
        <div class="trip-card">
            <div class="trip-section">
                <div class="detail-item">
                    <i class="bi bi-geo-alt-fill detail-icon"></i>
                    <div class="detail-content">
                        <div class="detail-label">Origem</div>
                        <div class="detail-value"><?= htmlspecialchars($viagem['via_origem']) ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="bi bi-geo-fill detail-icon"></i>
                    <div class="detail-content">
                        <div class="detail-label">Destino</div>
                        <div class="detail-value"><?= htmlspecialchars($viagem['via_destino']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="trip-section">
                <div class="detail-item">
                    <i class="bi bi-calendar-check detail-icon"></i>
                    <div class="detail-content">
                        <div class="detail-label">Data e Hora</div>
                        <div class="detail-value"><?= date('d/m/Y \à\s H:i', strtotime($viagem['via_data'])) ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="bi bi-cash-coin detail-icon"></i>
                    <div class="detail-content">
                        <div class="detail-label">Valor</div>
                        <div class="detail-value">R$ <?= number_format($viagem['via_valor'], 2, ',', '.') ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="bi bi-info-circle detail-icon"></i>
                    <div class="detail-content">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <?php
                            $statusClass = '';
                            switch($viagem['via_status']) {
                                case 'Confirmada':
                                    $statusClass = 'status-confirmed';
                                    break;
                                case 'Pendente':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'Cancelada':
                                    $statusClass = 'status-canceled';
                                    break;
                                default:
                                    $statusClass = '';
                            }
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($viagem['via_status']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="trip-section">
                <div class="detail-item">
                    <i class="bi bi-star detail-icon"></i>
                    <div class="detail-content">
                        <div class="detail-label">Avaliação</div>
                        <?php if ($avaliacao): ?>
                            <div class="rating-stars">
                                <?php
                                $nota = (int)$avaliacao['ava_nota'];
                                for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $nota ? '-fill' : '' ?> star"></i>
                                <?php endfor; ?>
                                <span style="margin-left: 0.5rem;"><?= number_format($avaliacao['ava_nota'], 1, ',', '.') ?></span>
                            </div>
                            <?php if (!empty($avaliacao['ava_comentario'])): ?>
                                <div class="rating-comment">
                                    "<?= htmlspecialchars($avaliacao['ava_comentario']) ?>"
                                    <div style="font-size: 0.8rem; color: #666; margin-top: 0.5rem;">
                                        Avaliado em <?= date('d/m/Y', strtotime($avaliacao['ava_data_avaliacao'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="detail-value">Viagem ainda não avaliada</div>
                            <a href="avaliar_viagem.php?via_codigo=<?= $viagem['via_codigo'] ?>" class="btn-evaluate mt-2">
                                <i class="bi bi-pencil"></i> Avaliar Viagem
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="historico.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Voltar ao Histórico
        </a>
    </div>
</body>

</html>