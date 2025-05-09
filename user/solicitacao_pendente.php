<?php
session_start();
if (!isset($_SESSION['hora_abertura_' . $_GET['id']])) {
    $_SESSION['hora_abertura_' . $_GET['id']] = time();
}
$hora_abertura = $_SESSION['hora_abertura_' . $_GET['id']];

require '../bd/conexao.php';
$conexao = conexao::getInstance();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=missing_id");
    exit;
}
if (!isset($_SESSION['logado099']) && $_SESSION['tipo'] != 'usuario') {
    exit;
}

$id_solicitacao = intval($_GET['id']);

$sql = "SELECT s.*, u.usu_nome, u.usu_email, u.usu_telefone
        FROM solicitacoes s
        INNER JOIN usuarios u ON u.usu_codigo = s.usu_codigo
        WHERE s.sol_codigo = :id LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id_solicitacao, PDO::PARAM_INT);
$stmt->execute();

$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    header("Location: index.php?error=not_found");
    exit;
}

// Formata os dados para exibição
$valor_formatado = number_format($solicitacao['sol_valor'], 2, ',', '.');
$status_class = strtolower($solicitacao['sol_status']) === 'aceita' ? 'status-accepted' : (strtolower($solicitacao['sol_status']) === 'recusada' ? 'status-rejected' : 'status-pending');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação #<?= $solicitacao['sol_codigo'] ?> | ZoomX</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/solicitacao_pendente.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Solicitação #<?= $solicitacao['sol_codigo'] ?></h1>
            <span class="status-badge <?= $status_class ?>">
                <?= ucfirst($solicitacao['sol_status']) ?>
            </span>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">Serviço</span>
                <span class="detail-value"><?= ucfirst($solicitacao['sol_servico']) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Valor</span>
                <span class="detail-value">R$ <?= $valor_formatado ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Origem</span>
                <span class="detail-value"><?= htmlspecialchars($solicitacao['sol_origem']) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Destino</span>
                <span class="detail-value"><?= htmlspecialchars($solicitacao['sol_destino']) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Horário</span>
                <span class="detail-value"><?= date('H:i', strtotime($solicitacao['sol_data'])) ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Distância</span>
                <span class="detail-value"><?= $solicitacao['sol_distancia'] ?> km</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Data</span>
                <span class="detail-value"><?= date('d/m/Y', strtotime($solicitacao['sol_data'])) ?></span>
            </div>
            <div class="detail-item" id="cancelContainer">
                <span class="detail-label">Cancelar solicitação <small>(<span id="countdown">10</span>s)</small></span>
                <form action="../actions/actionsolicitacao.php" method="POST" class="cancel-form">
                    <input type="hidden" name="acao" value="cancelar">
                    <input type="hidden" name="id" value="<?= $solicitacao['sol_codigo'] ?>">
                    <button type="submit" class="btn-cancel" id="cancelButton">Cancelar</button>
                </form>
            </div>

        </div>

        <?php if (!empty($solicitacao['sol_observacao'])): ?>
            <div class="detail-item">
                <span class="detail-label">Observações</span>
                <p><?= htmlspecialchars($solicitacao['sol_observacao']) ?></p>
            </div>
        <?php endif; ?>

        <div class="user-info">
            <h3>Informações do Cliente</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Nome</span>
                    <span class="detail-value"><?= htmlspecialchars($solicitacao['usu_nome']) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">E-mail</span>
                    <span class="detail-value"><?= htmlspecialchars($solicitacao['usu_email']) ?></span>
                </div>

                <?php if (!empty($solicitacao['usu_telefone'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Telefone</span>
                        <span class="detail-value"><?= htmlspecialchars($solicitacao['usu_telefone']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (
            !empty($solicitacao['sol_lat_origem']) && !empty($solicitacao['sol_lng_origem']) &&
            !empty($solicitacao['sol_lat_destino']) && !empty($solicitacao['sol_lng_destino'])
        ): ?>
            <div class="map-container" id="map">
            </div>
        <?php endif; ?>
    </div>

    <div class="notification-popup" id="notificationPopup">
        <i class="fas fa-check-circle notification-icon notification-success" id="notificationIcon"></i>
        <div class="notification-content">
            <div class="notification-title" id="notificationTitle">Aguarde!</div>
            <div id="notificationMessage">Atualizando o status</div>
        </div>
    </div>

    <?php if (isset($_GET['status']) && in_array($_GET['status'], ['aceita', 'recusada'])): ?>
        <script>
            const popup = document.getElementById('notificationPopup');
            const icon = document.getElementById('notificationIcon');
            const title = document.getElementById('notificationTitle');
            const message = document.getElementById('notificationMessage');

            if ('<?= $_GET['status'] ?>' === 'aceita') {
                title.textContent = 'Sucesso!';
                message.textContent = 'Solicitação aceita com sucesso';
                icon.className = 'fas fa-check-circle notification-icon notification-success';
            } else {
                title.textContent = 'Aviso';
                message.textContent = 'Solicitação recusada';
                icon.className = 'fas fa-times-circle notification-icon notification-error';
            }

            popup.classList.add('show');

            setTimeout(() => {
                popup.classList.remove('show');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 300);
            }, 3000);
        </script>
    <?php endif; ?>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
        <?php if (
            !empty($solicitacao['sol_lat_origem']) && !empty($solicitacao['sol_lng_origem']) &&
            !empty($solicitacao['sol_lat_destino']) && !empty($solicitacao['sol_lng_destino'])
        ): ?>

            function initMap() {
                const map = L.map('map').setView([-21.8732, -51.8432], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Adicionar marcadores
                const origem = L.marker([
                        <?= $solicitacao['sol_lat_origem'] ?>,
                        <?= $solicitacao['sol_lng_origem'] ?>
                    ]).addTo(map)
                    .bindPopup("<b>Origem:</b><br><?= addslashes($solicitacao['sol_origem']) ?>");

                const destino = L.marker([
                        <?= $solicitacao['sol_lat_destino'] ?>,
                        <?= $solicitacao['sol_lng_destino'] ?>
                    ]).addTo(map)
                    .bindPopup("<b>Destino:</b><br><?= addslashes($solicitacao['sol_destino']) ?>");

                const bounds = L.latLngBounds([
                    [<?= $solicitacao['sol_lat_origem'] ?>, <?= $solicitacao['sol_lng_origem'] ?>],
                    [<?= $solicitacao['sol_lat_destino'] ?>, <?= $solicitacao['sol_lng_destino'] ?>]
                ]);
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            document.addEventListener('DOMContentLoaded', initMap);
        <?php endif; ?>

        function verificarStatus() {
            const id = <?= (int)$_GET['id'] ?>;
            fetch(`../api/verificar_notificacao.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'pendente') {
                        const popup = document.getElementById('notificationPopup');
                        const icon = document.getElementById('notificationIcon');
                        const title = document.getElementById('notificationTitle');
                        const message = document.getElementById('notificationMessage');

                        if (data.status === 'aceita') {
                            title.textContent = 'Sucesso!';
                            message.textContent = data.mensagem;
                            icon.className = 'fas fa-check-circle notification-icon notification-success';
                        } else {
                            title.textContent = 'Aviso';
                            message.textContent = data.mensagem;
                            icon.className = 'fas fa-times-circle notification-icon notification-error';
                        }

                        popup.classList.add('show');

                        setTimeout(() => {
                            popup.classList.remove('show');
                            setTimeout(() => {
                                window.location.href = `solicitacao_pendente.php?id=${id}&status=${data.status}`;
                            }, 300);
                        }, 3000);
                    }
                })
                .catch(console.error);
        }

        <?php if (!isset($_GET['status'])): ?>
            setInterval(verificarStatus, 5000);
        <?php endif; ?>
        const horaAberturaServidor = <?= $hora_abertura ?>;
        const agora = Math.floor(Date.now() / 1000);
        const segundosDecorridos = agora - horaAberturaServidor;
        let tempoRestante = 10 - segundosDecorridos;

        const countdownSpan = document.getElementById('countdown');
        const cancelButton = document.getElementById('cancelButton');
        const cancelContainer = document.getElementById('cancelContainer');

        if (tempoRestante <= 0) {
            if (cancelButton) cancelButton.disabled = true;
            if (cancelContainer) cancelContainer.style.opacity = 0.5;
        } else {
            const interval = setInterval(() => {
                tempoRestante--;
                if (countdownSpan) countdownSpan.textContent = tempoRestante;

                if (tempoRestante <= 0) {
                    clearInterval(interval);
                    if (cancelButton) cancelButton.disabled = true;
                    if (cancelContainer) cancelContainer.style.opacity = 0.5;
                }
            }, 1000);
        }
    </script>
</body>

</html>