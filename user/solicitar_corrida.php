<?php

session_start();
if (!isset($_SESSION['logado099'])  && $_SESSION['ativo'] != 1 && $_SESSION['tipo'] !== 'usuario') {
    header('Location: ../user/login.php');
    exit;
}

$chavePix = '12345678900'; // Substitua pela chave Pix real
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Corrida | ZoomX</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="../css/solicitar_corrida.css">
</head>

<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Calculando rota...</div>
        </div>
    </div>

    <!-- Error Message -->
    <div class="error-message" id="errorMessage"></div>

    <div class="app-container">
        <div class="form-section">
            <a href="index.php" class="comeBack">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <div class="form-header">
                <img src="../assets/motorcycle.png" alt="MotoTáxi" class="img-fluid mb-3" style="width: 100px; height: auto;">
                <h1>Solicitar Corrida</h1>
                <p>Preencha os campos abaixo para solicitar seu mototáxi</p>
            </div>

            <form action="../actions/actionsolicitacao.php" method="post" id="solicitacaoForm">
                <input type="hidden" name="acao" value="adicionar">

                <div class="form-group">
                    <label for="origem" class="form-label">Origem</label>
                    <input type="text" id="origem" name="origem" class="form-input" placeholder="Rua e número" required>
                </div>

                <div class="form-group">
                    <label for="destino" class="form-label">Destino</label>
                    <input type="text" id="destino" name="destino" class="form-input" placeholder="Rua e número" required>
                </div>

                <input type="hidden" id="lat_origem" name="lat_origem">
                <input type="hidden" id="lng_origem" name="lng_origem">
                <input type="hidden" id="lat_destino" name="lat_destino">
                <input type="hidden" id="lng_destino" name="lng_destino">
                <input type="hidden" id="distancia" name="distancia">
                <input type="hidden" id="valor" name="valor">
                <input type="hidden" id="tempo_estimado" name="tempo_estimado">
                <input type="hidden" name="servico" value="mototaxi">
                <input type="hidden" name="usu_codigo" value="<?= $_SESSION['id'] ?>">

                <div class="form-group">
                    <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                    <select name="forma_pagamento" id="forma_pagamento" class="form-select" required onchange="mostrarPix()">
                        <option value="">Selecione...</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                        <option value="pix">Pix</option>
                    </select>
                </div>
                <p id="pix" style="display: none; text-align:center;color:#28a745;">Faça a transação para a seguinte chave pix <?= $chavePix; ?></p>
                <img id="qrCode" src="../assets/qr_code.png" alt="QR Code" style="display: none; width: 100px; height: auto; margin: 0 auto;">
                <p id="whatsapp" style="display: none; text-align:center;color:#28a745;">E envie o comprovante para este <a href='https://wa.me/' target="_blank" style="color:#28a745">WhatsApp</a></p>
                <div class="form-group">
                    <label for="observacao" class="form-label">Observações (opcional)</label>
                    <input type="text" id="observacao" name="observacao" class="form-input" placeholder="Alguma informação adicional">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Confirmar Corrida</button>
                </div>
            </form>
        </div>

        <div class="map-section">
            <div id="map"></div>

            <div class="route-info" id="route-info">
                <div class="info-item">
                    <div class="info-label">Distância</div>
                    <div class="info-value" id="info-distancia">0 km</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Valor</div>
                    <div class="info-value price" id="info-valor">R$ 0,00</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tempo Estimado</div>
                    <div class="info-value" id="info-tempo">0 min</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="../js/solicitar_corrida.js"></script>
</body>

</html>