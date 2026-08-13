-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/02/2026 às 20:49
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
-- Banco de dados: `recimap`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `ranking`
--

CREATE TABLE `ranking` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `pontuacao` int(11) NOT NULL,
  `data_jogo` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ranking`
--

INSERT INTO `ranking` (`id`, `id_usuario`, `pontuacao`, `data_jogo`) VALUES
(1, 2, 15, '2025-10-28 15:16:46'),
(2, 1, 17, '2025-10-30 16:09:47'),
(3, 1, 12, '2025-10-30 16:44:49'),
(4, 1, 0, '2026-01-23 17:13:36'),
(5, 1, 0, '2026-01-26 13:41:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_avaliacoes`
--

CREATE TABLE `tab_avaliacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ponto_coleta_id` int(11) NOT NULL,
  `avaliacao` int(11) NOT NULL CHECK (`avaliacao` >= 1 and `avaliacao` <= 5),
  `comentario` text DEFAULT NULL,
  `dt_hr_avaliacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_avaliacoes`
--

INSERT INTO `tab_avaliacoes` (`id`, `usuario_id`, `ponto_coleta_id`, `avaliacao`, `comentario`, `dt_hr_avaliacao`) VALUES
(1, 1, 4, 5, 'WE', '2026-01-22 19:48:52'),
(2, 1, 4, 3, '', '2026-01-22 19:48:54'),
(3, 1, 4, 3, '', '2026-01-22 19:48:55'),
(4, 1, 4, 3, '', '2026-01-22 19:48:55'),
(5, 1, 4, 3, '', '2026-01-22 19:48:56'),
(6, 1, 4, 3, '', '2026-01-22 19:48:56'),
(7, 1, 4, 3, '', '2026-01-22 19:48:56'),
(8, 1, 4, 3, '', '2026-01-22 19:48:56'),
(9, 1, 4, 3, 'E22', '2026-01-22 19:48:57'),
(10, 1, 4, 3, '', '2026-01-22 19:48:57'),
(11, 1, 4, 3, '2E2', '2026-01-22 19:48:58'),
(12, 1, 4, 3, '2E2', '2026-01-22 19:48:59'),
(13, 1, 4, 3, 'E2E2', '2026-01-22 19:49:01'),
(14, 1, 4, 3, 'E2E', '2026-01-22 19:49:02'),
(15, 1, 4, 4, '2EE', '2026-01-22 19:49:05'),
(16, 1, 4, 1, 'E2E', '2026-01-22 19:49:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_empresas`
--

CREATE TABLE `tab_empresas` (
  `id` int(11) NOT NULL,
  `ponto_coleta` int(11) NOT NULL,
  `login` varchar(80) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_empresas`
--

INSERT INTO `tab_empresas` (`id`, `ponto_coleta`, `login`, `senha`) VALUES
(1, 1, 'org', '123'),
(2, 2, 'pev', 'pevinho123'),
(3, 3, 'urbam', '123');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_entradas_material`
--

CREATE TABLE `tab_entradas_material` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `ponto_coleta` int(11) NOT NULL,
  `material` int(11) NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `tipo_lixo` varchar(80) NOT NULL,
  `data_entrada` date NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_entradas_material`
--

INSERT INTO `tab_entradas_material` (`id`, `id_empresa`, `ponto_coleta`, `material`, `quantidade`, `tipo_lixo`, `data_entrada`, `criado_em`) VALUES
(38, 2, 2, 3, 12.00, 'arroz', '2025-12-30', '2026-01-27 17:11:01'),
(39, 2, 2, 5, 12.00, 'pao', '2026-01-27', '2026-01-27 17:14:51'),
(40, 2, 2, 3, 12.00, 'macarrao', '2026-02-02', '2026-02-02 17:07:57'),
(41, 1, 1, 3, 12.00, 'carro', '2026-02-02', '2026-02-02 17:08:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_materiais`
--

CREATE TABLE `tab_materiais` (
  `id` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_materiais`
--

INSERT INTO `tab_materiais` (`id`, `nome`) VALUES
(3, 'Eletrônico'),
(4, 'Hospitalar'),
(1, 'Orgânico'),
(5, 'Radioativo'),
(2, 'Reciclável');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_pontos_coleta`
--

CREATE TABLE `tab_pontos_coleta` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `horario_funcionamento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_pontos_coleta`
--

INSERT INTO `tab_pontos_coleta` (`id`, `nome`, `endereco`, `latitude`, `longitude`, `telefone`, `horario_funcionamento`) VALUES
(1, 'Organe-se Compostagem Urbana', 'R. Dr. Pedro Luiz de Oliveira Costa, 3000 - Jardim Limoeiro, São José dos Campos - SP, 12211-000', -23.249982, -45.950232, '(12) 982517434', 'Seg-Sex 09:00–17:00'),
(2, 'PEV - Posto de Entrega Voluntária - 31 de Março - PMSJC', 'R. Guidoval, 100 - Conj. Res. Trinta e Um de Marco, São José dos Campos - SP, 12237-130', -23.240656, -45.915685, '', 'Seg-Sáb 08:00–17:00, Dom 08:00-12:00'),
(3, 'URBAM - Estação de Tratamento de Resíduos Sólidos', 'Estr. José Augusto Teixeira, 400 - Jardim Torrao de Ouro, São José dos Campos - SP', -23.248542, -45.864873, '(12) 39449434', ''),
(4, 'Peso do Vale Compra e Retirada de Sucatas e Resíduos Industriais', 'Rua Piraquara Club, 211 - Vila Sinha, São José dos Campos - SP, 12212-630', -23.150265, -45.901694, '(12) 982849129', 'Seg-Sex 08:00-16:00, Sáb 08:00-12:00'),
(5, 'PEV - URBANOVA', 'Condomínio Chácara dos Eucaliptos, São José dos Campos - SP', -23.182144, -45.930877, '', 'Seg-Sáb 08:00-17:00, Dom 08:00-12:00'),
(6, 'REMETAIS - Reciclagem/Comércio Metais', 'Av. Pres. Juscelino Kubitschek, 8500 - Vila Tatetuba, São José dos Campos - SP, 12230-002', -23.177410, -45.848136, '(12) 39123113', 'Seg-Sex 08:00–12:00, 13:30–17:30, Sáb 08:00-12:00'),
(7, 'FAISA coleta de resíduos/reciclagem é com a gente.', 'R. Ana Paula Nunes Dutra, 67 - Campos de São José, São José dos Campos - SP, 12226-711', -23.206759, -45.814147, '(12) 988491043', 'Seg-Sex 07:00–19:00, Sáb 07:00-13:00'),
(8, 'Brasil Vale Resíduos Industriais', 'Av. João Rodolfo Castelli, 703 - Putim, São José dos Campos - SP, 12228-000', -23.239256, -45.830970, '(12) 39446075', 'Seg-Sex 08:00–17:00, Sáb 08:00-12:00'),
(9, 'Pele Reciclagem', 'Rua Pollux, N 41 - Jardim Satélite, São José dos Campos - SP, 12230-370', -23.221589, -45.888648, '', 'Seg-Sex 08:00–17:00, Sáb 08:00-12:00'),
(10, 'PEV - Posto de Entrega Voluntária - Jardim Satélite PMSJC', 'R. Estrela Dalva, 135 - Bosque dos Eucaliptos, São José dos Campos - SP, 12230-480', -23.232631, -45.894485, '', 'Seg-Sáb 08:00–17:00, Dom 08:00-12:00'),
(11, 'APARAS DO VALE', 'R. Lucélia, 963 - Chácaras Reunidas, São José dos Campos - SP, 12238-450', -23.254398, -45.928130, '(12) 39346352', 'Seg-Sex 08:00–18:00'),
(12, 'Sucatas do Vale - Gestão de Resíduos Industriais', 'R. Francisco Rosa Marquês, 261 - Res. Uniao, São José dos Campos - SP, 12239-020', -23.259129, -45.906157, '(12) 30191713', 'Seg-Sex 07:30–17:30'),
(13, 'Local de Entrega Voluntária', 'Av. Malek Assad, 725 - Jardim Santa Maria, Jacareí - SP, 12328-080', -23.284488, -45.967618, '', 'Seg-Sex 07:00–19:00, Sáb 07:00-18:00'),
(14, 'GDL RECICLAGEM', 'R. Dr. Armando Azevedo, 242 - Jardim Paraiso, Jacareí - SP, 12316-260', -23.312064, -45.938171, '(12) 997246321', 'Seg-Sex 08:00–17:00, Sáb 08:00-12:00'),
(15, 'PEV - URBANOVA', 'Condomínio Chácara dos Eucaliptos, São José dos Campos - SP', -23.184689, -45.933108, '', 'Seg-Sáb 08:00–17:00, Dom 08:00-12:00'),
(16, 'NaLata - A Solução para Pequenos Resíduos', 'R. Euclídes Miragaia, 394 - Jardim Vale Paraiso, São José dos Campos - SP, 12245-820', -23.191632, -45.888476, '(12) 920014215', 'Seg-Sex 07:30–17:30, Sáb 08:00-12:00'),
(17, 'FAISA coleta de resíduos/reciclagem é com a gente.', 'R. Ana Paula Nunes Dutra, 67 - Campos de São José, São José dos Campos - SP, 12226-711', -23.205832, -45.806766, '(12) 988491043', 'Seg-Sex 07:00–19:00, Sáb 07:00-13:00'),
(18, 'Avenida Caçambas', 'R. Guidoval, 100 - Conj. Res. Trinta e Um de Marco, São José dos Campos - SP, 12237-130', -23.236437, -45.914912, '', 'Seg-Dom 07:00-22:00'),
(19, 'PEV - Posto de Entrega Voluntária - 31 de Março - PMSJC', 'R. Noruega, 677 - Vila Nair, São José dos Campos - SP, 12231-140', -23.210565, -45.883327, '', 'Seg-Sáb 08:00–17:00, Dom 08:00-12:00'),
(20, 'URBAM - Estação de Tratamento de Resíduos Sólidos', 'Estr. José Augusto Teixeira, 400 - Jardim Torrao de Ouro, São José dos Campos - SP', -23.250533, -45.865764, '(12) 39441000', 'Seg-Sex 08:00-17:00'),
(21, 'Urbam setor coleta e manutenções e Oficina', 'Estr. José Augusto Teixeira, 200 - Jardim Torrao de Ouro, São José dos Campos - SP, 12231-590', -23.248010, -45.861301, '(12) 39449434', ''),
(22, 'Julix Comércio Coleta Resíduos Industriais', 'Res - R. José Francisco Pereira Sáles, 366 - Conj. Res. Trinta e Um de Marco, São José dos Campos - SP, 12237-091', -23.241819, -45.913389, '(12) 997121780', 'Seg-Dom 08:00-18:00'),
(23, 'lixão do du grau', 'alto da ponte', -23.177459, -45.865431, '12990129886', ''),
(26, 'Colet of lixy de eua', 'Rua Francisco Joao Leme, Vila sinha', 0.000000, 0.000000, '', ''),
(27, 'Colet of lixy de eua', 'Rua Francisco Joao Leme, Vila sinha', 0.000000, 0.000000, '', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_ponto_coleta_material`
--

CREATE TABLE `tab_ponto_coleta_material` (
  `ponto_coleta_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_ponto_coleta_material`
--

INSERT INTO `tab_ponto_coleta_material` (`ponto_coleta_id`, `material_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(26, 5),
(27, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_usuarios`
--

CREATE TABLE `tab_usuarios` (
  `id` int(11) NOT NULL,
  `apelido` varchar(80) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `token_recuperacao` varchar(255) DEFAULT NULL,
  `expira_token` datetime DEFAULT NULL,
  `nivel` enum('usuario','admin') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_usuarios`
--

INSERT INTO `tab_usuarios` (`id`, `apelido`, `email`, `senha`, `dt_criacao`, `token_recuperacao`, `expira_token`, `nivel`) VALUES
(1, 'adm', 'adm@gmail.com', '$2y$10$QkNH4rJMr5jLLndJwLvAuOo1poygNNS1IvHGQ2nDZoMcOlNgyNSUG', '2025-10-28 18:15:10', NULL, NULL, 'admin'),
(2, 'nicolas', 'nick@gmail.com', '$2y$10$L2y7FsendeoI/E7VW4xHe.QaqbI7pWp4VPh..UKhbdjiHDjpxyle2', '2025-10-28 18:15:45', NULL, NULL, 'usuario'),
(3, 'ple', 'santossilval805@gmail.com', '$2y$10$TMteW1fPm4AoxXy5vbE9BeJAPOfSlV1uXm5qGzuM1tJ.zQnvT6AXe', '2025-11-04 19:09:28', '55b62a2b370a6036a68213010bf940cda5ae4b0977b071b5a2efc13d87f02caca78e6123e88dc8df08b56c86552261b4d187', '2025-11-04 21:10:21', 'usuario');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuarios` (`id_usuario`);

--
-- Índices de tabela `tab_avaliacoes`
--
ALTER TABLE `tab_avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `ponto_coleta_id` (`ponto_coleta_id`);

--
-- Índices de tabela `tab_empresas`
--
ALTER TABLE `tab_empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_empresas` (`ponto_coleta`);

--
-- Índices de tabela `tab_entradas_material`
--
ALTER TABLE `tab_entradas_material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ponto_coleta` (`ponto_coleta`),
  ADD KEY `material` (`material`),
  ADD KEY `fk_entrada_empresa` (`id_empresa`);

--
-- Índices de tabela `tab_materiais`
--
ALTER TABLE `tab_materiais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `tab_pontos_coleta`
--
ALTER TABLE `tab_pontos_coleta`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tab_ponto_coleta_material`
--
ALTER TABLE `tab_ponto_coleta_material`
  ADD PRIMARY KEY (`ponto_coleta_id`,`material_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Índices de tabela `tab_usuarios`
--
ALTER TABLE `tab_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `apelido` (`apelido`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ranking`
--
ALTER TABLE `ranking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tab_avaliacoes`
--
ALTER TABLE `tab_avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `tab_empresas`
--
ALTER TABLE `tab_empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tab_entradas_material`
--
ALTER TABLE `tab_entradas_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `tab_materiais`
--
ALTER TABLE `tab_materiais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tab_pontos_coleta`
--
ALTER TABLE `tab_pontos_coleta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `tab_usuarios`
--
ALTER TABLE `tab_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `ranking`
--
ALTER TABLE `ranking`
  ADD CONSTRAINT `fk_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `tab_usuarios` (`id`),
  ADD CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tab_usuarios` (`id`);

--
-- Restrições para tabelas `tab_avaliacoes`
--
ALTER TABLE `tab_avaliacoes`
  ADD CONSTRAINT `tab_avaliacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `tab_usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tab_avaliacoes_ibfk_2` FOREIGN KEY (`ponto_coleta_id`) REFERENCES `tab_pontos_coleta` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tab_empresas`
--
ALTER TABLE `tab_empresas`
  ADD CONSTRAINT `fk_empresas` FOREIGN KEY (`ponto_coleta`) REFERENCES `tab_pontos_coleta` (`id`);

--
-- Restrições para tabelas `tab_entradas_material`
--
ALTER TABLE `tab_entradas_material`
  ADD CONSTRAINT `fk_entrada_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `tab_empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_entrada_ponto` FOREIGN KEY (`ponto_coleta`) REFERENCES `tab_pontos_coleta` (`id`),
  ADD CONSTRAINT `tab_entradas_material_ibfk_1` FOREIGN KEY (`ponto_coleta`) REFERENCES `tab_empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tab_entradas_material_ibfk_2` FOREIGN KEY (`material`) REFERENCES `tab_materiais` (`id`);

--
-- Restrições para tabelas `tab_ponto_coleta_material`
--
ALTER TABLE `tab_ponto_coleta_material`
  ADD CONSTRAINT `tab_ponto_coleta_material_ibfk_1` FOREIGN KEY (`ponto_coleta_id`) REFERENCES `tab_pontos_coleta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tab_ponto_coleta_material_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `tab_materiais` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
