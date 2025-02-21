<?php
// Define a variável de sessão quando a ação é realizada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ação do usuário (por exemplo, o envio de um formulário)
    $_SESSION['has_visited_presentation'] = true;
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apresentação - BookStack</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            /* Fundo claro geral */
            color: #111;
            overflow-x: hidden;
        }

        .hero-content {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            /* Limitar a largura máxima */
            margin: 0 auto;
            /* Centralizar conteúdo */
            gap: 30px;
            /* Espaçamento entre texto e GIF */
        }

        .hero-text {
            max-width: 600px;
            /* Limitar a largura do texto */
        }

        .hero-gif {
            flex: 1;
            /* Ocupar o espaço restante */
            max-width: 400px;
            /* Limitar a largura do GIF */
        }

        .hero-gif img {
            width: 100%;
            /* Tornar o GIF responsivo */
            border-radius: 15px;
            /* Arredondar bordas do GIF */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            /* Adicionar sombra */
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #111;
            /* Fundo preto */
            background-image:
                radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            /* Cria um padrão de pontos */
            position: relative;
            overflow: hidden;
            text-align: center;
            z-index: 1;
        }

        /* Efeito de linhas amarelas suaves */
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(45deg,
                    rgba(255, 193, 7, 0.05) 0px,
                    rgba(255, 193, 7, 0.05) 1px,
                    transparent 2px,
                    transparent 50px);
            z-index: 0;
            pointer-events: none;
            /* Não bloqueia cliques */
        }

        .hero h1 {
            font-size: 5rem;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            max-width: 900px;
            text-align: center;
            color: #fff;
            opacity: 1;
            /* Garantir que esteja visível */
            transform: translateY(0);
            /* Garantir que a posição esteja correta */
            z-index: 2;
            /* Garantir que esteja na frente */
            background-color: transparent;
            /* Remover o background temporário */
        }

        #title {
            color: #F7d547;
        }

        .hero h2 {
            font-size: 1.5rem;
            margin: 0 0 20px 0;
            padding: 0;
            line-height: 1.2;
            max-width: 900px;
            text-align: center;
            color: #fff;
            /* Cor preta */
            opacity: 1;
            /* Garantir que a opacidade esteja ativa */
            transform: translateY(0);
            /* Garantir que a posição esteja correta */
        }

        .hero .btn {
            padding: 15px 40px;
            margin-top: 20px;
            background-color: #FFC107;
            /* Botão em amarelo */
            border: none;
            border-radius: 30px;
            color: #111;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            opacity: 1;
            /* Garantir que a opacidade esteja ativa */
            transform: translateY(0);
            /* Garantir que a posição esteja correta */
            transition: background-color 0.3s ease;
            z-index: 5;
            /* Garante que o botão esteja acima de outros elementos */
            position: relative;
            /* Mantém a posição relativa ao container hero */
            pointer-events: auto;
            /* Garante que o botão receba eventos de clique */
        }

        .hero .btn:hover {
            background-color: #e0a800;
            /* Tom mais escuro de amarelo */
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            /* Garante que as formas não bloqueiem cliques */
        }

        .shape {
            position: absolute;
            background-color: #F7d547;
            /* Formas em amarelo com opacidade */
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .shape.one {
            width: 150px;
            height: 150px;
            bottom: -50px;
            left: -50px;
        }

        .shape.two {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
        }


        /* Seção de Funcionalidades de Aluno e Administrador */
        .features-section {
            padding: 80px 20px;
            background-color: #111;
            /* Cor sólida preta */
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .features-section h2 {
            font-size: 3rem;
            margin-bottom: 30px;
            color: #fff;
            position: relative;
            z-index: 2;
        }

        .features-section:before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100vw;
            height: 100%;
            z-index: 1;
        }

        .features-container {
            display: flex;
            justify-content: space-around;
            align-items: stretch;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .feature-block {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            text-align: left;
            max-width: 350px;
            flex: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #fff;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-block h3 {
            font-size: 1.8rem;
            color: #FFC107;
            margin-bottom: 15px;
        }

        .feature-block ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-block ul li {
            font-size: 1.1rem;
            margin: 8px 0;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .feature-block ul li i {
            font-size: 1.2rem;
            color: #FFC107;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .feature-block:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-block:hover ul li {
            color: #FFC107;
        }

        .feature-block:hover ul li i {
            transform: translateX(5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: #FFC107;
            margin-bottom: 15px;
            text-align: center;
        }


        /* Botão de Retorno ao Topo */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #FFC107;
            color: #111;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background-color: #e0a800;
        }

        /* Seção com Cards */
        .cards-section {
            padding: 80px 20px;
            background-color: #fff;
            /* Fundo branco */
            text-align: center;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .card {
            width: 300px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card .icon {
            font-size: 4rem;
            color: #FFC107;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 1rem;
            color: #666;
            line-height: 1.5;
        }

        /* Seção com Texto e Imagem */
        .content-section {
            padding: 80px 20px;
            background-color: #f0f0f0;
            /* Fundo claro */
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            border: none;
            /* Remove borda */
            box-shadow: none;
            /* Remove sombra */
        }

        .content-text {
            flex: 1;
            padding: 20px;
            max-width: 600px;
            opacity: 0;
            transform: translateX(-20px);
        }

        .content-text h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #111;
        }

        .content-text p {
            font-size: 1.2rem;
            line-height: 1.6;
            color: #333;
        }

        .content-image {
            flex: 1;
            max-width: 600px;
            overflow: hidden;
            border-radius: 0;
            /* Remove bordas arredondadas */
            background-color: transparent;
            /* Remove fundo */
            box-shadow: none;
            /* Remove sombra */
            opacity: 0;
            transform: translateX(20px);
        }

        .content-image img {
            width: 100%;
            height: auto;
            box-shadow: none;
            /* Remove sombra da imagem */
            border: none;
            /* Remove borda */
        }

        /* Rodapé */
        footer {
            background-color: #222;
            color: #aaa;
            text-align: center;
            padding: 30px 20px;
        }

        footer a {
            color: #FFC107;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #fff;
        }

        /* Seção de Expansão */
        .expand-section {
            padding: 80px 20px;
            background-color: #f7f7f7;
            text-align: center;
        }

        .expand-section h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
            /* Cor do título */
        }

        .expand-item {
            display: inline-block;
            width: 200px;
            height: 200px;
            margin: 20px;
            background-color: #FFC107;
            border-radius: 50%;
            transition: all 0.5s ease;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            text-align: center;
            /* Centraliza o conteúdo */
        }

        .expand-item:hover {
            background-color: #e0a800;
        }

        .expand-item i {
            font-size: 3rem;
            color: #222;
            /* Cor do ícone */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .expand-item p {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #222;
            /* Cor do texto */
            font-size: 1.2rem;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
        }

        .expand-item:hover i {
            opacity: 0;
            transform: translate(-50%, 20%);
        }

        .expand-item:hover p {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%);
        }


        /* Responsividade */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
            }

            .hero h2 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1.2rem;
            }

            .hero .btn {
                font-size: 1rem;
                padding: 12px 30px;
            }

            .content-section {
                flex-direction: column;
                text-align: center;
            }

            .content-text {
                transform: translateX(0);
                text-align: center;
            }

            .content-image {
                transform: translateX(0);
                text-align: center;
            }
        }

        /* Botão na Hero Section */
        .hero .btn-login {
            padding: 15px 40px;
            margin-top: 20px;
            margin-left: 20px;
            background-color: transparent;
            border: 2px solid #FFC107;
            border-radius: 30px;
            color: #FFC107;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            opacity: 1;
            transform: translateY(0);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .hero .btn-login:hover {
            background-color: #FFC107;
            color: #111;
        }

        /* Estilos para a Seção de Acesso */
        .access-section {
            padding: 80px 20px;
            background-color: #333;
            /* Fundo escuro */
            text-align: center;
            color: #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            margin: 40px 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Animação suave */
        }

        .access-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            /* Efeito ao passar o mouse */
        }

        .access-section h2 {
            font-size: 2.5rem;
            color: #FFC107;
            /* Título em amarelo */
            margin-bottom: 20px;
        }

        .access-section p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            color: #f5f5f5;
            /* Texto claro */
        }

        .access-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn-access {
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-access.login {
            background-color: #FFC107;
            /* Fundo amarelo */
            color: #111;
            /* Texto escuro */
        }

        .btn-access.login:hover {
            background-color: #e0a800;
            /* Tom mais escuro de amarelo ao passar o mouse */
        }

        .btn-access.continue {
            background-color: transparent;
            border: 2px solid #FFC107;
            /* Borda amarela */
            color: #FFC107;
            /* Texto amarelo */
        }

        .btn-access.continue:hover {
            background-color: #FFC107;
            /* Fundo amarelo */
            color: #111;
            /* Texto escuro */
        }
        .info-continuar {
    font-size: 12px;
    color: #555;
    display: block;
    margin-top: 5px;
    text-align: center;
}
    </style>
</head>

<body>
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 data-aos="fade-up" data-aos-delay="100">Bem-vindo ao <span id="title"
                        style="color: #FFC107;">BookStack!</span></h1>
                <h2 data-aos="fade-up" data-aos-delay="200">Seu assistente ideal para encontrar e gerenciar todos os
                    livros disponíveis em sua escola!</h2>
                <button class="btn" data-aos="fade-up" data-aos-delay="400"
                    onclick="scrollToSection('#Cards')">Conheça mais</button>
                <button class="btn" data-aos="fade-up" data-aos-delay="500"
                    onclick="scrollToSection('#Acesso')">Já possuo login</button>
            </div>
        </div>

        <!-- Formas Flutuantes para Efeito de Fundo -->
        <div class="floating-shapes">
            <div class="shape one"></div>
            <div class="shape two"></div>
        </div>
    </section>


    <!-- Seção com Cards de Destaque -->
    <section class="cards-section" id="Cards">
        <h2 data-aos="fade-up">Por que usar o <span class="highlight">BookStack?</span></h2>
        <div class="cards-container">
            <div class="card" data-aos="fade-up" data-aos-delay="100">
                <div class="icon"><i class="fas fa-rocket"></i></div>
                <h2>Gestão rápida e eficiente</h2>
                <p>Organize e gerencie o acervo de livros da biblioteca com rapidez e eficiência, permitindo uma
                    administração mais prática e ágil.</p>
            </div>
            <div class="card" data-aos="fade-up" data-aos-delay="200">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <h2>Melhor otimização do tempo</h2>
                <p>Com o BookStack, você encontra e gerencia livros com facilidade, economizando tempo valioso para
                    alunos e administradores.</p>
            </div>
            <div class="card" data-aos="fade-up" data-aos-delay="300">
                <div class="icon"><i class="fas fa-book"></i></div>
                <h2>Acesso facilitado ao catálogo</h2>
                <p>Ofereça aos usuários uma navegação simples e intuitiva pelo acervo da biblioteca, com ferramentas de
                    busca e categorização personalizadas.</p>
            </div>
        </div>
    </section>

    <!-- Seção de Funcionalidades de Aluno e Administrador -->
    <section class="features-section">
        <h2 data-aos="fade-up">Recursos do <span class="highlight">BookStack</span> para cada usuário</h2>
        <div class="features-container">
            <!-- Bloco para Aluno -->
            <div class="feature-block aluno" data-aos="fade-right">
                <div class="feature-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Para alunos</h3>
                <ul>
                    <li><i class="fas fa-book"></i> Consulta ao catálogo de livros</li>
                    <li><i class="fas fa-search"></i>Busca avançada por títulos</li>
                    <li><i class="fas fa-calendar-check"></i>Reservas de livros</li>
                    <li><i class="fas fa-history"></i>Histórico de reservas</li>
                </ul>
            </div>
            <!-- Bloco para Administrador -->
            <div class="feature-block admin" data-aos="fade-left">
                <div class="feature-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Para administradores</h3>
                <ul>
                    <li><i class="fas fa-cogs"></i>Gestão de usuários</li>
                    <li><i class="fas fa-sync-alt"></i>Controle de reservas e devoluções</li>
                    <li><i class="fas fa-chart-line"></i>Relatórios de utilização</li>
                    <li><i class="fas fa-book"></i>Cadastro e atualização de livros</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="expand-section">
        <h2>Facilidade e Acessibilidade</h2>
        <div class="expand-item">
            <i class="fas fa-users"></i> <!-- Ícone de Usuários -->
            <p>Usuários diferentes, experiência unificada</p>
        </div>
        <div class="expand-item">
            <i class="fas fa-chart-line"></i> <!-- Ícone de Gráficos -->
            <p>Relatórios em tempo real</p>
        </div>
        <div class="expand-item">
            <i class="fas fa-shield-alt"></i> <!-- Ícone de Segurança -->
            <p>Segurança de dados</p>
        </div>
    </section>


    <!-- Nova Seção com Efeitos de Fade -->
    <section class="content-section">
        <div class="content-text" data-aos="fade-right">
            <h2>Sobre o <span style="color: #ffc107;">BookStack</span></h2>
            <p>O BookStack é uma solução completa para bibliotecas, facilitando a gestão para administradores com
                controle total do acervo e reservas, enquanto oferece aos alunos uma experiência intuitiva de busca,
                consulta e reservas de livros. Uma ferramenta que une eficiência e praticidade para transformar a gestão
                e o acesso à leitura.</p>
        </div>
        <div class="content-image" data-aos="fade-left">
            <img src="img/notebbok.png" alt="BookStack">
        </div>
    </section>

    <section class="access-section" id="Acesso">
    <h2>Já possui conta?</h2>
    <p>Faça login para acessar suas reservas e gerenciar suas leituras.</p>
    <div class="access-buttons">
        <a href="index.php?p=login" class="btn-access login">Fazer login<br><span class="info-continuar">Entre e acesse suas reservas!</span></a>
        <a href="index.php?p=home" class="btn-access continue">
            Continuar sem cadastro<br><span class="info-continuar">Você pode ver nossa coleção de livros!</span>
        </a>
    </div>
</section>

    <!-- Rodapé -->
    <footer>
        <p>© 2024 BookStack. Todos os direitos reservados.</p>
        <p>
            <a href="#">Termos de Uso</a> |
            <a href="#">Política de Privacidade</a> |
            <a href="#">Contato</a>
        </p>
    </footer>

    <!-- Botão de Retorno ao Topo -->
    <button class="back-to-top" aria-label="Voltar ao topo">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/ScrollToPlugin.min.js"></script>
    <script>
        // Inicializa AOS (Animate On Scroll)
        AOS.init({
            duration: 1000,
            once: true
        });

        // Animação inicial da Hero Section com GSAP
        gsap.from(".hero h1, .hero h2, .hero p, .hero .btn", {
            duration: 1,
            y: 30, // Reduzir o movimento vertical
            opacity: 0,
            stagger: 0.2,
            onComplete: function () {
                // Ajustar z-index e opacidade após animação
                gsap.set(".hero h1, .hero h2, .hero p, .hero .btn", {
                    zIndex: 2,
                    opacity: 1 // Assegurar opacidade total
                });
            }
        });

        // Animação das formas flutuantes
        gsap.to(".shape.one", {
            duration: 10,
            x: 200,
            y: 200,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });
        gsap.to(".shape.two", {
            duration: 15,
            x: -300,
            y: -300,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });
        gsap.to(".shape.white", {
            duration: 15,
            x: 100,
            y: 50,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });

        // Função para mostrar/ocultar o botão de voltar ao topo
        window.addEventListener('scroll', () => {
            const backToTopButton = document.querySelector('.back-to-top');
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        // Função para rolar para o topo ao clicar no botão
        document.querySelector('.back-to-top').addEventListener('click', () => {
            gsap.to(window, {
                duration: 1,
                scrollTo: {
                    y: 0,
                    autoKill: true
                },
                ease: "power2.inOut"
            });
        });

        // Função para rolar suavemente até a seção especificada
        function scrollToSection(sectionId) {
            gsap.to(window, {
                duration: 1,
                scrollTo: {
                    y: sectionId,
                    offsetY: 0 // Remove qualquer offset para rolar até o topo da seção
                },
                ease: "power2.inOut"
            });
        }

    </script>
</body>

</html>