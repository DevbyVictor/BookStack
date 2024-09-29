<?php
include_once '../includes/db_connect.php';
@session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $subtitulo = $_POST['subtitulo'];
    $sinopse = $_POST['sinopse'];
    $autor = $_POST['autor'];
    $instituicao = $_POST['instituicao'];
    $ano = $_POST['ano'];
    $isbn = $_POST['isbn'];
    $categoria = $_POST['categoria'];
    $exemplares = $_POST['exemplares'];
    $condicao = $_POST['condicao'];

    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['capa']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["capa"]["name"]);
        move_uploaded_file($_FILES["capa"]["tmp_name"], $target_file);
    } else {
        $target_file = null;
    }

    try {
        $conn = Conectar::getInstance();
        $query = "INSERT INTO livros (titulo, subtitulo, sinopse, autor, instituicao, ano, isbn, capa, categoria, exemplares, condicao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$titulo, $subtitulo, $sinopse, $autor, $instituicao, $ano, $isbn, $target_file, $categoria, $exemplares, $condicao]);
        $_SESSION['message'] = "Novo livro cadastrado com sucesso!";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro ao cadastrar o livro: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    header("Location: cad_livros.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Cadastro de Livros</title>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Cadastro de Livros</h1>

        <!-- Formulário de Cadastro -->
        <form action="cad_livros.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                    <div class="invalid-feedback">Por favor, insira o título.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="subtitulo" class="form-label">Subtítulo</label>
                    <input type="text" class="form-control" id="subtitulo" name="subtitulo" required>
                    <div class="invalid-feedback">Por favor, insira o subtítulo.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="sinopse" class="form-label">Sinopse</label>
                    <textarea class="form-control" id="sinopse" name="sinopse" rows="3" required></textarea>
                    <div class="invalid-feedback">Por favor, insira a sinopse.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="autor" class="form-label">Autor</label>
                    <input type="text" class="form-control" id="autor" name="autor" required>
                    <div class="invalid-feedback">Por favor, insira o autor.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="instituicao" class="form-label">Instituição</label>
                    <input type="text" class="form-control" id="instituicao" name="instituicao" required>
                    <div class="invalid-feedback">Por favor, insira a instituição.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="ano" class="form-label">Ano de Publicação</label>
                    <input type="number" class="form-control" id="ano" name="ano" required>
                    <div class="invalid-feedback">Por favor, insira o ano de publicação.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" required>
                    <div class="invalid-feedback">Por favor, insira o ISBN.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="exemplares" class="form-label">Número de Exemplares</label>
                    <input type="number" class="form-control" id="exemplares" name="exemplares" required>
                    <div class="invalid-feedback">Por favor, insira o número de exemplares.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="condicao">Condição</label>
                    <select class="form-control" id="condicao" name="condicao">
                        <option value="novo">Novo</option>
                        <option value="usado">Usado</option>
                    </select>
                    <div class="invalid-feedback">Por favor, selecione a condição do livro.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="capa">Capa do Livro</label>
                    <input type="file" class="form-control-file" id="capa" name="capa">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="mb-3">
                        <label for="categoria">Categoria</label>
                        <select class="form-control" id="categoria" name="categoria">
                            <option value="Romance">Romance</option>
                            <option value="Drama">Drama</option>
                            <option value="Novela">Novela</option>
                            <option value="Conto">Conto</option>
                            <option value="Crônica">Crônica</option>
                            <option value="Poesia">Poesia</option>
                            <option value="Ficção">Ficção</option>
                            <option value="Aventura">Aventura</option>
                            <option value="HQ">HQ</option>
                            <option value="Terror">Terror</option>
                        </select>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Cadastrar Livro</button>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    
    <!-- Script de Validação -->
    <script>
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Alerta de Cadastro com SweetAlert2
        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                title: '<?php echo $_SESSION['message_type'] == "success" ? "Sucesso!" : "Erro!"; ?>',
                text: '<?php echo $_SESSION['message']; ?>',
                icon: '<?php echo $_SESSION['message_type']; ?>',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?> // Limpar sessão após exibição
                window.location.href = 'admin_dashboard.php?p=cad_livros'; // Redirecionar após alerta
            });
        <?php endif; ?>
    </script>
</body>
</html>
