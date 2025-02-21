<?php
session_start();  // Iniciar a sessão

header('Content-Type: application/json');

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo json_encode(['status' => 'not_logged_in']);
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
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if ($reserva) {
    // Atualizar o status da reserva para 'cancelado' e registrar a data de cancelamento
    $query = "UPDATE reservas SET status = 'cancelado', data_cancelamento = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$reserva_id]);

    // Verificar quantas vezes o aluno já cancelou a reserva do mesmo livro em menos de 15 minutos
    $livro_id = $reserva['livro_id'];
    $query = "SELECT COUNT(*) as cancelamentos_recentes FROM reservas WHERE aluno_id = ? AND livro_id = ? AND status = 'cancelado' AND TIMESTAMPDIFF(MINUTE, data_cancelamento, NOW()) <= 15";
    $stmt = $conn->prepare($query);
    $stmt->execute([$aluno_id, $livro_id]);
    $cancelamentos_recentes = $stmt->fetch(PDO::FETCH_ASSOC)['cancelamentos_recentes'];

    // Se for o 3º cancelamento em 15 minutos, aplicar a punição
    if ($cancelamentos_recentes >= 3) {
        // Aplicar a punição por 5 horas
        $_SESSION['punição'] = time() + (5 * 60 * 60); // Punição de 5 horas
        echo json_encode(['status' => 'punido', 'horas_punicao' => 5]);
        exit;
    } elseif ($cancelamentos_recentes == 2) {
        // Notificar o usuário que, se cancelar mais uma vez, será punido
        echo json_encode(['status' => 'notificacao', 'mensagem' => 'Se cancelar mais uma vez em até 15 minutos, você será punido com um tempo de espera de 5 horas.']);
        exit;
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Reserva cancelada com sucesso.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Não foi possível cancelar a reserva.']);
    exit;
}
?>
