-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08-Set-2024 às 12:46
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bookstack`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `adminstradores`
--

CREATE TABLE `adminstradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(100) DEFAULT NULL,
  `nivel_acesso` int(11) DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `status_online` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `adminstradores`
--

INSERT INTO `adminstradores` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `foto`, `status_online`) VALUES
(1, 'Victor', 'admin@chefe.com', '3decd49a6c6dce88c16a85b9a8e42b51aa36f1e2', 1, NULL, 1),
(16, 'Victor Clone', 'teste@admin.com', '88fa846e5f8aa198848be76e1abdcb7d7a42d292', 3, 'uploads_adm/WhatsApp Image 2024-02-06 at 19.49.09 (3).jpeg', 0),
(17, 'Victor o Tester', 'victortester@gmail.com', '88fa846e5f8aa198848be76e1abdcb7d7a42d292', 2, 'uploads_adm/WhatsApp Image 2024-02-06 at 19.49.09 (3).jpeg', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `rm` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `alunos`
--

INSERT INTO `alunos` (`id`, `rm`, `nome`, `email`, `senha`, `telefone`, `foto`) VALUES
(1, '12345', 'VV', 'victor@teste.com', '$2y$10$41noeInegEHzHCEu2057l.t2pF51S9QmRNA1vJZu7mQHQS.qhQGtC', '19282922', 'uploads/GSX.jpg'),
(2, '123455', 'Pedro', 'pedrooliveira.silva@gmail.com', '$2y$10$7QjcXTDMOpmAbmPcAjDOVOxZIMsNRy8Lk8sBSm1HWqMSq5rURKQwa', '(11) 98882-7654', 'uploads/estudante.avif');

-- --------------------------------------------------------

--
-- Estrutura da tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` varchar(255) NOT NULL,
  `sinopse` text NOT NULL,
  `autor` varchar(255) NOT NULL,
  `instituicao` varchar(255) NOT NULL,
  `ano` int(11) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `capa` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) NOT NULL,
  `exemplares` int(11) NOT NULL,
  `condicao` enum('Novo','Usado') NOT NULL,
  `favoritado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `subtitulo`, `sinopse`, `autor`, `instituicao`, `ano`, `isbn`, `capa`, `categoria`, `exemplares`, `condicao`, `favoritado`) VALUES
(40, 'Principe Davi', 'O rei', 'ddddd', 'dddd', 'dddd', 2222, '12121212', 'uploads/49d48ef87b14301ad5db1693e912662a.png', 'Tecnologia', 3, 'Usado', 1),
(42, 'Teste Dois', 'Testezinho', 'ssssssssssssss', 'sssssss', 'ssssssss', 1211212, '12121212', 'uploads/GSX.jpg', 'Romance', 2, 'Novo', 0),
(43, 'Harry potter', 'eeee', 'dddddddd', 'dddddddddd', 'ddddddddd', 2223, 'ws', NULL, 'Terror', 2, 'Novo', 0),
(45, 'Testeee', 'eeee', 'eeee', 'eeee', 'eee', 3232, '3323232', 'uploads/1e3d920441ca891943f4a539a2bb2331.jpg', 'Romance', 2, 'Novo', 0),
(46, 'trhghrgrgrgsdsdsdsdsdsdsdsdsdsdsdsd', 'grgrgr3', 'gr3gr3g', 'r3gr3gr3gr3', 'grgr3gr3g', 343434, 'efefffe', 'uploads/hq9xNAan_400x400.jpg', 'Romance', 1, 'Novo', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `data_devolucao` date NOT NULL,
  `status` enum('reservado','devolvido','atrasado') DEFAULT 'reservado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `adminstradores`
--
ALTER TABLE `adminstradores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `livro_id` (`livro_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `adminstradores`
--
ALTER TABLE `adminstradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
