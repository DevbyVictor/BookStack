<?php
include_once '../includes/db_connect.php';
require_once 'class/class_adm.php';

$conn = Conectar::getInstance();
$adm = new Adm();
$id = isset($_GET['id']) ? $_GET['id'] : '';
$sub_adm = [];

if ($id) {
    $query = "SELECT * FROM adminstradores WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $sub_adm = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);
    $nivel_acesso = $_POST['nivel_acesso'];
    $foto_path = $sub_adm['foto'];

    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads_adm/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $foto_path = $target_file;
        } else {
            $_SESSION['message'] = "Erro ao fazer upload da foto.";
            $_SESSION['message_type'] = "danger";
            header("Location: admin_dashboard.php?p=consultar_sub_adm");
            exit;
        }
    }

    $query = "UPDATE adminstradores SET nome = :nome, email = :email, senha = :senha, nivel_acesso = :nivel_acesso, foto = :foto WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
    $stmt->bindParam(':nivel_acesso', $nivel_acesso, PDO::PARAM_INT);
    $stmt->bindParam(':foto', $foto_path, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: admin_dashboard.php?p=consultar_sub-adm");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sub-Administrador</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Editar Sub-Administrador</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome"
                    value="<?php echo htmlspecialchars($sub_adm['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?php echo htmlspecialchars($sub_adm['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="mb-3">
                <label for="nivel_acesso" class="form-label">Nível de Acesso</label>
                <input type="number" class="form-control" id="nivel_acesso" name="nivel_acesso"
                    value="<?php echo htmlspecialchars($sub_adm['nivel_acesso']); ?>" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto ADM</label>
                <input type="file" class="form-control-file" id="foto" name="foto">
                <?php if (!empty($sub_adm['foto'])): ?>
                <img src="<?php echo htmlspecialchars($sub_adm['foto']); ?>" alt="Foto do adm"
                    class="img-fluid img-thumbnail" style="max-height: 200px; width: auto;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
</body>
</html>
