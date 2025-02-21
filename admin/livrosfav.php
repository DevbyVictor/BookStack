<?php
// Inclui o arquivo que contém a classe de conexão
include_once 'includes/db_connect.php';
@session_start();

// Define uma variável para verificar o status de login
$usuarioLogado = isset($_SESSION['aluno_id']);

// Define o caminho base para os uploads
$baseUploadPath = 'http://localhost/bookstack/admin/controle/';

// Obtenha os favoritos se o usuário estiver logado
if ($usuarioLogado) {
    // Obtenha a instância de conexão
    $conn = Conectar::getInstance();

    // Consulta para obter os livros favoritos do aluno logado
    $query = "SELECT livros.* FROM livros INNER JOIN favoritos ON livros.id = favoritos.livro_id WHERE favoritos.aluno_id = :aluno_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':aluno_id', $_SESSION['aluno_id'], PDO::PARAM_INT);
    $stmt->execute();
    $livrosFavoritados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $livrosFavoritados = [];
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .icon-heart {
            font-size: 24px;
            color: #fff;
            background-color: transparent;
            transition: color 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
        }

        .icon-favoritar[data-favoritado="1"] .icon-heart {
            color: #ff0000;
        }

        .livro-capa {
            width: 100%;
            /* A largura da imagem vai ocupar 100% do card */
            height: 250px;
            /* Altura fixa para todas as imagens */
            object-fit: cover;
            /* Mantém a proporção e preenche o container */
            border-radius: 5px;
            /* Bordas arredondadas, opcional */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Adiciona uma leve sombra */
        }


        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            max-width: 200px;
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
        }

        .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .card-text {
            font-size: 0.75rem;
            color: #555;
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

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .error-container {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
            text-align: center;
        }

        .error-box {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .error-box h1 {
            font-size: 2rem;
            color: #495057;
        }

        .error-box p {
            color: #6c757d;
            font-size: 1rem;
        }

        .error-box a {
            color: #fff;
        }

        .btn-login {
            background-color: #0d6efd;
            border-color: #0d6efd;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-login:hover {
            background-color: #025ce2;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .error-icon {
            font-size: 4rem;
            color: #ff6b6b;
        }

        /* Remove o comportamento de colunas quando não há favoritos */
        .no-favorites {
            display: flex;
            /* Flexbox para centralizar */
            justify-content: center;
            /* Alinha horizontalmente */
            align-items: center;
            /* Alinha verticalmente */
            height: 70vh;
            /* Altura total da área */
            text-align: center;
            /* Centraliza o texto */
            margin: 0;
            /* Remove qualquer margem adicional */
            padding: 0;
            /* Remove padding extra */
        }

        /* Garantir que a largura do container seja automática */
        .no-favorites .no-reserves-box {
            max-width: 500px;
            /* Largura máxima */
            width: auto;
            /* Permite ajustar automaticamente */
            margin: 0 auto;
            /* Centraliza horizontalmente */
            padding: 20px;
            /* Espaçamento interno */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Meus Favoritos</h2>

        <?php if ($usuarioLogado): ?>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-4">
                <?php if (count($livrosFavoritados) > 0): ?>
                    <?php foreach ($livrosFavoritados as $livro): ?>
                        <div class="col" id="livro-<?php echo htmlspecialchars($livro['id']); ?>">
                            <div class="card h-100 shadow-sm position-relative">
                                <?php if (!empty($livro['capa'])): ?>
                                    <img src="<?php echo $baseUploadPath . $livro['capa']; ?>" class="card-img-top livro-capa"
                                        alt="Capa do livro">
                                <?php else: ?>
                                    <img src="controle/uploads/imagem-nao-disponivel.jpg" class="card-img-top livro-capa"
                                        alt="Capa não disponível">
                                <?php endif; ?>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <a href="#" class="btn btn-sm icon-favoritar"
                                        data-id="<?php echo htmlspecialchars($livro['id']); ?>" data-favoritado="1">
                                        <i class="fa fa-heart icon-heart"></i>
                                    </a>
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($livro['autor']); ?></p>
                                    <a href="controle/reserva.php?id=<?php echo htmlspecialchars($livro['id']); ?>"
                                        class="btn btn-primary btn-sm mt-3">Reservar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reserves-container no-favorites">
                        <div class="no-reserves-box">
                            <i class="fas fa-book-open" style="font-size: 4rem; color: #3498db;"></i>
                            <h2 class="mt-3">Nenhum livro favoritado ainda!</h2>
                            <p class="mt-2 text-muted">
                                Parece que você ainda não favoritou nenhum livro. Explore nosso acervo e adicione seus
                                favoritos!
                            </p>
                            <a href="index.php?p=home" class="btn btn-primary btn-lg mt-3 px-4">
                                <i class="fas fa-search"></i> Explorar Livros
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-center align-items-center" style="height: 70vh;">
                <div class="text-center p-4 bg-white shadow rounded" style="max-width: 500px; border: 1px solid #ddd;">
                    <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: #f1c40f;"></i>
                    <h2 class="mt-3">Ops! Você não está logado.</h2>
                    <p class="mt-2 text-muted">Para acessar seus livros favoritos, faça login na sua conta.</p>
                    <a href="index.php?p=login" class="btn btn-primary btn-lg mt-3 px-4">
                        <i class="fas fa-sign-in-alt"></i> Fazer Login
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.icon-favoritar').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    var button = this;
                    var livroId = button.getAttribute('data-id');
                    var isFavoritado = button.getAttribute('data-favoritado') === '1';
                    var action = isFavoritado ? 'unfavorite' : 'favorite';

                    // Alerta de confirmação usando SweetAlert2
                    Swal.fire({
                        title: 'Tem certeza?',
                        text: isFavoritado ? 'Você deseja desfavoritar este livro?' : 'Você deseja favoritar este livro?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
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
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: data.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });

                                        if (isFavoritado) {
                                            // Remove o livro da lista imediatamente
                                            var card = document.getElementById('livro-' + livroId);
                                            card.parentNode.removeChild(card);
                                        } else {
                                            // Atualiza o estado visual do botão de favoritar
                                            button.setAttribute('data-favoritado', '1');
                                            button.querySelector('.icon-heart').classList.add('fas');
                                            button.querySelector('.icon-heart').classList.remove('far');
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Erro',
                                            text: data.message
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Erro ao favoritar/desfavoritar:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: 'Houve um erro ao processar sua solicitação. Tente novamente.'
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>