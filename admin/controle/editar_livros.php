<?php
require_once 'class/livros.php';

$livro = new Livro();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
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

    if (isset($_FILES['capa']) && $_FILES['capa']['error'] == UPLOAD_ERR_OK) {
        $capa = $_FILES['capa'];
        $caminho_capa = 'uploads/' . basename($capa['name']);
        if (move_uploaded_file($capa['tmp_name'], $caminho_capa)) {
            $capa_path = $caminho_capa;
        } else {
            $_SESSION['message'] = "Erro ao fazer upload da capa.";
            $_SESSION['message_type'] = "danger";
            header("Location: admin_dashboard.php?p=consultar_livros");
            exit;
        }
    } else {
        $livroData = $livro->getLivro($id);
        $capa_path = $livroData['capa'];
    }

    if ($livro->updateLivro($id, $titulo, $subtitulo, $sinopse, $autor, $instituicao, $ano, $isbn, $capa_path, $categoria, $exemplares, $condicao)) {
        $_SESSION['message'] = "Livro atualizado com sucesso!";
        $_SESSION['message_type'] = "success";
        header("Location: admin_dashboard.php?p=consultar_livros");
        exit;
    } else {
        $_SESSION['message'] = "Erro ao atualizar o livro.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $id = $_GET['id'];
    $livroData = $livro->getLivro($id);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <title>Editar Livro</title>
</head>

<body>
    <div class="container mt-3">
        <h1 class="text-center mb-4">Editar Livro</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="post" action="editar_livros.php" enctype="multipart/form-data" class="needs-validation"
            novalidate>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($livroData['id']); ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo"
                        value="<?php echo htmlspecialchars($livroData['titulo']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o título.</div>
                </div>
                <div class="col-md-6">
                    <label for="subtitulo" class="form-label">Subtítulo</label>
                    <input type="text" class="form-control" id="subtitulo" name="subtitulo"
                        value="<?php echo htmlspecialchars($livroData['subtitulo']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o subtítulo.</div>
                </div>
            </div>

            <div class="mb-3">
                <label for="sinopse" class="form-label">Sinopse</label>
                <textarea class="form-control" id="sinopse" name="sinopse" rows="3"
                    required><?php echo htmlspecialchars($livroData['sinopse']); ?></textarea>
                <div class="invalid-feedback">Por favor, insira a sinopse.</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="autor" class="form-label">Autor</label>
                    <input type="text" class="form-control" id="autor" name="autor"
                        value="<?php echo htmlspecialchars($livroData['autor']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o autor.</div>
                </div>
                <div class="col-md-6">
                    <label for="instituicao" class="form-label">Instituição</label>
                    <input type="text" class="form-control" id="instituicao" name="instituicao"
                        value="<?php echo htmlspecialchars($livroData['instituicao']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira a instituição.</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ano" class="form-label">Ano de Publicação</label>
                    <input type="number" class="form-control" id="ano" name="ano"
                        value="<?php echo htmlspecialchars($livroData['ano']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o ano de publicação.</div>
                </div>
                <div class="col-md-6">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn"
                        value="<?php echo htmlspecialchars($livroData['isbn']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o ISBN.</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
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
            <div class="row mb-3">
                <div class="col-md-6">
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
            <div class="row">
                <div class="col-md-6">
                    <label for="capa">Capa do Livro</label>
                    <input type="file" class="form-control-file" id="capa" name="capa">
                    <button class="btn btn-primary mt-3" type="submit">Salvar Alterações</button>
                    <a href="admin_dashboard.php?p=consultar_livros" class="btn btn-secondary mt-3">Cancelar</a>
                </div>
                <div class="col-md-6">
                    <?php if (!empty($livroData['capa'])): ?>
                        <img src="<?php echo htmlspecialchars($livroData['capa']); ?>" alt="Capa do Livro"
                            class="img-fluid img-thumbnail" style="max-height: 200px; width: auto;">
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>

</html>