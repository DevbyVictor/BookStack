<?php
session_start();  // Iniciar a sessão

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo "Você precisa estar logado para cancelar a reserva.";
    exit;
}

$aluno_id = $_SESSION['aluno_id'];  // Pega o ID do aluno da sessão
$livro_id = $_POST['livro_id'];     // ID do livro que está sendo cancelado

require_once '../includes/db_connect.php';  // Conectar ao banco de dados
$conn = Conectar::getInstance();

// Verificar se o aluno já fez uma reserva desse livro e se pode cancelar
$query = "SELECT * FROM reservas WHERE aluno_id = ? AND livro_id = ? AND status = 'reservado'";
$stmt = $conn->prepare($query);
$stmt->execute([$aluno_id, $livro_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if ($reserva) {
    // Marcar a reserva como cancelada e registrar a data do cancelamento
    $query = "UPDATE reservas SET status = 'cancelado', data_cancelamento = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$reserva['id']]);
    
    echo "Reserva cancelada com sucesso.";
} else {
    echo "Não foi possível cancelar a reserva.";
}
?>
