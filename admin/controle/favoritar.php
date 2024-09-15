<?php
include ('../includes/db_connect.php');

$conn = Conectar::getInstance();

$id = $_POST['id'];
$action = $_POST['action'];

if ($action === 'favorite') {
    $query = "UPDATE livros SET favoritado = TRUE WHERE id = :id";
} elseif ($action === 'unfavorite') {
    $query = "UPDATE livros SET favoritado = FALSE WHERE id = :id";
} else {
    echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    exit();
}

$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar']);
}
?>