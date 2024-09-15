<?php
require_once '../includes/db_connect.php'; // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rm = $_POST['rm'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = $_POST['telefone'];
    $foto = null;

    // Verifica se foi enviado um arquivo
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = 'uploads/' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    try {
        $conn = Conectar::getInstance();

        // Prepara a inserção no banco de dados
        $stmt = $conn->prepare("INSERT INTO alunos (rm, nome, email, senha, telefone, foto) VALUES (:rm, :nome, :email, :senha, :telefone, :foto)");
        $stmt->bindParam(':rm', $rm);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':foto', $foto);

        if ($stmt->execute()) {
            echo "Aluno cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar o aluno.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
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
        <h2>Cadastrar Aluno</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="rm">RM:</label>
                <input type="text" class="form-control" id="rm" name="rm" required>
            </div>
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
                <label for="telefone">Telefone:</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto Aluno</label>
                <input type="file" class="form-control-file" id="foto" name="foto">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</body>

</html>