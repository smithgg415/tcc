-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/05/2025 às 05:41
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tcc`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `anuncios`
--

CREATE TABLE `anuncios` (
  `anu_codigo` int(11) NOT NULL,
  `anu_titulo` varchar(255) NOT NULL,
  `anu_foto` varchar(255) NOT NULL,
  `anu_descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `anuncios`
--

INSERT INTO `anuncios` (`anu_codigo`, `anu_titulo`, `anu_foto`, `anu_descricao`) VALUES
(1, 'Promoção Especial', 'https://canada1.discourse-cdn.com/ifood/original/3X/7/0/701ec0740eabe56404807e7895d6b3c5e628db56.png', 'Aproveite nossas ofertas imperdíveis!'),
(2, 'Entrega Rápida', 'https://canada1.discourse-cdn.com/ifood/original/3X/7/0/701ec0740eabe56404807e7895d6b3c5e628db56.png', 'Receba seu pedido em tempo recorde.'),
(3, 'Novos Benefícios', 'https://neofeed.com.br/wp-content/uploads/2024/12/ifood-benefcios-1200x900.webp', 'Conheça os novos benefícios exclusivos.'),
(4, 'Tecnologia iFood', 'https://t.ctcdn.com.br/hBrMnLAkYNDOkZIaoceep7ENhe8=/1200x675/smart/i525126.jpeg', 'A inovação que transforma sua entrega.'),
(5, 'Pedido com Segurança', 'https://files.tecnoblog.net/wp-content/uploads/2022/05/ifood_capa-2_tb-700x394.png', 'Faça seu pedido com mais segurança e tranquilidade.'),
(6, 'Experiência Única', 'https://files.tecnoblog.net/wp-content/uploads/2022/05/ifood_capa-3_tb-700x394.png', 'Descubra uma nova forma de pedir comida.'),
(7, 'Descontos Imbatíveis', 'https://i0.statig.com.br/bancodeimagens/3o/qw/j7/3oqwj7fgrj2w75d61ptp9qgnd.jpg', 'Economize mais nas suas próximas corridas ou entregas.'),
(8, 'Ofertas do Dia', 'https://www.dmtemdebate.com.br/wp-content/uploads/2022/04/a-publica-bressane-entreagdor-aplicativo-ifood-1024x585.webp', 'Confira as promoções especiais disponíveis por tempo limitado.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `ava_codigo` int(11) NOT NULL,
  `usu_codigo` int(11) NOT NULL,
  `via_codigo` int(11) NOT NULL,
  `ava_nota` tinyint(4) NOT NULL CHECK (`ava_nota` between 1 and 5),
  `ava_comentario` text DEFAULT NULL,
  `ava_data_avaliacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`ava_codigo`, `usu_codigo`, `via_codigo`, `ava_nota`, `ava_comentario`, `ava_data_avaliacao`) VALUES
(4, 1, 8, 5, 'Serviço ótimo', '2025-05-04 21:03:11'),
(5, 1, 7, 5, 'Gostei muito!', '2025-05-04 21:19:40'),
(6, 1, 6, 3, '', '2025-05-04 21:37:03'),
(7, 1, 9, 5, 'Caí', '2025-05-04 22:39:32'),
(8, 1, 11, 5, 'Muito Bom', '2025-05-04 23:37:12'),
(9, 1, 12, 5, 'Serviço ótimo!', '2025-05-05 03:11:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `cup_codigo` varchar(50) NOT NULL,
  `cup_codigo_id` int(11) NOT NULL,
  `cup_descricao` text DEFAULT NULL,
  `cup_tipo_desconto` enum('percentual','fixo') NOT NULL,
  `cup_valor_desconto` decimal(10,2) NOT NULL,
  `cup_valor_minimo` decimal(10,2) DEFAULT 0.00,
  `cup_quantidade_uso` int(11) DEFAULT 1,
  `cup_usado` int(11) DEFAULT 0,
  `cup_ativo` tinyint(1) DEFAULT 1,
  `cup_validade_inicio` datetime DEFAULT NULL,
  `cup_validade_fim` datetime DEFAULT NULL,
  `cup_criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `fun_codigo` int(11) NOT NULL,
  `fun_nome` varchar(255) NOT NULL,
  `fun_email` varchar(255) NOT NULL,
  `fun_telefone` varchar(15) NOT NULL,
  `fun_data_contratacao` date NOT NULL,
  `fun_cnh` varchar(20) DEFAULT NULL,
  `fun_ativo` tinyint(1) NOT NULL DEFAULT 1,
  `fun_senha` varchar(255) NOT NULL,
  `fun_cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`fun_codigo`, `fun_nome`, `fun_email`, `fun_telefone`, `fun_data_contratacao`, `fun_cnh`, `fun_ativo`, `fun_senha`, `fun_cargo`) VALUES
(1, 'cleiton', 'cleiton@email.com', '(18)981971147', '0000-00-00', NULL, 1, '123456', 'Atendente'),
(2, 'Luís Felipe Giacomelli Rodrigues', 'lfgiacomellirodrigues@gmail.com', '', '0000-00-00', NULL, 1, '123456', 'Mototaxista');

-- --------------------------------------------------------

--
-- Estrutura para tabela `motocicletas`
--

CREATE TABLE `motocicletas` (
  `mot_codigo` int(11) NOT NULL,
  `mot_modelo` varchar(100) NOT NULL,
  `mot_placa` varchar(10) NOT NULL,
  `mot_ano` int(11) NOT NULL,
  `fun_codigo` int(11) NOT NULL,
  `mot_cor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `motocicletas`
--

INSERT INTO `motocicletas` (`mot_codigo`, `mot_modelo`, `mot_placa`, `mot_ano`, `fun_codigo`, `mot_cor`) VALUES
(1, 'CB300F', '123k211', 2025, 2, 'Preto');

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes`
--

CREATE TABLE `solicitacoes` (
  `sol_codigo` int(11) NOT NULL,
  `sol_origem` varchar(255) NOT NULL,
  `sol_destino` varchar(255) NOT NULL,
  `sol_valor` decimal(10,2) NOT NULL,
  `sol_formapagamento` varchar(50) NOT NULL,
  `sol_distancia` decimal(10,2) NOT NULL,
  `sol_data` datetime NOT NULL,
  `usu_codigo` int(11) NOT NULL,
  `sol_largura` decimal(10,2) DEFAULT NULL,
  `sol_comprimento` decimal(10,2) DEFAULT NULL,
  `sol_peso` decimal(10,2) DEFAULT NULL,
  `sol_status` varchar(50) NOT NULL,
  `sol_servico` varchar(50) NOT NULL,
  `sol_observacoes` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `solicitacoes`
--

INSERT INTO `solicitacoes` (`sol_codigo`, `sol_origem`, `sol_destino`, `sol_valor`, `sol_formapagamento`, `sol_distancia`, `sol_data`, `usu_codigo`, `sol_largura`, `sol_comprimento`, `sol_peso`, `sol_status`, `sol_servico`, `sol_observacoes`) VALUES
(13, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'pix', 3.25, '2025-05-03 23:40:37', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(14, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 4.76, 'pix', 1.27, '2025-05-03 23:41:31', 1, 12.00, 1.00, 1.00, 'recusada', 'entrega', 'Pizza'),
(15, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'dinheiro', 1.27, '2025-05-04 00:25:34', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(16, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'pix', 1.27, '2025-05-04 15:04:25', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(17, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'pix', 1.27, '2025-05-04 15:05:58', 1, NULL, NULL, NULL, 'recusada', 'mototaxi', ''),
(18, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 4.76, 'dinheiro', 1.27, '2025-05-04 15:10:03', 1, 12.00, 12.00, 1.00, 'aceita', 'entrega', 'Caixa de pizza'),
(19, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'cartao', 1.27, '2025-05-04 15:13:02', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(20, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'cartao', 1.27, '2025-05-04 15:15:25', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(21, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'pix', 3.25, '2025-05-04 15:17:09', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(22, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.77, 'dinheiro', 1.29, '2025-05-04 17:11:09', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(23, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'cartao', 3.25, '2025-05-04 18:34:21', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(24, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'dinheiro', 1.27, '2025-05-04 18:36:34', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(25, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'cartao', 3.25, '2025-05-04 22:09:32', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(26, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.77, 'dinheiro', 1.29, '2025-05-04 23:37:42', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(27, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'dinheiro', 1.27, '2025-05-04 23:56:00', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(28, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.77, 'cartao', 1.29, '2025-05-05 00:07:29', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', ''),
(29, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'cartao', 1.27, '2025-05-05 00:17:55', 1, NULL, NULL, NULL, 'aceita', 'mototaxi', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usu_codigo` int(11) NOT NULL,
  `usu_nome` varchar(255) NOT NULL,
  `usu_email` varchar(255) NOT NULL,
  `usu_senha` varchar(255) NOT NULL,
  `usu_telefone` varchar(15) NOT NULL,
  `usu_ativo` tinyint(1) DEFAULT NULL,
  `usu_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `usu_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`usu_codigo`, `usu_nome`, `usu_email`, `usu_senha`, `usu_telefone`, `usu_ativo`, `usu_created_at`, `usu_updated_at`) VALUES
