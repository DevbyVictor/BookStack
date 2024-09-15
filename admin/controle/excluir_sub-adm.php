<?php
include_once '../includes/db_connect.php';

$conn = Conectar::getInstance();

$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id) {
    $query = "DELETE FROM adminstradores WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: admin_dashboard.php?p=consultar_sub-adm');
exit();
?>
