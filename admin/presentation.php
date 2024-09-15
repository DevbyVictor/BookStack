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
    <title>Conheça o Bookstack</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            position: relative;
            background: url('img/background-presentation.jpeg') no-repeat center center;
            background-size: cover;
            color: #fff;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .hero h1 {
            font-family: Arial, sans-serif;
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .hero p {
            font-family: Arial, sans-serif;
            font-size: 1.25rem;
            line-height: 1.5;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .section {
            padding: 60px 0;
        }

        .section-title {
            margin-bottom: 40px;
            font-size: 2rem;
            font-weight: bold;
        }

        .hero-content {
            max-width: 800px;
            margin: auto;
        }

        /* Adicionar cor de fundo leve às seções */
        .section {
            background-color: var(--bg-light);
            padding: 60px 0;
        }

        /* Estilo para as seções de destaque */
        .bg-light {
            background-color: #f5f5f5;
            /* Fundo cinza claro */
            padding: 40px 0;
        }

        /* Atualizar botões */
        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        /* Atualizar links e destaque */
        a {
            color: #007bff;
        }

        a:hover {
            color: #0056b3;
        }

        .highlight {
            color: #ffcc00;
            /* Amarelo mais escuro e legível */
            font-weight: bold;
        }

        .section-title span {
            color: #ffcc00;
            /* Amarelo mais escuro e legível */
        }

        .testimonial-item p {
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .testimonial-item p.fw-bold {
            color: #ffcc00;
            /* Amarelo mais escuro e legível */
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
    </style>
</head>

<body>
    <!-- Seção Hero -->
    <header id="home" class="hero">
        <div>
            <h1>Bem-vindo ao <span class="highlight">Bookstack!</span></h1>
            <p>Seu assistente ideal para encontrar e gerenciar todos os livros disponíveis em sua escola!</p>
            <a href="#contact" class="btn btn-primary mt-3">Fazer Login</a>
        </div>
    </header>

    <!-- Seção O que é o Bookstack? -->
    <section id="about" class="section">
        <div class="container">
            <h2 class="section-title text-center">O que é o <span class="highlight">Bookstack?</span></h2>
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <img src="img/logo2.png" alt="O que é o Bookstack" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <p class="lead">O Bookstack é um sistema inovador de gestão de bibliotecas desenvolvido para
                        facilitar o gerenciamento de livros e usuários com eficiência e praticidade.</p>
                    <p>Com o Bookstack, você pode:</p>
                    <ul>
                        <li><i class="bi bi-book"></i> Catalogar e organizar livros de forma rápida e fácil.</li>
                        <li><i class="bi bi-person"></i> Gerenciar contas de usuários com informações detalhadas.</li>
                        <li><i class="bi bi-calendar-check"></i> Realizar e acompanhar empréstimos de forma eficiente.
                        </li>
                        <li><i class="bi bi-bar-chart"></i> Gerar relatórios e estatísticas sobre a operação da
                            biblioteca.</li>
                        <li><i class="bi bi-bell"></i> Facilitar a comunicação com os usuários através de notificações e
                            alertas.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Serviços -->
    <section id="services" class="section bg-light">
        <div class="container">
            <h2 class="section-title text-center">Nossos <span class="highlight">Objetivos</span></h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title highlight">Visão Geral do Sistema</h5>
                            <p class="card-text">Com o Bookstack, você tem acesso a um sistema de gestão de biblioteca
                                inovador que moderniza a administração de livros, usuários e empréstimos. Nossa
                                plataforma intuitiva facilita a catalogação, o gerenciamento e o controle, oferecendo
                                funcionalidades avançadas e relatórios detalhados para uma gestão mais eficiente e
                                organizada.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title highlight">Importância da Automação</h5>
                            <p class="card-text">A automação do Bookstack é essencial para aumentar a eficiência
                                operacional das bibliotecas. Eliminando processos manuais e repetitivos, o sistema reduz
                                erros humanos e libera tempo para atividades mais estratégicas. Relatórios detalhados e
                                buscas precisas tornam a operação da biblioteca mais ágil e adaptável às necessidades
                                dos usuários.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title highlight">Benefícios para os Usuários</h5>
                            <p class="card-text">O Bookstack melhora a experiência do usuário com uma interface amigável
                                e funcionalidades como reserva online e notificações automáticas. Acesso facilitado a
                                informações sobre livros e histórico de empréstimos torna a interação mais conveniente e
                                personalizada, oferecendo uma experiência de biblioteca superior.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Depoimentos -->
    <section id="testimonials" class="section" style="background-color: #000000;">
        <div class="container">
            <h2 class="text-center mb-4 text-white">Opiniões dos <span class="highlight">Nossos Alunos</span></h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-item text-white">
                        <p>"Ótimo serviço! Muito satisfeito com o atendimento e a facilidade para encontrar e gerenciar
                            livros de forma muito prática."</p>
                        <p class="fw-bold highlight">Rafael de Oliveira, Etec Martinho Di Ciero Itu-SP</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-item text-white">
                        <p>"Excelente qualidade e profissionalismo. O sistema realmente ajudou a organizar nossa
                            biblioteca de forma eficiente."</p>
                        <p class="fw-bold highlight">Amanda Soares, Colégio Ceunsp Salto-SP</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-item text-white">
                        <p>"Recomendo a todos! O Bookstack facilitou a gestão dos livros e o acompanhamento dos
                            empréstimos. Serviço top de linha."</p>
                        <p class="fw-bold highlight">Pedro Silveira Nunes, Colégio Divino Salvador Itu-SP</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Login -->
    <section id="contact" class="section bg-light">
        <div class="container mb-3">
            <h2 class="text-center mb-4">Já possui um <span class="highlight">cadastro?</span></h2>
            <p class="text-center mb-4">Se você já é um usuário registrado, faça login abaixo. Caso contrário, você pode
                continuar sem cadastro.</p>
            <div class="row">
                <!-- Opção para Login -->
                <div class="col-md-6 mb-4">
                    <h3 class="text-center highlight">Faça Login</h3>
                    <form>
                        <div class="mb-3">
                            <label for="login-email" class="form-label">Usuario RM</label>
                            <input type="email" class="form-control" id="login-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="login-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
                <!-- Opção para Continuar sem Cadastro -->
                <div class="col-md-6">
                    <h3 class="text-center highlight">Continuar sem Cadastro</h3>
                    <p class="text-center">Você pode continuar navegando e acessando o conteúdo do site sem se
                        registrar. Caso deseje, poderá se cadastrar mais tarde.</p>
                    <div class="text-center">
                        <a href="index.php?p=index" class="btn btn-secondary">Continuar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="bg-light py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Bookstack.</p>
            <a href="#home" class="btn btn-outline-dark mt-3">Voltar ao Topo</a>
        </div>
    </footer>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
</body>

</html>