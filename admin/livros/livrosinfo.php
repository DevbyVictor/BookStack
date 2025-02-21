<?php
session_start();
include('../includes/db_connect.php');

$baseUploadPath = 'http://localhost/bookstack/admin/controle/'; // Ajuste conforme o caminho correto no seu sistema

$usuarioLogado = isset($_SESSION['aluno_id']); // Verifica se o aluno está logado
$alunoId = $usuarioLogado ? $_SESSION['aluno_id'] : null; // Obtém o ID do aluno logado
$conn = Conectar::getInstance();

if (isset($_GET['id'])) {
    $idLivro = $_GET['id'];

    // Consulta as informações do livro
    $query = "SELECT * FROM livros WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idLivro, PDO::PARAM_INT);
    $stmt->execute();
    $livro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$livro) {
        die('Livro não encontrado.');
    }

    // Consulta os comentários relacionados ao livro
    $comentariosQuery = "SELECT c.*, a.nome AS autor_nome, a.foto AS autor_foto 
                         FROM comentarios c 
                         JOIN alunos a ON c.aluno_id = a.id 
                         WHERE c.livro_id = :livro_id 
                         ORDER BY c.data_criacao DESC";
    $comentariosStmt = $conn->prepare($comentariosQuery);
    $comentariosStmt->bindParam(':livro_id', $idLivro, PDO::PARAM_INT);
    $comentariosStmt->execute();
    $comentarios = $comentariosStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die('ID do livro não fornecido.');
}

// Processa o envio de um novo comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && $usuarioLogado) {
    $novoComentario = trim($_POST['comentario']);
    if (!empty($novoComentario)) {
        $inserirComentarioQuery = "INSERT INTO comentarios (livro_id, aluno_id, comentario) 
                                   VALUES (:livro_id, :aluno_id, :comentario)";
        $inserirComentarioStmt = $conn->prepare($inserirComentarioQuery);
        $inserirComentarioStmt->bindParam(':livro_id', $idLivro, PDO::PARAM_INT);
        $inserirComentarioStmt->bindParam(':aluno_id', $alunoId, PDO::PARAM_INT);
        $inserirComentarioStmt->bindParam(':comentario', $novoComentario, PDO::PARAM_STR);
        $inserirComentarioStmt->execute();

        // Evita o reenvio do formulário após um refresh
        header("Location: livrosinfo.php?id=$idLivro");
        exit;
    }
}

// Processa a exclusão de comentários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar_comentario']) && $usuarioLogado) {
    $comentarioId = $_POST['deletar_comentario'];

    // Confere se o comentário pertence ao usuário logado
    $verificarQuery = "SELECT id FROM comentarios WHERE id = :id AND aluno_id = :aluno_id";
    $verificarStmt = $conn->prepare($verificarQuery);
    $verificarStmt->bindParam(':id', $comentarioId, PDO::PARAM_INT);
    $verificarStmt->bindParam(':aluno_id', $alunoId, PDO::PARAM_INT);
    $verificarStmt->execute();

    if ($verificarStmt->rowCount() > 0) {
        $deletarQuery = "DELETE FROM comentarios WHERE id = :id";
        $deletarStmt = $conn->prepare($deletarQuery);
        $deletarStmt->bindParam(':id', $comentarioId, PDO::PARAM_INT);
        $deletarStmt->execute();
    }

    header("Location: livrosinfo.php?id=$idLivro");
    exit;
}

