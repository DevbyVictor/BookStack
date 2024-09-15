<?php
include ('includes/db_connect.php');

// Conectar ao banco de dados
$conn = Conectar::getInstance();

// Consulta para obter os livros favoritados
$query = "SELECT * FROM livros WHERE favoritado = TRUE";
$stmt = $conn->query($query);
$livrosFavoritados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define o caminho base para os uploads
$baseUploadPath = 'http://localhost/bookstack2/admin/controle/';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
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
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Meus Favoritos</h2>
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
                <p class="no-favorites">Você ainda não favoritou nenhum livro.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.icon-favoritar').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault(); // Evita o comportamento padrão do link

                    var button = this;
                    var livroId = button.getAttribute('data-id');
                    var isFavoritado = button.getAttribute('data-favoritado') === '1';

                    if (confirm('Você tem certeza que deseja ' + (isFavoritado ? 'desfavoritar' : 'favoritar') + ' este livro?')) {
                        fetch('controle/favoritar.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${encodeURIComponent(livroId)}&action=${isFavoritado ? 'unfavorite' : 'favorite'}`
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    if (isFavoritado) {
                                        // Remove o card do livro da página
                                        var card = document.getElementById('livro-' + livroId);
                                        card.parentNode.removeChild(card);
                                    } else {
                                        // Atualiza o estado do botão
                                        button.setAttribute('data-favoritado', '1');
                                        button.querySelector('.icon-heart').classList.add('favoritado');
                                    }
                                } else {
                                    alert('Erro ao atualizar a favoritação. Tente novamente.');
                                }
                            })
                            .catch(error => {
                                console.error('Erro:', error);
                            });
                    }
                });
            });
        });

    </script>
</body>

</html>