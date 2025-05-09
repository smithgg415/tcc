<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .footer {
            background: var(--primary);
            color: var(--secondary);
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .footer-logo {
            font-size: 1.8rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .footer-links h4 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--highlight);
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.2s;
        }

        .footer-links a:hover {
            color: var(--secondary);
            padding-left: 5px;
        }
    </style>
</head>

<body>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <span class="footer-logo">ZoomX</span>
                    <p>Transporte rápido e seguro para você e suas encomendas.</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-4">
                    <div class="footer-links">
                        <h4>Serviços</h4>
                        <ul>
                            <li><a href="#">MotoTáxi</a></li>
                            <li><a href="#">MotoEntrega</a></li>
                            <li><a href="#">Express</a></li>
                            <li><a href="#">Planos</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-4">
                    <div class="footer-links">
                        <h4>Empresa</h4>
                        <ul>
                            <li><a href="#">Sobre nós</a></li>
                            <li><a href="#">Carreiras</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Contato</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="footer-links">
                        <h4>Baixe o App</h4>
                        <div class="d-flex flex-column">
                            <a href="#" class="btn btn-dark mb-2"><i class="bi bi-apple"></i> App Store</a>
                            <a href="#" class="btn btn-dark"><i class="bi bi-google-play"></i> Google Play</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 ZoomX. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>

</html>