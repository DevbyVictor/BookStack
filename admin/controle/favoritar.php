<?php
session_start();
include('../includes/db_connect.php');

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Você precisa estar logado para favoritar um livro.']);
    exit();
}

// Verifica se o livro_id e action foram enviados no POST
if (!isset($_POST['livro_id']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos para favoritar ou desfavoritar.']);
    exit();
}

$aluno_id = $_SESSION['aluno_id'];
$livro_id = $_POST['livro_id'];
$action = $_POST['action'];

// Conecta ao banco de dados
$conn = Conectar::getInstance();

if ($action === 'favorite') {
    try {
        // Tenta inserir diretamente para evitar duplicidades
        $stmt = $conn->prepare("INSERT INTO favoritos (aluno_id, livro_id) VALUES (:aluno_id, :livro_id)");
        $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
        $stmt->bindParam(':livro_id', $livro_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Livro adicionado aos favoritos.']);
    } catch (PDOException $e) {
        // Caso o livro já esteja favoritado, capturamos o erro de duplicidade
        if ($e->getCode() == 23000) { // Código SQLSTATE para duplicidade
            echo json_encode(['status' => 'error', 'message' => 'Livro já está nos favoritos.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar o livro aos favoritos.']);
        }
    }
    exit();
} elseif ($action === 'unfavorite') {
    // Remove o livro dos favoritos
    $stmt = $conn->prepare("DELETE FROM favoritos WHERE aluno_id = :aluno_id AND livro_id = :livro_id");
    $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
    $stmt->bindParam(':livro_id', $livro_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Livro removido dos favoritos.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao remover o livro dos favoritos.']);
    }
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ação inválida.']);
    exit();
}
