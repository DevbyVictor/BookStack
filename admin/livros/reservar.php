<?php
session_start();
header('Content-Type: application/json');

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$aluno_id = $_SESSION['aluno_id'];  // ID do aluno da sessão

// Verificar se o aluno está punido
if (isset($_SESSION['punição']) && time() < $_SESSION['punição']) {
    $horas_restantes = ceil(($_SESSION['punição'] - time()) / 3600);

    echo json_encode([
        'status' => 'punido',
        'horas_punicao' => (int) $horas_restantes // Converter para inteiro
    ]);
    exit;
}

// Verificar se o livro_id foi passado via POST
if (isset($_POST['livro_id'])) {
    $livro_id = $_POST['livro_id'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Livro não especificado']);
    exit;
}

require_once '../includes/db_connect.php';
require_once '../controle/class/livros.php';

// Instanciar a classe Livro
$livroObj = new Livro();
$livro = $livroObj->getLivro($livro_id);

// Verificar se o livro existe
if (!$livro) {
    echo json_encode(['status' => 'error', 'message' => 'Livro não encontrado']);
    exit;
}

try {
    $conn = Conectar::getInstance();

    // Iniciar transação para garantir consistência
    $conn->beginTransaction();

    // Verificar o número de reservas ativas para este livro
    $query = "SELECT exemplares_disponiveis FROM livros WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se ainda há exemplares disponíveis para reserva
    if (!$resultado || $resultado['exemplares_disponiveis'] <= 0) {
        echo json_encode(['status' => 'max_exemplares']);
        $conn->rollBack(); // Reverter alterações caso algo dê errado
        exit;
    }

    // Verificar se o aluno já reservou o livro
    $query = "SELECT * FROM reservas WHERE livro_id = ? AND aluno_id = ? AND status = 'reservado'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id, $aluno_id]);
    $reserva_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reserva_existente) {
        echo json_encode(['status' => 'already_reserved']);
        $conn->rollBack(); // Reverter alterações caso algo dê errado
        exit;
    }

    // Verificar se o aluno já possui uma reserva ou retirada ativa para o mesmo livro
    $query = "SELECT * FROM reservas WHERE livro_id = ? AND aluno_id = ? AND status IN ('reservado', 'retirado')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id, $aluno_id]);
    $reserva_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reserva_existente) {
        echo json_encode(['status' => 'already_reserved_or_retirado']);
        $conn->rollBack(); // Reverter alterações caso algo dê errado
        exit;
    }

    // Inserir a nova reserva
    $data_reserva = date('Y-m-d H:i:s');
    $data_devolucao = date('Y-m-d', strtotime('+7 days'));

    $query = "INSERT INTO reservas (livro_id, aluno_id, data_reserva, data_devolucao, status) VALUES (?, ?, ?, ?, 'reservado')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id, $aluno_id, $data_reserva, $data_devolucao]);

    // Atualizar exemplares disponíveis
    $query = "UPDATE livros SET exemplares_disponiveis = exemplares_disponiveis - 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id]);

    // Confirmar a transação
    $conn->commit();

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    $conn->rollBack(); // Reverter alterações em caso de erro
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>