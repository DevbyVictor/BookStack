-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/11/2024 às 22:32
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
-- Banco de dados: `bookstack`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `adminstradores`
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
-- Despejando dados para a tabela `adminstradores`
--

INSERT INTO `adminstradores` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `foto`, `status_online`) VALUES
(1, 'Victor', 'admin@chefe.com', '3decd49a6c6dce88c16a85b9a8e42b51aa36f1e2', 1, NULL, 1),
(16, 'Victor Clone', 'teste@admin.com', '88fa846e5f8aa198848be76e1abdcb7d7a42d292', 3, 'uploads_adm/WhatsApp Image 2024-02-06 at 19.49.09 (3).jpeg', 0),
(17, 'Victor o Tester', 'victor@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 2, 'uploads_adm/WhatsApp Image 2024-02-06 at 19.49.09 (3).jpeg', 0),
(18, 'Victor', 'vv@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 2, 'uploads_adm/GSX.jpg', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
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
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `rm`, `nome`, `email`, `senha`, `telefone`, `foto`) VALUES
(1, '12345', 'VV', 'victor@teste.com', '$2y$10$41noeInegEHzHCEu2057l.t2pF51S9QmRNA1vJZu7mQHQS.qhQGtC', '19282922', 'uploads/GSX.jpg'),
(2, '123455', 'Pedro ', 'pedrooliveira.silva@gmail.com', '$2y$10$7QjcXTDMOpmAbmPcAjDOVOxZIMsNRy8Lk8sBSm1HWqMSq5rURKQwa', '(11) 98882-7654', 'uploads/estudante.avif');

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `livro_id`, `aluno_id`, `comentario`, `data_criacao`) VALUES
(1, 50, 2, 'Teste 1', '2024-11-17 11:14:41'),
(3, 50, 1, 'Teste 2', '2024-11-22 18:34:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `sinopse` text NOT NULL,
  `autor` varchar(255) NOT NULL,
  `instituicao` varchar(255) NOT NULL,
  `ano` int(11) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `capa` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) NOT NULL,
  `exemplares` int(11) NOT NULL,
  `exemplares_disponiveis` int(11) NOT NULL,
  `condicao` enum('Novo','Usado') NOT NULL,
  `favoritado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `subtitulo`, `sinopse`, `autor`, `instituicao`, `ano`, `isbn`, `capa`, `categoria`, `exemplares`, `exemplares_disponiveis`, `condicao`, `favoritado`) VALUES
(50, 'ALICE NO PAÍS DAS MARAVILHAS - LEWIS CARROL', '.', 'O livro conta a história de Alice, uma menina curiosa que segue um Coelho Branco de colete e relógio, mergulhando sem pensar na sua toca. A protagonista é projetada para um novo mundo, repleto de animais e objetos antropomórficos, que falam e se comportam como seres humanos.', 'Lewis Carrol', 'Darkside', 1865, '0000004', 'uploads/Alice no País das maravilhas - Lewis Carroll.jpg', 'Aventura', 3, 1, 'Novo', 0),
(51, 'A ILHA DO TESOURO - ROBERT LOUIS  STEVENSON', '.', 'Um dia, um pirata mal-encarado aparece para se hospedar e carrega consigo um baú que logo desperta o interesse dos seus perseguidores. Quando o pirata morre, Jim é quem abre o baú e descobre um mapa para uma ilha onde está enterrado um grande tesouro.', 'Robert Louis Stevenson', 'Cassell and Company', 1883, '0000005', 'uploads/A ilha do tesouro - Robert Louis Stevenson.jpg', 'Aventura', 0, 0, 'Usado', 0),
(52, 'AVENTURAS DO DITO CARREIRO - JANNART MOUTINHO ', '.', 'Dito Carreiro é um personagem típico do sertão nordestino, conhecido por sua habilidade com os animais e sua destreza nas estradas empoeiradas que cortam a caatinga. Ele é um vaqueiro experiente, mas também um contador de histórias habilidoso, cujas narrativas têm o poder de transportar seus ouvintes para mundos distantes e emocionantes.', 'Jannart Moutinho', 'Biruta', 2007, '0000006', 'uploads/Aventuras do Dito Carreiro - Jannart Moutinho Ribeiro.jpg', 'Aventura', 2, 3, 'Novo', 0),
(53, 'A CÓLERA DO CORDEIRO - GUY HOCQUENGHEM', '.', 'Este último livro do autor, é um romance de amor e aventura. Focalizando o início da era crista, revela para o leitor os primeiros embates e contradições do cristianismo. João e Paulo, João ou Paulo foi um grande dilema da Igreja nascente. A luta entre o sonho de Joao e o realismo de Paulo mostra as cisões da nova religião, tentando impor-se a um mundo em constante crise.', 'Guy Hocquenghem', 'Editions Albin Michel', 1979, '0000007', 'uploads/A cólera do cordeiro - Guy Hocquenghem.jpg', 'Aventura', 1, 0, 'Usado', 0),
(54, 'ILUSÕES: AS AVENTURAS DE UM MESSIAS INDECISO - RICHARD BACH', '.', 'A história gira em torno de um piloto que, cansado da rotina mundana, encontra um mecânico que se apresenta como um mestre ilusionista. Esse mestre afirma ser nada menos que um messias, embora suas ações e ensinamentos contradigam essa afirmação. O piloto, inicialmente cético, começa a questionar sua própria visão de mundo à medida que testemunha os poderes aparentemente sobrenaturais do mestre e suas interações com os demais personagens, incluindo um grupo de seguidores em busca de respostas espirituais.', 'Richard Bach', 'Dell Publishing', 1977, '0000008', 'uploads/Ilusões - As Aventuras de um Messias Indeciso - Richard Bach.jpg', 'Aventura', 2, 1, 'Novo', 0),
(55, 'A CIDADELA DO CAOS - STEVE JACKSON', '.', 'Neste livro, os leitores embarcam em uma aventura épica como um herói enviado para a Cidadela do Caos, uma fortaleza governada por um feiticeiro maligno conhecido como Balthus Dire. O objetivo do jogador é derrotar Balthus Dire e libertar a terra de Kakhabad do seu domínio sombrio.', 'Steve Jackson', 'Puffin Books', 1983, '0000009', 'uploads/A Cidadela do Caos - Steve Jackson.jpg', 'Aventura', 1, 0, 'Usado', 0),
(56, 'CANTARE ESTÓRIAS - JOSÉ ALAERCIO ZAMUNER', '.', 'É uma obra que mergulha nas profundezas da imaginação, memória e emoção. Nesta coletânea, Zamuner transporta os leitores para um mundo onde a beleza é encontrada nas pequenas coisas da vida, nas experiências do cotidiano e nas paisagens da natureza.', 'José Alaercio Zamuner', 'Alcance', 2016, '0000010', 'uploads/Cantare estórias - josé Alaercio Zamuner.jpg', 'Conto', 1, 1, 'Usado', 0),
(57, 'CONTOS NOVOS - MÁRIO DE ANDRADE', '.', 'Os contos presentes nesta obra são conhecidos por sua diversidade temática, que inclui reflexões sobre a cultura brasileira, críticas sociais, explorações psicológicas e observações sobre a vida urbana. Mário de Andrade utiliza uma linguagem vívida e uma narrativa envolvente para cativar seus leitores e explorar as complexidades da condição humana.', 'Mário de Andrade', 'José Olympio', 1947, '0000011', 'uploads/Contos Novos - Mário de Andrade.jpg', 'Conto', 1, 0, 'Novo', 0),
(59, 'FELICIDADE E OUTROS CONTOS - KATHERINE MANSFIELD', '.', 'Esta coletânea inclui alguns dos contos mais aclamados de Katherine Mansfield, como \"Prelude\", \"Felicidade\", \"A Mosca\" e \"O Cesto da Piquenique\", entre outros. A escrita delicada e perspicaz de Mansfield oferece aos leitores uma visão profunda da condição humana e continua a ser apreciada por sua relevância e beleza até os dias de hoje.', 'Katherine Mansfield', 'Companhia das Letras', 1920, '0000013', 'uploads/Felicidade e outros contos - Katherine Mansfield.jpg', 'Conto', 4, 4, 'Usado', 0),
(60, 'COBRAS EM COMPOTA - ÍNDIGO', '.', 'É uma coletânea de que explora uma variedade de temas, desde questões sociais e políticas até reflexões sobre a vida cotidiana e as relações humanas.  \"Cobras em Compota\" é uma obra que desafia e encanta, convidando os leitores a mergulhar nas profundezas da experiência humana por meio da arte.', 'Índigo', 'Penalux', 2020, '0000013', 'uploads/Cobras em compota - Índigo.jpg', 'Conto', 2, 2, 'Novo', 0),
(61, 'O REFLEXO PERDIDO E OUTROS CONTOS INSENSATOS – HOFFMANN', '.', 'Narra a história de um homem que encontra um duplo sinistro e perturbador de si mesmo, entre outros contos que exploram temas como sonhos, loucura e mistério. Hoffmann é reverenciado por sua habilidade em criar atmosferas vívidas e perturbadoras, bem como por sua capacidade de mergulhar nas profundezas da psique humana.', 'Hoffmann', 'Companhia das Letras', 2017, '0000014', 'uploads/O reflexo perdido e outros contos insensatos - Hoffmann.jpg', 'Conto', 1, 1, 'Novo', 0),
(63, 'CONTOS FANTÁSTICOS DO SÉCULO XIX - ITALO CALVINO', '.', 'Nessa antologia, Calvino seleciona e apresenta uma série de contos de diversos autores do século XIX que exploram o gênero do fantástico. A obra inclui contos de autores como Edgar Allan Poe, Nathaniel Hawthorne, E.T.A. Hoffmann, Guy de Maupassant, entre outros. Esses contos muitas vezes envolvem elementos sobrenaturais, estranhos ou inexplicáveis, explorando os limites da realidade e desafiando as convenções narrativas.', 'Italo Calvino ', 'Companhia das Letras', 1978, '0000016', 'uploads/Contos fantásticos do século XIX - Italo Calvino.jpg', 'Conto', 1, 1, 'Usado', 0),
(64, 'AS TRÊS IRMÃS - ANTON TCHEKHOV', '.', 'Ela retrata a vida de três irmãs, Olga, Masha e Irina, que vivem em uma cidade provinciana russa e sonham em retornar a Moscou, onde passaram sua infância. A peça aborda temas como a busca por significado na vida, a insatisfação com as circunstâncias e a passagem do tempo. Tchekhov é conhecido por sua habilidade em retratar a condição humana com uma mistura única de humor, ironia e melancolia.', 'Anton Tchekhov', 'Global', 1900, '0000017', 'uploads/As três irmãs - Anton Tchekhov.jpg', 'Conto', 2, 2, 'Novo', 0),
(65, 'CONTOS REUNIDOS - MOACYR SCLIAR', '.', 'É uma coletânea que reúne uma seleção dos contos do renomado escritor brasileiro Moacyr Scliar. Scliar foi um autor prolífico, conhecido por suas narrativas cativantes que exploram temas como identidade, cultura judaica, imigração, medicina e sociedade brasileira.', 'Moacyr Scliar', 'Companhia das Letras', 1995, '0000018', 'uploads/Contos reunidos - Moacyr Scliar.jpg', 'Conto', 2, 2, 'Novo', 0),
(66, 'DESLOCAMENTOS - FERNANDO PIRES', '.', 'Essa coleção aborda temas como identidade, memória, amor e reflexões sobre a existência humana. Fernando Pires é conhecido pela capacidade de criar imagens vívidas que ecoam as experiências pessoais e universais dos leitores. \"Deslocamentos\" convida os leitores a uma jornada emocional e introspectiva explorando os desafios e as belezas da vida cotidiana.', 'Fernando Pires', 'Compor', 2019, '0000019', 'uploads/Deslocamentos - Fernando Pires.jpg', 'Conto', 2, 2, 'Usado', 0),
(67, 'AS CRÔNICAS MARCIANAS - RAY BRADBURY', '.', 'A obra é composta por uma série de contos interligados que exploram a colonização e a eventual evolução cultural e emocional dos humanos em Marte.\r\nEla aborda diferentes aspectos da colonização, desde a chegada inicial dos humanos ao planeta vermelho até o seu abandono eventual. Bradbury mergulha nas complexidades da natureza humana, explorando temas como solidão, saudade, esperança, medo e a relação entre a humanidade e o ambiente alienígena.', 'Ray Bradbury', 'Biblioteca Azul', 1950, '0000020', 'uploads/As Crônicas Marcianas - Ray Bradbury.jpg', 'Crônica', 2, 2, 'Usado', 0),
(69, 'PROSA E VERSO - MÁRIO QUINTANA', '.', 'Mário Quintana é conhecido por sua linguagem simples e acessível, mas ao mesmo tempo profunda e reflexiva. Em \"Prosa e Verso\", ele explora uma variedade de emoções e experiências humanas, muitas vezes com um toque de humor e ironia.', 'Mário Quintana', 'Globo', 1980, '0000021', 'uploads/Prosa e verso - Mário Quintana.jpg', 'Crônica', 1, 1, 'Usado', 0),
(70, 'CISNE DE FELTRO - PAULO MENDES CAMPOS', '.', 'É uma crônica emocionante que consegue capturar a inocência e a profundidade das relações humanas, mesmo em meio às circunstâncias mais difíceis. A habilidade de Paulo Mendes Campos em explorar temas universais de uma maneira tão tocante é uma das razões pelas quais esse conto continua a ser lido e apreciado por tantas pessoas.', 'Paulo Mendes Campos', 'Civilização Brasileira ', 2001, '0000021', 'uploads/Cisne de Feltro - Paulo Mendes Campos.png', 'Crônica', 2, 2, 'Novo', 0),
(71, 'LIVRO DOS SONETOS - PAULO BOMFIM', '.', 'Este livro é uma coleção de sonetos que abrangem uma variedade de temas, desde amor e paixão até reflexões sobre a vida e a condição humana.', 'Paulo Bomfim', 'Amaral Gurgel', 2006, '0000022', 'uploads/Livro dos Sonetos - Paulo Bomfim.jpg', 'Crônica', 2, 2, 'Novo', 0),
(72, 'CRÔNICA DA CASA ASSASSINADA - LÚCIO CARDOSO', '.', 'É uma saga familiar que explora temas de decadência, tradição, segredos e tragédia. Situado em uma fazenda isolada em Minas Gerais, o enredo gira em torno dos conflitos e relações complexas entre os membros da família Meneses.', 'Lúcio Cardoso', 'Companhia das Letras', 1959, '0000023', 'uploads/Cronica da Casa Assassinada - Lúcio Cardoso.jpg', 'Crônica', 2, 2, 'Usado', 0),
(73, 'CONTOS E CRÔNICAS - FERNANDO SABINO', '.', 'Os contos e crônicas de Sabino abordam uma ampla gama de temas, desde reflexões sobre a vida urbana até histórias que exploram as nuances das relações humanas. Sua escrita é marcada por diálogos vívidos, personagens autênticos e uma narrativa que cativa o leitor desde a primeira página.', 'Fernando Sabino', 'Nova Fronteira', 1961, '0000024', 'uploads/Contos e crônicas - Fernando Sabino.jpg', 'Crônica', 3, 3, 'Novo', 0),
(74, 'TEMPO DE DESCABELAR E OUTRAS CRÔNICAS CABELUDAS - RITA ESPESCHIT', '.', 'É uma coletânea de crônicas que abordam de forma humorística e perspicaz diversos aspectos do cotidiano, proporcionando momentos de reflexão e diversão ao leitor.\r\nRita Espeschit é uma escritora brasileira contemporânea conhecida por sua escrita leve, bem-humorada e cativante. Em \"Tempo de Descabelar e Outras Crônicas Cabeludas\", ela apresenta uma série de crônicas que exploram situações do dia a dia de forma criativa e envolvente.', 'Rita Espeschit', 'Dubolsinho', 2015, '0000025', 'uploads/Tempo de descabelar e outras crônicas cabeludas - Rita Espeschit.jpg', 'Crônica', 1, 1, 'Usado', 0),
(75, 'A BOCA NO MUNDO - FERNANDO BONASSI', '.', 'O livro retrata a vida de jovens da periferia de São Paulo, explorando suas experiências, dilemas e aspirações em meio a um contexto urbano complexo.\r\nA obra é marcada por uma linguagem direta e por abordar temas sociais relevantes, como violência, desigualdade e marginalização. Fernando Bonassi utiliza uma narrativa ágil e intensa para conduzir o leitor pelos cenários e conflitos enfrentados pelos personagens.', 'Fernando Bonassi', 'Novo Século', 1998, '0000026', 'uploads/A boca no mundo - Fernando Bonassi.jpg', 'Crônica', 1, 1, 'Usado', 0),
(76, 'DOIS AMIGOS E UM CHATO - STANISLAW PONTE PRETA', '.', 'A obra é uma leitura divertida e leve, ideal para quem aprecia o humor brasileiro e as crônicas que capturam o espírito de uma época específica. Stanislaw Ponte Preta é considerado um dos grandes mestres do humor na literatura brasileira, e \"Dois Amigos e um Chato\" é um exemplo clássico de seu estilo irreverente e perspicaz.', 'Stanislaw Ponte Preta', 'Moderna Literatura', 1960, '0000027', 'uploads/Dois amigos e um chato - Stanislaw Ponte Preta.jpg', 'Crônica', 4, 4, 'Novo', 0),
(79, 'OS CAVALINHOS DE PLATIPLANTO - JOSÉ J. VEIGA', '.', 'No enredo, acompanhamos as aventuras de um menino chamado Fábio e seu amigo Mauro, que descobrem uma máquina misteriosa que produz pequenos cavalinhos de brinquedo. Esses cavalinhos, feitos de um material chamado \"platiplanto\", possuem propriedades mágicas e acabam levando Fábio e Mauro a uma série de situações inusitadas e emocionantes.', 'JOSÉ J. VEIGA', 'Ática ', 1979, '012', 'uploads/Os Cavalinhos de Platiplanto - José J. Veiga.jpg', 'Conto', 1, 1, 'Novo', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `data_devolucao` date NOT NULL,
  `data_retirada` datetime DEFAULT NULL,
  `status` enum('reservado','devolvido','atrasado','cancelado','retirado') DEFAULT 'reservado',
  `data_cancelamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `adminstradores`
--
ALTER TABLE `adminstradores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `livro_id` (`livro_id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `livro_id` (`livro_id`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `livro_id` (`livro_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `adminstradores`
--
ALTER TABLE `adminstradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`);

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
