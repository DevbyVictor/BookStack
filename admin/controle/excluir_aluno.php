<?php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['rm'])) {
    $rm = $_GET['rm'];
    try {
        $conn = Conectar::getInstance();

        // Excluir aluno (reservas serão excluídas automaticamente por ON DELETE CASCADE)
        $stmt = $conn->prepare("DELETE FROM alunos WHERE rm = :rm");
        $stmt->bindParam(':rm', $rm, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?p=consultar_alunos");
            exit();
        } else {
            echo "Erro ao excluir o aluno.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    echo "Acesso inválido.";
}
?>