// Processa a edição de comentários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_comentario']) && $usuarioLogado) {
    $comentarioId = $_POST['editar_comentario'];
    $novoTexto = trim($_POST['texto_editado']);

    if (!empty($novoTexto)) {
        // Confere se o comentário pertence ao usuário logado
        $verificarQuery = "SELECT id FROM comentarios WHERE id = :id AND aluno_id = :aluno_id";
        $verificarStmt = $conn->prepare($verificarQuery);
        $verificarStmt->bindParam(':id', $comentarioId, PDO::PARAM_INT);
        $verificarStmt->bindParam(':aluno_id', $alunoId, PDO::PARAM_INT);
        $verificarStmt->execute();

        if ($verificarStmt->rowCount() > 0) {
            $editarQuery = "UPDATE comentarios SET comentario = :comentario WHERE id = :id";
            $editarStmt = $conn->prepare($editarQuery);
            $editarStmt->bindParam(':comentario', $novoTexto, PDO::PARAM_STR);
            $editarStmt->bindParam(':id', $comentarioId, PDO::PARAM_INT);
            $editarStmt->execute();
        }
    }

    header("Location: livrosinfo.php?id=$idLivro");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($livro['titulo']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos para a capa do livro */
        .livro-capa {
            width: 100%;
            height: 600px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Card do livro */
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Botão Voltar no topo com seta estilizada */
        .btn-voltar-top {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: transparent;
            color: #343a40;
            border: none;
            font-size: 24px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 1000;
        }

        .btn-voltar-top i {
            font-size: 24px;
            color: #343a40;
        }

        .btn-voltar-top:hover i {
            color: #555;
        }

        /* Estilos dos botões de ação */
        .btn-reservar,
        .btn-login-necessario,
        .btn-voltar {
            border-radius: 50px;
            width: 100%;
            font-weight: 600;
            padding: 12px;
            font-size: 1rem;
            margin-top: 10px;
        }

        .btn-reservar {
            background-color: #ffc107;
            border: none;
            color: #000;
            margin-right: 10px;
        }

        .btn-reservar:hover {
            background-color: #ffcd39;
            color: #000;
        }

        .btn-login-necessario {
            background-color: #6c757d;
            color: #fff;
            border: none;
            font-size: 1rem;
            padding: 12px;
            max-width: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-login-necessario:hover {
            background-color: #ffcd39;
            color: #000;
        }

        .btn-voltar {
            background-color: #f8f9fa;
            color: #343a40;
            border: 1px solid #343a40;
            font-size: 1rem;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-voltar:hover {
            background-color: #343a40;
            color: #f7d547;
        }

        /* Ajustes de responsividade */
        @media (max-width: 992px) {
            .livro-capa {
                height: 400px;
            }

            h2,
            h3 {
                font-size: 1.5rem;
            }

            p.lead {
                font-size: 1rem;
            }

            .btn-voltar-top {
                font-size: 22px;
                top: 15px;
                left: 15px;
            }
        }

        @media (max-width: 768px) {
            .livro-capa {
                height: 250px;
            }

            h2,
            h3 {
                font-size: 1.3rem;
            }

            p.lead {
                font-size: 0.9rem;
            }

            .btn-voltar-top {
                font-size: 20px;
                top: 12px;
                left: 12px;
            }

            .btn-login-necessario {
                font-size: 0.9rem;
                max-width: 180px;
                padding: 8px;
                text-align: center;
            }

            .btn-reservar,
            .btn-voltar {
                width: 48%;
                margin-top: 10px;
            }

            .card {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .livro-capa {
                height: 180px;
            }

            .btn-voltar-top {
                font-size: 18px;
                top: 10px;
                left: 10px;
            }
        }

        .comentarios-section {
            margin-top: 30px;
        }

        .comentario-item {
            border-bottom: 1px solid #ddd;
            padding: 15px;
            display: flex;
            align-items: flex-start;
        }

        .comentario-avatar {
            width: 50px;
            height: 50px;
            background-color: #ddd;
            border-radius: 50%;
            margin-right: 15px;
            overflow: hidden;
        }

        .comentario-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .comentario-conteudo {
            flex: 1;
        }

        .comentario-autor {
            font-weight: bold;
        }

        .comentario-data {
            color: #888;
            font-size: 0.9em;
        }

        .form-editar {
            display: none;
        }

       
    </style>
</head>

<body style="font-family: 'Montserrat', sans-serif;">

    <!-- Botão Voltar fixo no canto superior esquerdo com ícone de seta -->
    <button onclick="history.back()" class="btn-voltar-top">
        <i class="fas fa-arrow-left text-warning"></i>
    </button>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6 col-sm-12 mb-4">
                <div class="card shadow-sm">
                    <!-- Imagem do livro -->
                    <?php if (!empty($livro['capa'])): ?>
                        <img src="<?php echo $baseUploadPath . $livro['capa']; ?>" class="card-img-top livro-capa"
                            alt="Capa do livro">
                    <?php else: ?>
                        <img src="controle/uploads/imagem-nao-disponivel.jpg" class="card-img-top livro-capa"
                            alt="Capa não disponível">
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-7 col-md-6 col-sm-12">
                <!-- Informações do livro -->
                <h2><?php echo htmlspecialchars($livro['titulo']); ?></h2>
                <p>Por <span class="text-danger"><?php echo htmlspecialchars($livro['autor']); ?></span></p>
                <p class="lead"><?php echo htmlspecialchars($livro['sinopse']); ?></p>

                <h3>Características do Livro</h3>
                <table class="table table-striped mt-3">
                    <tbody>
                        <tr>
                            <th scope="row">Autor</th>
                            <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Instituição</th>
                            <td><?php echo htmlspecialchars($livro['instituicao']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Ano</th>
                            <td><?php echo htmlspecialchars($livro['ano']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Exemplares Disponíveis</th>
                            <td><?php echo htmlspecialchars($livro['exemplares']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Categoria</th>
                            <td><?php echo htmlspecialchars($livro['categoria']); ?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Botões de ação -->
                <div class="btn-group d-flex justify-content-between">
                    <?php if ($usuarioLogado): ?>
                        <button class="btn btn-warning btn-lg btn-reservar"
                            onclick="reservarLivro(<?php echo htmlspecialchars($livro['id']); ?>)">Reservar</button>
                    <?php else: ?>
                        <a href="../index.php?p=login" class="btn btn-login-necessario">Login para reservar</a>
                    <?php endif; ?>
                    <a href="javascript:history.back()" class="btn btn-voltar">Voltar</a>
                </div>
            </div>
        </div>
        <hr>

        <div class="comentarios-section">
            <h2>Comentários</h2>

            <!-- Comentários -->
            <?php if (!empty($comentarios)): ?>
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comentario-item">
                        <div class="comentario-avatar">
                            <img src="<?php echo $comentario['autor_foto'] ? $baseUploadPath . $comentario['autor_foto'] : 'path/to/default/avatar.png'; ?>"
                                alt="Avatar">
                        </div>
                        <div class="comentario-conteudo">
                            <span class="comentario-autor"><?php echo htmlspecialchars($comentario['autor_nome']); ?></span>
                            <span
                                class="comentario-data">(<?php echo date('d/m/Y H:i', strtotime($comentario['data_criacao'])); ?>)</span>
                            <p><?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?></p>

                            <?php if ($usuarioLogado && $comentario['aluno_id'] == $alunoId): ?>
                                <button class="btn btn-sm btn-primary editar-comentario"
                                    data-id="<?php echo $comentario['id']; ?>">Editar</button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="deletar_comentario" value="<?php echo $comentario['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
            <?php endif; ?>

            <!-- Formulário de Comentários -->
            <?php if ($usuarioLogado): ?>
                <form method="POST" class="mt-4">
                    <div class="form-group">
                        <textarea name="comentario" class="form-control" rows="3"
                            placeholder="Escreva seu comentário aqui..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning mt-4">
                    <p>Você precisa estar logado para comentar. <a href="../index.php?p=login">Faça login aqui</a>.</p>
                </div>
            <?php endif; ?>
        </div>
</body>


</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Importação do ícone Font Awesome -->
<script>
    $(document).on('click', '.editar-comentario', function () {
        const comentarioId = $(this).data('id');
        Swal.fire({
            title: 'Editar Comentário',
            input: 'textarea',
            inputValue: $(this).closest('.comentario-item').find('p').text(),
            showCancelButton: true,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: {
                        editar_comentario: comentarioId,
                        texto_editado: result.value
                    },
                    success: function () {
                        Swal.fire('Sucesso', 'Comentário atualizado!', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire('Erro', 'Não foi possível atualizar o comentário.', 'error');
                    }
                });
            }
        });
    });
    function reservarLivro(livro_id) {
        Swal.fire({
            title: 'Confirmação de Reserva',
            text: "Tem certeza que deseja reservar este livro?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, reservar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'reservar.php',
                    type: 'POST',
                    data: { livro_id: livro_id },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Reservado com sucesso!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(function () { location.reload(); }, 2000);
                        } else if (response.status === 'notificacao') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Atenção!',
                                text: response.mensagem,
                                showConfirmButton: true
                            });
                        } else if (response.status === 'punido') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Punição Aplicada!',
                                text: `Você não pode reservar livros pelos próximos ${response.horas_punicao} horas.`,
                                timer: 3000
                            });
                        } else if (response.status === 'max_exemplares') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Exemplares Esgotados!',
                                text: 'Não há mais exemplares disponíveis para reserva.',
                                timer: 2000
                            });
                        } else if (response.status === 'already_reserved') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Já Reservado!',
                                text: 'Você já reservou este livro.',
                                timer: 2000
                            });
                        } else if (response.status === 'wait_time') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Aguarde!',
                                text: `Você deve esperar ${response.horas_restantes} horas para reservar este livro novamente.`,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao reservar!',
                                text: 'Ocorreu um problema ao reservar o livro. Tente novamente.',
                                timer: 2000
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Houve um erro ao reservar o livro. Tente novamente.',
                            timer: 2000
                        });
                    }
                });
            }
        });
    }
</script>

</body>

</html>