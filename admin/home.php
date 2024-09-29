<?php
include('includes/db_connect.php');

// Cria uma instância da classe Conectar
$conn = Conectar::getInstance();

// Definir o número de livros por página
$livrosPorPagina = 12; // Defina o número de livros a serem exibidos por página
$paginaAtual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1; // Página atual, padrão é 1

// Calcular o offset
$offset = ($paginaAtual - 1) * $livrosPorPagina;

// Consulta para obter o total de livros
$queryTotal = "SELECT COUNT(*) as total FROM livros";
$stmtTotal = $conn->query($queryTotal);
$totalLivros = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular o número total de páginas
$totalPaginas = ceil($totalLivros / $livrosPorPagina);

// Consulta para obter os livros da página atual
$query = "SELECT * FROM livros LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);
$stmt->bindParam(':limit', $livrosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define o caminho base para os uploads
$baseUploadPath = 'http://localhost/bookstack/admin/controle/';
?>

<link rel="stylesheet" href="css/paginationcss.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- CARROUSEL -->
<br>
<div class="container-sm">
    <div id="carouselMain" class="carousel slide carousel-dark" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselMain" data-bs-slide-to="0" class="active bg-dark"></button>
            <button type="button" data-bs-target="#carouselMain" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselMain" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="img/banner1.jpeg" class="d-none d-md-block img-fluid" id="carousel-img" alt="">
                <img src="img/banner1.jpeg" class="d-block d-md-none img-fluid" id="carousel-img" alt="">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="img/banner2.jpeg" class="d-none d-md-block img-fluid" id="carousel-img" alt="">
                <img src="img/banner2.png" class="d-block d-md-none img-fluid" id="carousel-img " alt="">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="img/banner3.jpeg" class="d-none d-md-block img-fluid" id="carousel-img" alt="">
                <img src="img/banner1.png" class="d-block d-md-none img-fluid" id="carousel-img" alt="">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselMain" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselMain" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Próximo</span>
        </button>
    </div>
    <style>
        #carousel-img {
            height: 400px;
            /* Altura menor para achatar a imagem */
            width: 100%;
            /* Largura total do container */
            object-fit: cover;
            /* Ajusta a imagem para cobrir o elemento, mantendo a proporção */
        }

        .pagination-modern {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .pagination-modern ul {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination-modern ul li {
            margin: 0 5px;
        }

        .pagination-modern ul li a,
        .pagination-modern .prev-modern,
        .pagination-modern .next-modern {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 40px;
            /* Aumenta a largura mínima dos botões */
            height: 40px;
            /* Aumenta a altura dos botões */
            border-radius: 50%;
            /* Torna os botões circulares */
            background-color: #f8f9fa;
            /* Cor de fundo padrão */
            color: #333;
            /* Cor do texto */
            border: 1px solid #ddd;
            /* Borda padrão */
            text-decoration: none;
            /* Remove a sublinhado dos links */
            font-size: 16px;
            /* Aumenta o tamanho da fonte */
            font-weight: 600;
            /* Deixa o texto em negrito */
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            /* Adiciona transição suave */
        }

        .pagination-modern ul li a:hover,
        .pagination-modern .prev-modern:hover,
        .pagination-modern .next-modern:hover {
            background-color: #f7d547;
            /* Cor de fundo ao passar o mouse */
            color: #fff;
            /* Cor do texto ao passar o mouse */
            transform: scale(1.1);
            /* Leve aumento ao passar o mouse */
        }

        .pagination-modern ul li.active-modern a {
            background-color: #f7d547;
            /* Cor de fundo para a página ativa */
            color: #fff;
            /* Cor do texto para a página ativa */
            border-color: #f7d547;
            /* Borda para a página ativa */
            font-size: 18px;
            /* Aumenta o tamanho da fonte para destacar */
            transform: scale(1.2);
            /* Aumenta o botão ativo */
        }

        .pagination-modern .prev-modern,
        .pagination-modern .next-modern {
            border-radius: 20px;
            /* Borda arredondada para os botões de navegação */
            padding: 0 15px;
            /* Espaçamento horizontal */
            font-size: 16px;
            /* Tamanho da fonte */
            font-weight: bold;
            /* Negrito */
        }

        .pagination-modern .prev-modern {
            margin-right: 10px;
            /* Espaçamento à direita */
        }

        .pagination-modern .next-modern {
            margin-left: 10px;
            /* Espaçamento à esquerda */
        }

        /* Oculta o texto dos links de navegação em telas menores */
        @media (max-width: 576px) {

            .pagination-modern .prev-modern,
            .pagination-modern .next-modern {
                font-size: 0;
                /* Esconde o texto */
                min-width: 30px;
                /* Mantém a largura mínima */
                height: 30px;
                /* Ajusta a altura */
                border-radius: 50%;
                /* Torna os botões circulares */
            }

            .pagination-modern .prev-modern::after,
            .pagination-modern .next-modern::after {
                content: '';
                /* Remove o conteúdo de texto */
            }
        }
    </style>

    <!-- LIVROS -->

    <div id="carouselMain2" class="carousel slide container-lg mt-4" data-bs-pause="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="container">
                    <div class="row row-cols-lg-6 row-cols-sm-2 row-cols-md-3 g-3">
                        <div class="col">
                            <div class="card shadow-sm">

                                <img src="img/livro1.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">

                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="?p=livros/livrosinfo">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-md btn-outline-warning"
                                                    style="border-radius: 50px;">Reservar</button>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livro2.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="?p=livros/livrosinfo">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-md btn-outline-warning"
                                                    style="border-radius: 50px;">Reservar</button>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livro3.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livro4.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livro5.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/ivro6.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="container">
                    <div class="row row-cols-lg-6 row-cols-sm-2 row-cols-md-4 g-3">
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livroex.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livroex.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livroex.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livroex.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livroex.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <img src="img/livroex.jpg" class="bd-placeholder-img card-img-top" width="100%"
                                    height="230">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-md btn-outline-warning"
                                                style="border-radius: 50px;">Reservar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pagination_rounded mt-4 d-flex justify-content-center">
        <ul>
            <li data-bs-target="#carouselMain2" data-bs-slide="prev">
                <a href="#" class="prev"> <i class="fa fa-angle-left" aria-hidden="true"></i></a>
            </li>
            <li data-bs-target="#carouselMain2" data-bs-slide-to="0">
                <a href="#"></a>
            </li>
            <li class="hidden-xs" data-bs-target="#carouselMain2" data-bs-slide-to="1">
                <a href="#"></a>
            </li>
            <li data-bs-target="#carouselMain2" data-bs-slide="next">
                <a href="#" class="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
            </li>
        </ul>
    </div>
    <!-- Livros Disponíveis -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Livros Disponíveis</h2>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-4">
            <?php foreach ($livros as $livro): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm position-relative">
                        <!-- A imagem e o corpo do card que leva para a página do livro -->
                        <a href="livros/livrosinfo.php?id=<?php echo htmlspecialchars($livro['id']); ?>">
                            <?php if (!empty($livro['capa'])): ?>
                                <img src="<?php echo $baseUploadPath . $livro['capa']; ?>" class="card-img-top livro-capa"
                                    alt="Capa do livro">
                            <?php else: ?>
                                <img src="controle/uploads/imagem-nao-disponivel.jpg" class="card-img-top livro-capa"
                                    alt="Capa não disponível">
                            <?php endif; ?>
                        </a>

                        <div class="card-body d-flex flex-column">
                            <a href="livros/livrosinfo.php?id=<?php echo htmlspecialchars($livro['id']); ?>"
                                class="text-decoration-none text-dark">
                                <h5 class="card-title"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($livro['autor']); ?></p>
                            </a>

                            <!-- Aqui está a lógica para o botão de reserva -->
                            <div class="mt-auto text-center">
                                <?php
                                if (isset($_SESSION['aluno_id'])) {
                                    $query = "SELECT * FROM reservas WHERE livro_id = ? AND aluno_id = ? AND status = 'reservado'";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute([$livro['id'], $_SESSION['aluno_id']]);

                                    if ($stmt->rowCount() > 0) {
                                        echo '<button class="btn btn-secondary btn-sm mt-3" disabled>Reservado</button>';
                                    } else {
                                        $query = "SELECT COUNT(*) as reservas_ativas FROM reservas WHERE livro_id = ? AND status = 'reservado'";
                                        $stmt = $conn->prepare($query);
                                        $stmt->execute([$livro['id']]);
                                        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $reservas_ativas = $resultado['reservas_ativas'];

                                        if ($reservas_ativas >= $livro['exemplares']) {
                                            echo '<button class="btn btn-danger btn-sm mt-3" disabled>Exemplares Máximos Atingidos</button>';
                                        } else {
                                            // Botão de reserva com Ajax
                                            echo '<button class="btn btn-primary btn-sm mt-3" onclick="reservarLivro(' . $livro['id'] . ')">Reservar</button>';
                                        }
                                    }
                                } else {
                                    echo '<button class="btn btn-primary btn-sm mt-3" disabled>Faça login para reservar</button>';
                                }
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination-modern mt-4 d-flex justify-content-center">
            <ul>
                <?php if ($paginaAtual > 1): ?>
                    <li>
                        <a href="?pagina=<?php echo $paginaAtual - 1; ?>" class="prev-modern"> <i class="fa fa-angle-left"
                                aria-hidden="true"></i> Anterior</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="<?php echo ($i == $paginaAtual) ? 'active-modern' : ''; ?>">
                        <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($paginaAtual < $totalPaginas): ?>
                    <li>
                        <a href="?pagina=<?php echo $paginaAtual + 1; ?>" class="next-modern">Próximo <i
                                class="fa fa-angle-right" aria-hidden="true"></i></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>

    <style>
        .btn-reservar {
            border-radius: 50px;
            /* Borda arredondada */
            padding: 0.5rem 1.5rem;
            /* Ajuste o padding conforme necessário */
            font-size: 0.9rem;
            /* Ajuste o tamanho da fonte conforme necessário */
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        .btn-reservar:hover {
            background-color: #0056b3;
            /* Cor de fundo ao passar o mouse */
            border-color: #0056b3;
            /* Cor da borda ao passar o mouse */
            color: #fff;
            /* Cor do texto ao passar o mouse */
        }

        .icon-heart {
            font-size: 24px;
            color: #fff;
            /* Cor padrão do coração */
            background-color: transparent;
            transition: color 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
        }

        .icon-favoritar[data-favoritado="1"] .icon-heart {
            color: #ff0000;
            /* Fundo vermelho quando favoritado */
        }

        .livro-capa {
            width: 100%;
            height: 200px;
            /* Altura menor para as capas dos livros */
            object-fit: cover;
            /* Mantém a proporção da imagem */
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            /* Garante que o card ocupe a largura da coluna */
            max-width: 200px;
            /* Define uma largura máxima para o card */
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 0.75rem;
        }

        .card-title,
        .card-text {
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            /* Impede que o texto ultrapasse o limite do card */
        }

        .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            line-height: 1.2;
            /* Ajuste para um melhor espaçamento */
        }

        .card-text {
            font-size: 0.75rem;
            color: #555;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-outline-secondary:hover {
            color: #495057;
            border-color: #495057;
        }

        .position-absolute {
            position: absolute;
        }

        .top-0 {
            top: 0;
        }

        .end-0 {
            right: 0;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .rounded-circle {
            border-radius: 50%;
        }

        .mt-auto {
            margin-top: auto;
        }

        .pagination_rounded {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination_rounded ul {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination_rounded ul li {
            margin: 0 5px;
        }

        .pagination_rounded ul li a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: gray;
            color: #4285f4;
            border: 1px solid #e0e0e0;
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            transition: background-color 0.3s, color 0.3s, border 0.3s;
        }

        .pagination_rounded ul li a:hover {
            background-color: #f7d547;
            color: #fff;
            border: 1px solid #f7d547;
        }

        .pagination_rounded ul li.active a {
            background-color: #f7d547;
            color: #fff;
            border: 1px solid #f7d547;
        }

        .pagination_rounded .prev,
        .pagination_rounded .next {
            width: 70px;
            height: 28px;
            border-radius: 35px;
            background-color: gray;
            color: black;
            line-height: 28px;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s, border 0.3s;
        }

        .pagination_rounded .prev:hover,
        .pagination_rounded .next:hover {
            background-color: #f7d547;
            color: #fff;
        }

        .visible-xs {
            display: none !important;
        }
    </style>
    <script>
        function reservarLivro(livro_id) {
            Swal.fire({
                title: 'Confirmação de Reserva',
                text: "Tem certeza que deseja reservar este livro?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, reservar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Faz a requisição Ajax para reservar o livro
                    $.ajax({
                        url: 'livros/reservar.php',
                        type: 'POST',
                        data: { livro_id: livro_id },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Reservado com sucesso!',
                                text: 'O livro foi reservado com sucesso.',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Atualizar a página ou o botão de reserva após o sucesso
                            setTimeout(function () {
                                location.reload(); // Atualiza a página após a reserva
                            }, 2000);
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Houve um erro ao reservar o livro. Tente novamente.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const icons = document.querySelectorAll('.icon-favoritar');

            icons.forEach(icon => {
                icon.addEventListener('click', (event) => {
                    event.preventDefault();
                    const url = icon.getAttribute('href');
                    const isFavoritado = icon.getAttribute('data-favoritado') === '1';
                    const newStatus = isFavoritado ? 'unfavorite' : 'favorite'; // Envia a ação correta
                    const livroId = icon.getAttribute('data-id'); // Adiciona o ID do livro

                    // Atualiza o estado visual
                    icon.setAttribute('data-favoritado', isFavoritado ? '0' : '1');
                    icon.querySelector('.icon-heart').classList.toggle('favoritado', !isFavoritado);

                    // Envia a requisição AJAX para atualizar o estado no servidor
                    fetch('controle/favoritar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${encodeURIComponent(livroId)}&action=${encodeURIComponent(newStatus)}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                console.log('Favoritação atualizada com sucesso.');
                            } else {
                                console.error('Erro ao atualizar favoritação:', data.status);
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao atualizar favoritação:', error);
                        });
                });
            });
        });

    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>