<?php
session_start();  // Iniciar a sessão

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo "Você precisa estar logado para cancelar a reserva.";
    exit;
}

$aluno_id = $_SESSION['aluno_id'];  // Pega o ID do aluno da sessão
$reserva_id = $_POST['reserva_id']; // ID da reserva que está sendo cancelada

require_once '../includes/db_connect.php';  // Conectar ao banco de dados
$conn = Conectar::getInstance();

// Verificar se a reserva pertence ao aluno e se está no status "reservado"
$query = "SELECT * FROM reservas WHERE id = ? AND aluno_id = ? AND status = 'reservado'";
$stmt = $conn->prepare($query);
$stmt->execute([$reserva_id, $aluno_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);  // Usar fetch() para obter uma linha

if ($reserva) {
    // Atualizar o status da reserva para 'cancelado' e registrar a data de cancelamento
    $query = "UPDATE reservas SET status = 'cancelado', data_cancelamento = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$reserva_id]);

    // Armazenar a mensagem de sucesso na sessão
    $_SESSION['mensagem_sucesso'] = "Reserva cancelada com sucesso!";
} else {
    $_SESSION['mensagem_erro'] = "Não foi possível cancelar a reserva.";
}

// Redirecionar de volta para a página de histórico
header("Location: ../livros/historico_reservas.php?p=historico");
exit();
?>
