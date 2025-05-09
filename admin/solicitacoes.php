<?php
session_start();
if (!isset($_SESSION["logado099"]) && $_SESSION["tipo"] !== 'atendente') {
    header("Location: ../index.php");
    exit;
}
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$sql = "SELECT s.sol_codigo, s.sol_origem, s.sol_destino, s.sol_valor, s.sol_servico, 
               u.usu_nome, u.usu_codigo
        FROM solicitacoes s 
        INNER JOIN usuarios u ON u.usu_codigo = s.usu_codigo
        WHERE s.sol_status = 'pendente' 
        ORDER BY s.sol_data DESC";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mototaxistas = $conexao->query(
    "SELECT fun_codigo, fun_nome 
     FROM funcionarios 
     WHERE fun_ativo = 1 AND fun_cargo = 'mototaxista'"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin | Solicitações Pendentes</title>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f0f0f0;
            --text-color: #000;
            --primary-color: #1a1a1a;
            --accent-color: #333;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Righteous', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--accent-color);
        }

        .header h1 {
            font-size: 28px;
            letter-spacing: 1px;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 16px;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border-left: 4px solid var(--success-color);
            color: var(--text-color);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .action-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        select {
            padding: 8px 12px;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            font-family: 'Righteous', sans-serif;
            min-width: 180px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-family: 'Righteous', sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state p {
            font-size: 18px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .action-form {
                flex-direction: column;
                align-items: flex-start;
            }

            th,
            td {
                padding: 10px 5px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header_admin.php'; ?>
    <div class="container-fluid">
        <div class="header">
            <h1>Solicitações Pendentes</h1>
        </div>

        <?php if (!empty($_SESSION['mensagem'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['mensagem']);
                unset($_SESSION['mensagem']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <?php if (count($solicitacoes) === 0): ?>
                <div class="empty-state">
                    <h3>Nenhuma solicitação pendente</h3>
                    <p>Não há solicitações aguardando aprovação no momento.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Valor</th>
                                <th>Serviço</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitacoes as $solicitacao): ?>
                                <tr>
                                    <td><?= htmlspecialchars($solicitacao['sol_codigo']); ?></td>
                                    <td><?= htmlspecialchars($solicitacao['usu_nome']); ?></td>
                                    <td><?= htmlspecialchars($solicitacao['sol_origem']); ?></td>
                                    <td><?= htmlspecialchars($solicitacao['sol_destino']); ?></td>
                                    <td>R$ <?= number_format($solicitacao['sol_valor'], 2, ',', '.'); ?></td>
                                    <td><?= ucfirst(htmlspecialchars($solicitacao['sol_servico'])); ?></td>
                                    <td>
                                        <form class="action-form" method="POST" action="../actions/actionsolicitacao_admin.php" onsubmit="return validarFormulario(this)">
                                            <input type="hidden" name="id_solicitacao" value="<?= $solicitacao['sol_codigo']; ?>">

                                            <select name="funcionario_codigo" id="funcionario_codigo" <?= ($_POST['acao'] == 'aceitar' ? 'required' : ''); ?>>
                                                <option value="">Selecione um mototaxista</option>
                                                <?php foreach ($mototaxistas as $mototaxista): ?>
                                                    <option value="<?= $mototaxista['fun_codigo']; ?>">
                                                        <?= htmlspecialchars($mototaxista['fun_nome']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <button type="submit" name="acao" value="aceitar" class="btn btn-success">
                                                Aceitar
                                            </button>
                                            <button type="submit" name="acao" value="recusar" class="btn btn-danger">
                                                Recusar
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        const atualizarLista = () => {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('tbody');
                    if (newTable) {
                        document.querySelector('tbody').innerHTML = newTable.innerHTML;
                    }
                })
                .finally(() => setTimeout(atualizarLista, 10000));
        };
        window.onload = atualizarLista;

        function validarFormulario(form) {
            const acao = form.querySelector('button[type="submit"][clicked="true"]').value;
            const select = form.querySelector('select[name="funcionario_codigo"]');

            if (acao === 'recusar') {
                select.removeAttribute('required');
            }
            return true;
        }

        document.querySelectorAll('form button[type="submit"]').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('form button[type="submit"]').forEach(btn => btn.removeAttribute('clicked'));
                this.setAttribute('clicked', 'true');
            });
        });
    </script>
</body>

</html>