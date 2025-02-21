<?php
require_once '../includes/db_connect.php';

$conn = Conectar::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Atualizando dados do aluno
    $rm = $_POST['rm'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];

    try {
        $stmt = $conn->prepare("UPDATE alunos SET nome = :nome, email = :email, telefone = :telefone WHERE rm = :rm");
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);
        $stmt->bindParam(':rm', $rm, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?p=consultar_alunos");
            exit();
        } else {
            echo "Erro ao atualizar os dados do aluno.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    // Exibir formulário com dados do aluno
    $rm = $_GET['rm'];

    try {
        $stmt = $conn->prepare("SELECT * FROM alunos WHERE rm = :rm");
        $stmt->bindParam(':rm', $rm, PDO::PARAM_STR);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <title>Editar Aluno</title>
</head>

<body>
    <div class="container mt-3">
        <h1 class="text-center mb-4">Editar Aluno</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="post" action="editar_alunos.php" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="rm" value="<?php echo htmlspecialchars($aluno['rm']); ?>">

            <!-- Nome e Email -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome"
                        value="<?php echo htmlspecialchars($aluno['nome']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o nome do aluno.</div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($aluno['email']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira um email válido.</div>
                </div>
            </div>

            <!-- Telefone -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone"
                        value="<?php echo htmlspecialchars($aluno['telefone']); ?>" required>
                    <div class="invalid-feedback">Por favor, insira o telefone do aluno.</div>
                </div>
                <div class="col-md-6">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" class="form-control-file" id="foto" name="foto">
                </div>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="admin_dashboard.php?p=consultar_alunos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <script>
        // Validação do formulário
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function (form) {
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