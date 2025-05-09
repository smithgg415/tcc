    // Inicializa o mapa
    let map = L.map('map').setView([-21.8732, -51.8432], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Variáveis globais
    let routeLayer = null;
    let origemMarker = null;
    let destinoMarker = null;
    let typingTimer;
    const typingDelay = 1000;
    let lastRequestTime = 0;
    const minRequestInterval = 2000; // 2 segundos entre requisições

    // Elementos DOM
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorMessage = document.getElementById('errorMessage');
    const origemInput = document.getElementById('origem');
    const destinoInput = document.getElementById('destino');
    const larguraInput = document.getElementById('largura');
    const alturaInput = document.getElementById('altura');
    const comprimentoInput = document.getElementById('comprimento');
    const pesoInput = document.getElementById('peso');
    const submitBtn = document.getElementById('submitBtn');
    const solicitacaoForm = document.getElementById('solicitacaoForm');

    // Funções de controle de UI
    function showLoading() {
        loadingOverlay.style.display = 'flex';
        submitBtn.disabled = true;
    }

    function hideLoading() {
        loadingOverlay.style.display = 'none';
        submitBtn.disabled = false;
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }

    const geocodeCache = {};
    async function buscarCoordenadas(endereco) {
        if (geocodeCache[endereco]) {
            return geocodeCache[endereco];
        }

        const cidade = "Presidente Venceslau, SP, Brasil";
        const enderecoCompleto = `${endereco}, ${cidade}`;
        const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(enderecoCompleto)}&format=json&limit=1`;

        try {
            const now = Date.now();
            const timeSinceLastRequest = now - lastRequestTime;

            if (timeSinceLastRequest < 1000) {
                await new Promise(resolve => setTimeout(resolve, 1000 - timeSinceLastRequest));
            }

            const response = await fetch(url);
            lastRequestTime = Date.now();

            if (!response.ok) throw new Error('Erro na requisição');

            const data = await response.json();
            if (data.length === 0) throw new Error('Endereço não encontrado');

            const result = {
                lat: parseFloat(data[0].lat),
                lon: parseFloat(data[0].lon)
            };

            geocodeCache[endereco] = result;
            return result;

        } catch (error) {
            console.error('Erro ao buscar coordenadas:', error);
            throw error;
        }
    }

    async function calcularRotaEntrePontos(origemLat, origemLon, destinoLat, destinoLon) {
        const url = `https://router.project-osrm.org/route/v1/driving/${origemLon},${origemLat};${destinoLon},${destinoLat}?overview=full&geometries=geojson`;

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Erro ao calcular rota');

            return await response.json();
        } catch (error) {
            console.error('Erro ao calcular rota:', error);
            throw error;
        }
    }

    function validarEndereco(endereco) {
        return endereco && endereco.trim().length >= 5;
    }

    function validarDimensoes(largura, altura, comprimento, peso) {
        return largura > 0 && altura > 0 && comprimento > 0 && peso > 0;
    }

    function calcularValorEntrega(distanciaKm, largura, altura, comprimento, peso) {
        const valorBase = 4.00;
        const valorPorKm = 0.60;

        const volume = (largura * altura * comprimento) / 1000000;

        let volumeExtra = 0;
        if (volume > 0.1) volumeExtra = 5.00;
        else if (volume > 0.05) volumeExtra = 3.00;

        let pesoExtra = 0;
        if (peso > 10) pesoExtra = 5.00;
        else if (peso > 5) pesoExtra = 1.00;

        const valorTotal = valorBase + (valorPorKm * distanciaKm) + volumeExtra + pesoExtra;
        return parseFloat(valorTotal.toFixed(2));
    }

    async function calcularRota() {
        const origem = origemInput.value.trim();
        const destino = destinoInput.value.trim();
        const largura = parseFloat(larguraInput.value) || 0;
        const altura = parseFloat(alturaInput.value) || 0;
        const comprimento = parseFloat(comprimentoInput.value) || 0;
        const peso = parseFloat(pesoInput.value) || 0;

        if (!validarEndereco(origem) || !validarEndereco(destino)) {
            showError('Por favor, insira endereços válidos (mínimo 5 caracteres)');
            return;
        }

        if (!validarDimensoes(largura, altura, comprimento, peso)) {
            showError('Por favor, preencha as dimensões e peso corretamente');
            return;
        }

        if (!navigator.onLine) {
            showError('Você está offline. Conecte-se para calcular rotas.');
            return;
        }

        if (Date.now() - lastRequestTime < minRequestInterval) {
            return;
        }

        try {
            showLoading();
            document.getElementById('route-info').style.opacity = '0.7';

            const coordOrigem = await buscarCoordenadas(origem);
            const coordDestino = await buscarCoordenadas(destino);

            document.getElementById('lat_origem').value = coordOrigem.lat;
            document.getElementById('lng_origem').value = coordOrigem.lon;
            document.getElementById('lat_destino').value = coordDestino.lat;
            document.getElementById('lng_destino').value = coordDestino.lon;

            const rota = await calcularRotaEntrePontos(
                coordOrigem.lat, coordOrigem.lon,
                coordDestino.lat, coordDestino.lon
            );

            if (rota.code !== 'Ok' || !rota.routes || rota.routes.length === 0) {
                throw new Error('Nenhuma rota encontrada');
            }

            const distanciaMetros = rota.routes[0].distance;
            const distanciaKm = (distanciaMetros / 1000).toFixed(2);
            const duracaoSegundos = rota.routes[0].duration;
            const duracaoMinutos = Math.ceil(duracaoSegundos / 60);

            const valorTotal = calcularValorEntrega(distanciaKm, largura, altura, comprimento, peso);

            document.getElementById('distancia').value = distanciaKm;
            document.getElementById('valor').value = valorTotal;
            document.getElementById('tempo_estimado').value = duracaoMinutos;

            document.getElementById('info-distancia').textContent = `${distanciaKm} km`;
            document.getElementById('info-valor').textContent = `R$ ${valorTotal.toFixed(2)}`;
            document.getElementById('info-tempo').textContent = `${duracaoMinutos} min`;

            if (routeLayer) map.removeLayer(routeLayer);
            if (origemMarker) map.removeLayer(origemMarker);
            if (destinoMarker) map.removeLayer(destinoMarker);

            routeLayer = L.geoJSON(rota.routes[0].geometry, {
                style: {
                    color: '#007bff',
                    weight: 5,
                    opacity: 0.8
                }
            }).addTo(map);

            origemMarker = L.marker([coordOrigem.lat, coordOrigem.lon], {
                icon: L.divIcon({
                    className: 'marker-icon',
                    html: '<div style="background:#007bff;width:20px;height:20px;border-radius:50%;border:2px solid white;"></div>'
                })
            }).addTo(map).bindPopup(`<b>Origem:</b> ${origem}`).openPopup();

            destinoMarker = L.marker([coordDestino.lat, coordDestino.lon], {
                icon: L.divIcon({
                    className: 'marker-icon',
                    html: '<div style="background:#28a745;width:20px;height:20px;border-radius:50%;border:2px solid white;"></div>'
                })
            }).addTo(map).bindPopup(`<b>Destino:</b> ${destino}`).openPopup();

            map.fitBounds(L.latLngBounds(
                [coordOrigem.lat, coordOrigem.lon],
                [coordDestino.lat, coordDestino.lon]
            ), {
                padding: [50, 50]
            });

        } catch (error) {
            console.error('Erro ao calcular rota:', error);
            showError('Erro ao calcular rota: ' + error.message);
        } finally {
            hideLoading();
            document.getElementById('route-info').style.opacity = '1';
        }
    }

    [origemInput, destinoInput].forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                calcularRota();
            }, typingDelay);
        });
    });

    [larguraInput, alturaInput, comprimentoInput, pesoInput].forEach(input => {
        input.addEventListener('change', calcularRota);
    });

    solicitacaoForm.addEventListener('submit', function(e) {
        const distancia = document.getElementById('distancia').value;
        if (!distancia || distancia === '0') {
            e.preventDefault();
            showError('Por favor, calcule a rota antes de enviar');
            calcularRota();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (origemInput.value && destinoInput.value) {
            setTimeout(calcularRota, 1000);
        }
    });

    function mostrarPix() {
        const formaPagamento = document.getElementById('forma_pagamento').value;
        const pix = document.getElementById('pix');
        const qrCode = document.getElementById('qrCode');
        const whatsapp = document.getElementById('whatsapp');
        if (formaPagamento === 'pix') {
            pix.style.display = 'block';
            qrCode.style.display = 'block';
            whatsapp.style.display = 'block';
        } else {
            pix.style.display = 'none';
            qrCode.style.display = 'none';
            whatsapp.style.display = 'none';
        }
    }