(1, 'Luís Felipe Giacomelli Rodrigues', 'lfgiacomellirodrigues@gmail.com', '123456', '(18) 98197-1147', 1, '2025-05-01 19:16:18', '2025-05-05 02:22:00'),
(2, 'ana paula de oliveira giacomelli', 'paulagiacomelli28@hotmail.com', '123456', '(18) 98197-1147', 0, '2025-05-05 07:26:50', '2025-05-05 03:24:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `viagens`
--

CREATE TABLE `viagens` (
  `via_codigo` int(11) NOT NULL,
  `fun_codigo` int(11) NOT NULL,
  `sol_codigo` int(11) NOT NULL,
  `usu_codigo` int(11) NOT NULL,
  `via_origem` varchar(255) NOT NULL,
  `via_destino` varchar(255) NOT NULL,
  `via_valor` decimal(10,2) NOT NULL,
  `via_formapagamento` varchar(50) NOT NULL,
  `via_data` datetime NOT NULL,
  `via_servico` varchar(50) NOT NULL,
  `via_status` varchar(50) NOT NULL,
  `via_observacoes` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `viagens`
--

INSERT INTO `viagens` (`via_codigo`, `fun_codigo`, `sol_codigo`, `usu_codigo`, `via_origem`, `via_destino`, `via_valor`, `via_formapagamento`, `via_data`, `via_servico`, `via_status`, `via_observacoes`) VALUES
(2, 2, 13, 1, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'pix', '2025-05-03 23:40:54', 'mototaxi', 'finalizada', NULL),
(3, 2, 15, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'dinheiro', '2025-05-04 00:25:42', 'mototaxi', 'finalizada', NULL),
(4, 2, 16, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'pix', '2025-05-04 15:05:24', 'mototaxi', 'finalizada', NULL),
(5, 2, 18, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 4.76, 'dinheiro', '2025-05-04 15:10:19', 'entrega', 'finalizada', NULL),
(6, 2, 19, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'cartao', '2025-05-04 15:13:06', 'mototaxi', 'finalizada', NULL),
(7, 2, 20, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'cartao', '2025-05-04 15:15:31', 'mototaxi', 'finalizada', NULL),
(8, 2, 21, 1, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'pix', '2025-05-04 15:17:17', 'mototaxi', 'finalizada', NULL),
(9, 2, 22, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.77, 'dinheiro', '2025-05-04 17:11:23', 'mototaxi', 'finalizada', NULL),
(10, 2, 23, 1, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'cartao', '2025-05-04 18:35:56', 'mototaxi', 'finalizada', NULL),
(11, 2, 24, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'dinheiro', '2025-05-04 18:36:50', 'mototaxi', 'finalizada', NULL),
(12, 2, 25, 1, 'Avenida Tiradentes, 1110', 'Rua Paulo Sérgio Righetti, 45', 6.95, 'cartao', '2025-05-04 22:09:59', 'mototaxi', 'finalizada', NULL),
(13, 2, 26, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.77, 'dinheiro', '2025-05-04 23:39:32', 'mototaxi', 'finalizada', NULL),
(14, 2, 27, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'dinheiro', '2025-05-05 00:06:30', 'mototaxi', 'finalizada', NULL),
(15, 2, 28, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.77, 'cartao', '2025-05-05 00:07:42', 'mototaxi', 'finalizada', NULL),
(16, 2, 29, 1, 'Avenida Tiradentes, 1110', 'Rua José Egea Scoriza, 20', 5.76, 'cartao', '2025-05-05 00:29:55', 'mototaxi', 'finalizada', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`anu_codigo`);

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`ava_codigo`),
  ADD KEY `usu_codigo` (`usu_codigo`),
  ADD KEY `via_codigo` (`via_codigo`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`cup_codigo`),
  ADD UNIQUE KEY `cup_codigo_id` (`cup_codigo_id`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`fun_codigo`),
  ADD UNIQUE KEY `fun_email` (`fun_email`);

--
-- Índices de tabela `motocicletas`
--
ALTER TABLE `motocicletas`
  ADD PRIMARY KEY (`mot_codigo`),
  ADD UNIQUE KEY `mot_placa` (`mot_placa`),
  ADD KEY `fun_codigo` (`fun_codigo`);

--
-- Índices de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD PRIMARY KEY (`sol_codigo`),
  ADD KEY `usu_codigo` (`usu_codigo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usu_codigo`),
  ADD UNIQUE KEY `usu_email` (`usu_email`);

--
-- Índices de tabela `viagens`
--
ALTER TABLE `viagens`
  ADD PRIMARY KEY (`via_codigo`),
  ADD KEY `fun_codigo` (`fun_codigo`),
  ADD KEY `sol_codigo` (`sol_codigo`),
  ADD KEY `usu_codigo` (`usu_codigo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `anu_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `ava_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `cup_codigo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `fun_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `motocicletas`
--
ALTER TABLE `motocicletas`
  MODIFY `mot_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  MODIFY `sol_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usu_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `viagens`
--
ALTER TABLE `viagens`
  MODIFY `via_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`usu_codigo`) REFERENCES `usuarios` (`usu_codigo`),
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`via_codigo`) REFERENCES `viagens` (`via_codigo`);

--
-- Restrições para tabelas `motocicletas`
--
ALTER TABLE `motocicletas`
  ADD CONSTRAINT `motocicletas_ibfk_1` FOREIGN KEY (`fun_codigo`) REFERENCES `funcionarios` (`fun_codigo`);

--
-- Restrições para tabelas `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD CONSTRAINT `solicitacoes_ibfk_1` FOREIGN KEY (`usu_codigo`) REFERENCES `usuarios` (`usu_codigo`);

--
-- Restrições para tabelas `viagens`
--
ALTER TABLE `viagens`
  ADD CONSTRAINT `viagens_ibfk_1` FOREIGN KEY (`fun_codigo`) REFERENCES `funcionarios` (`fun_codigo`),
  ADD CONSTRAINT `viagens_ibfk_2` FOREIGN KEY (`sol_codigo`) REFERENCES `solicitacoes` (`sol_codigo`),
  ADD CONSTRAINT `viagens_ibfk_3` FOREIGN KEY (`usu_codigo`) REFERENCES `usuarios` (`usu_codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
