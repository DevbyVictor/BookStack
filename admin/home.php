<?php
@session_start();
include('includes/db_connect.php');

// Verifica se o aluno está logado
$aluno_id = isset($_SESSION['aluno_id']) ? $_SESSION['aluno_id'] : null;

// Cria uma instância da classe Conectar
$conn = Conectar::getInstance();

// Definir o número de livros por página
$livrosPorPagina = 12;
$paginaAtual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

// Calcular o offset
$offset = ($paginaAtual - 1) * $livrosPorPagina;

// Verifica se o modo aleatório foi ativado
$modoAleatorio = isset($_GET['random']) && $_GET['random'] === '1';

// Consulta para obter o total de livros
$queryTotal = "SELECT COUNT(*) as total FROM livros";
$stmtTotal = $conn->query($queryTotal);
$totalLivros = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular o número total de páginas
$totalPaginas = ceil($totalLivros / $livrosPorPagina);

// Verifica se existe uma categoria sendo filtrada
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

if ($modoAleatorio) {
    // Modo aleatório
    $query = "
        SELECT l.*, 
               CASE WHEN f.aluno_id IS NOT NULL THEN 1 ELSE 0 END AS favoritado 
        FROM livros l
        LEFT JOIN favoritos f ON l.id = f.livro_id AND f.aluno_id = :aluno_id
        ORDER BY RAND()
        LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
} elseif ($categoria) {
    // Filtrar por categoria
    $query = "
        SELECT l.*, 
               CASE WHEN f.aluno_id IS NOT NULL THEN 1 ELSE 0 END AS favoritado 
        FROM livros l
        LEFT JOIN favoritos f ON l.id = f.livro_id AND f.aluno_id = :aluno_id
        WHERE l.categoria = :categoria
        LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
} else {
    // Exibição padrão com paginação
    $query = "
        SELECT l.*, 
               CASE WHEN f.aluno_id IS NOT NULL THEN 1 ELSE 0 END AS favoritado 
        FROM livros l
        LEFT JOIN favoritos f ON l.id = f.livro_id AND f.aluno_id = :aluno_id
        LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
}

$stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
$stmt->bindParam(':limit', $livrosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define o caminho base para os uploads
$baseUploadPath = 'http://localhost/bookstack/admin/controle/';
?>
<link rel="stylesheet" href="css/paginationcss.css">
<link rel="preload" href="css/estilo_home.css" as="style" onload="this.rel='stylesheet'">
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" as="style"
    onload="this.rel='stylesheet'">

<!-- CAROUSEL RESPONSIVO -->
<div class="container-fluid p-0">
    <div id="carouselMain" class="carousel slide carousel-dark" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselMain" data-bs-slide-to="0" class="active bg-dark"></button>
            <button type="button" data-bs-target="#carouselMain" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselMain" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="img/banner4.png" class="img-fluid w-100" id="carousel-img" alt="Banner 1">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="img/banner5.png" class="img-fluid w-100" id="carousel-img" alt="Banner 2">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="img/banner6.png" class="img-fluid w-100" id="carousel-img" alt="Banner 3">
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
</div>


<!-- LIVROS RECOMENDADOS -->
<div class="container mt-5">
    <div class="section-header text-center mb-4">
        <h2 class="section-title">
            <i class="fas fa-book-reader me-2"></i>Livros Recomendados
        </h2>
        <p class="section-subtitle">Explore algumas sugestões da nossa biblioteca!</p>
        <hr class="section-divider">
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4">
        <?php
        // Limita a exibição a 6 livros
        $livrosExibidos = array_slice($livros, 0, 6);

        foreach ($livrosExibidos as $livro): ?>
            <div class="col">
                <div class="card h-100 shadow-sm position-relative livro-card">
                    <!-- Ícone de favorito (canto superior direito do card) -->
                    <a href="livrosfav.php?id=<?php echo $livro['id']; ?>"
                        class="favorite-icon position-absolute top-0 end-0 p-2 icon-favoritar"
                        data-id="<?php echo $livro['id']; ?>"
                        data-favoritado="<?php echo $livro['favoritado'] ? '1' : '0'; ?>">
                        <?php if ($livro['favoritado']): ?>
                            <i class="fas fa-heart text-danger icon-heart" aria-hidden="true"></i>
                        <?php else: ?>
                            <i class="far fa-heart icon-heart" aria-hidden="true"></i>
                        <?php endif; ?>
                    </a>

                    <!-- Capa do livro e link para mais informações -->
                    <a href="livros/livrosinfo.php?id=<?php echo htmlspecialchars($livro['id']); ?>">
                        <?php if (!empty($livro['capa'])): ?>
                            <img src="<?php echo $baseUploadPath . $livro['capa']; ?>" class="card-img-top livro-capa"
                                alt="Capa do livro">
                        <?php else: ?>
                            <img src="controle/uploads/imagem-nao-disponivel.jpg" class="card-img-top livro-capa"
                                alt="Capa não disponível">
                        <?php endif; ?>
                    </a>

                    <!-- Corpo do card com título e autor -->
                    <div class="card-body d-flex flex-column">
                        <a href="livros/livrosinfo.php?id=<?php echo htmlspecialchars($livro['id']); ?>"
                            class="text-decoration-none text-dark">
                            <h5 class="card-title text-center"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                            <p class="card-text text-muted text-center"><?php echo htmlspecialchars($livro['autor']); ?></p>
                        </a>

                        <!-- Botão de reserva -->
                        <div class="mt-auto text-center">
                            <?php
                            if (isset($_SESSION['aluno_id'])) {
                                $query = "SELECT * FROM reservas WHERE livro_id = ? AND aluno_id = ? AND status = 'reservado'";
                                $stmt = $conn->prepare($query);
                                $stmt->execute([$livro['id'], $_SESSION['aluno_id']]);

                                if ($stmt->rowCount() > 0) {
                                    echo '<button class="btn btn-warning btn-sm mt-3" disabled>Reservado</button>';
                                } else {
                                    $query = "SELECT COUNT(*) as reservas_ativas FROM reservas WHERE livro_id = ? AND status = 'reservado'";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute([$livro['id']]);
                                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $reservas_ativas = $resultado['reservas_ativas'];

                                    if ($reservas_ativas >= $livro['exemplares']) {
                                        echo '<button class="btn btn-danger btn-sm mt-3" disabled>Exemplares Esgotados</button>';
                                    } else {
                                        echo '<a href="livros/reservar.php?id=' . $livro['id'] . '">
                                              <button class="btn btn-reservar btn-sm mt-3">Reservar</button></a>';
                                    }
                                }
                            } else {
                                echo '<a href="index.php?p=login">
                                      <button class="btn btn-reservar btn-sm mt-3">Login para Reservar</button></a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Seção de Livros Disponíveis -->
<div data-scroll="livros-disponiveis" class="container mt-5">
    <div class="section-header text-center mb-4">
        <h2 class="section-title">
            <i class="fas fa-book-open me-2"></i>Livros Disponíveis
        </h2>
        <p class="section-subtitle">Explore toda a coleção e reserve seus livros favoritos!</p>
        <hr class="section-divider">
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-4">
        <?php foreach ($livros as $livro): ?>
            <div class="col">
                <div class="card h-100 shadow-sm position-relative livro-card">
                    <!-- Ícone de favorito (canto superior direito do card) -->
                    <a href="livrosfav.php?id=<?php echo $livro['id']; ?>"
                        class="favorite-icon position-absolute top-0 end-0 p-2 icon-favoritar"
                        data-id="<?php echo $livro['id']; ?>"
                        data-favoritado="<?php echo $livro['favoritado'] ? '1' : '0'; ?>">
                        <?php if ($livro['favoritado']): ?>
                            <i class="fas fa-heart text-danger icon-heart" aria-hidden="true"></i>
                        <?php else: ?>
                            <i class="far fa-heart icon-heart" aria-hidden="true"></i>
                        <?php endif; ?>
                    </a>

                    <!-- Capa do livro e link para mais informações -->
                    <a href="livros/livrosinfo.php?id=<?php echo htmlspecialchars($livro['id']); ?>">
                        <?php if (!empty($livro['capa'])): ?>
                            <img src="<?php echo $baseUploadPath . $livro['capa']; ?>" class="card-img-top livro-capa"
                                alt="Capa do livro">
                        <?php else: ?>
                            <img src="controle/uploads/imagem-nao-disponivel.jpg" class="card-img-top livro-capa"
                                alt="Capa não disponível">
                        <?php endif; ?>
                    </a>

                    <!-- Corpo do card com título e autor -->
                    <div class="card-body d-flex flex-column">
                        <a href="livros/livrosinfo.php?id=<?php echo htmlspecialchars($livro['id']); ?>"
                            class="text-decoration-none text-dark">
                            <h5 class="card-title text-center"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                            <p class="card-text text-muted text-center"><?php echo htmlspecialchars($livro['autor']); ?></p>
                        </a>

                        <!-- Botão de reserva -->
                        <div class="mt-auto text-center">
                            <?php
                            if (isset($_SESSION['aluno_id'])) {
                                // Verificar se o aluno já reservou ou retirou este livro
                                $query = "SELECT * FROM reservas WHERE livro_id = ? AND aluno_id = ? AND status IN ('reservado', 'retirado')";
                                $stmt = $conn->prepare($query);
                                $stmt->execute([$livro['id'], $_SESSION['aluno_id']]);
                                $reserva_existente = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($reserva_existente) {
                                    echo '<button class="btn btn-reservar btn-sm mt-3" disabled>Reservado</button>';
                                } else {
                                    // Verificar o número de reservas ativas para o livro
                                    $query = "SELECT COUNT(*) as reservas_ativas FROM reservas WHERE livro_id = ? AND status = 'reservado'";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute([$livro['id']]);
                                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $reservas_ativas = $resultado['reservas_ativas'];

                                    if ($reservas_ativas >= $livro['exemplares']) {
                                        echo '<button class="btn btn-danger btn-sm mt-3" disabled>Exemplares Esgotados</button>';
                                    } else {
                                        echo '<button class="btn-reservar btn-sm mt-3" onclick="reservarLivro(' . $livro['id'] . ')">Reservar</button>';
                                    }
                                }
                            } else {
                                echo '<a href="index.php?p=login"><button class="btn btn-reservar btn-sm mt-3">Faça login para reservar</button></a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
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
                <a href="?pagina=<?php echo $paginaAtual + 1; ?>" class="next-modern">Próximo <i class="fa fa-angle-right"
                        aria-hidden="true"></i></a>
            </li>
        <?php endif; ?>
    </ul>
</div>

</div>

<style>

</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function reservarLivro(livro_id) {
        console.log('Tentativa de reservar o livro com ID:', livro_id);  // Depuração inicial

        Swal.fire({
            title: 'Confirmação de Reserva',
            text: "Tem certeza que deseja reservar este livro?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, reservar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Usuário confirmou a reserva. Enviando requisição Ajax para reservar...');  // Depuração após confirmação

                // Faz a requisição Ajax para reservar o livro
                $.ajax({
                    url: 'livros/reservar.php',
                    type: 'POST',
                    data: { livro_id: livro_id },
                    success: function (response) {
                        console.log('Resposta do servidor ao reservar:', response);  // Depuração da resposta do servidor

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Reservado com sucesso!',
                                text: 'O livro foi reservado com sucesso.',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            setTimeout(function () {
                                location.reload();  // Atualiza a página após a reserva
                            }, 2000);
                        } else if (response.status === 'punido') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Punição Aplicada!',
                                text: `Você não pode reservar livros pelas próximas 5 horas.`,
                                showConfirmButton: true
                            });
                        } else if (response.status === 'max_exemplares') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Exemplares Esgotados!',
                                text: 'Não há mais exemplares disponíveis para reserva.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else if (response.status === 'already_reserved') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Já Reservado!',
                                text: 'Você já reservou este livro.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else if (response.status === 'wait_time') {
                            let horas_restantes = response.horas_restantes; // Pegamos a propriedade correta do objeto
                            Swal.fire({
                                icon: 'warning',
                                title: 'Aguarde!',
                                text: `Você deve esperar ${horas_restantes} horas para reservar este livro novamente.`,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao reservar!',
                                text: 'Ocorreu um problema ao reservar o livro. Tente novamente.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro ao enviar requisição Ajax:', xhr.responseText);  // Depuração de erro no AJAX
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Houve um erro ao reservar o livro. Tente novamente.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            } else {
                console.log('Usuário cancelou a reserva.');  // Depuração caso o usuário cancele a reserva
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        console.log('Página carregada. Iniciando o processo de adicionar eventos aos ícones de favoritar...');  // Depuração inicial ao carregar a página

        document.querySelectorAll('.icon-favoritar').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var button = this;
                var livroId = button.getAttribute('data-id');
                var isFavoritado = button.getAttribute('data-favoritado') === '1';
                var action = isFavoritado ? 'unfavorite' : 'favorite';

                console.log(`Favoritar/Desfavoritar livro com ID: ${livroId}, Ação: ${action}`);  // Depuração ao clicar no ícone de favoritar/desfavoritar

                // Faz a requisição AJAX para favoritar/desfavoritar
                fetch('controle/favoritar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `livro_id=${encodeURIComponent(livroId)}&action=${encodeURIComponent(action)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Resposta do servidor ao favoritar/desfavoritar:', data);  // Depuração da resposta do servidor

                        if (data.status === 'success') {
                            // Atualizar visualmente o botão de favoritar/desfavoritar
                            button.setAttribute('data-favoritado', isFavoritado ? '0' : '1');
                            button.querySelector('.icon-heart').classList.toggle('fas', !isFavoritado);
                            button.querySelector('.icon-heart').classList.toggle('far', isFavoritado);
                        } else {
                            console.error('Erro ao favoritar/desfavoritar:', data.message);  // Depuração de erro ao favoritar
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ops...',
                                text: 'Faça login para favoritar livros!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao enviar requisição ao favoritar/desfavoritar:', error);  // Depuração de erro no fetch
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao favoritar/desfavoritar o livro.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    });
            });
        });
    });

    function filterByCategory(category) {
        console.log('Filtrando por categoria:', category);  // Depuração ao filtrar por categoria
        window.location.href = `?categoria=${category}`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const scrollTarget = params.get('scroll'); // Obtém o valor de 'scroll'

        if (scrollTarget) {
            const section = document.querySelector(`[data-scroll="${scrollTarget}"]`);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });


</script>