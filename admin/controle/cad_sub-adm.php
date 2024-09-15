<?php
include_once 'class/auth.php';
include_once '../includes/db_connect.php';

// Verificar acesso para nível 1
verificarAcesso(1, 'exato');

// Obter a instância da conexão PDO
$conn = Conectar::getInstance();

// Processar o formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = sha1($_POST['senha']);
    $nivel_acesso = $_POST['nivel_acesso'];

    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads_adm/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $foto = $target_file;
        } else {
            $foto = null;
        }
    } else {
        $foto = null;
    }

    try {
        // Preparar a consulta para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO adminstradores (nome, email, senha, nivel_acesso, foto) VALUES (:nome, :email, :senha, :nivel_acesso, :foto)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':nivel_acesso', $nivel_acesso, PDO::PARAM_INT);
        $stmt->bindParam(':foto', $foto);

        // Executar a consulta
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Sub-Administrador cadastrado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro: " . $stmt->errorInfo()[2] . "</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Administradores</title>
    <link rel="stylesheet" href="path_to_bootstrap.css"> <!-- Atualize o caminho para o Bootstrap -->
</head>
<body>
    <div class="container">
        <h2>Cadastrar Administrador</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="form-group">
                <label for="nivel_acesso">Nível de Acesso:</label>
                <select class="form-control" id="nivel_acesso" name="nivel_acesso" required>
                    <option value="2">Sub-Administrador Nível 2</option>
                    <option value="3">Sub-Administrador Nível 3</option>
                    <!-- Adicione outros níveis conforme necessário -->
                </select>
            </div>
            <div class="form-group">
                <label for="foto">Foto ADM</label>
                <input type="file" class="form-control-file" id="foto" name="foto">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</body>
</html>
