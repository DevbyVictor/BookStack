<?php
session_start();  // Iniciar a sessão

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo "Você precisa estar logado para reservar um livro.";
    exit;
}

$aluno_id = $_SESSION['aluno_id'];  // Pega o ID do aluno da sessão

// Verificar se o livro_id foi passado via POST ou GET
if (isset($_POST['livro_id'])) {
    $livro_id = $_POST['livro_id'];
} elseif (isset($_GET['livro_id'])) {
    $livro_id = $_GET['livro_id'];
} else {
    echo "Livro não especificado.";
    exit;
}

require_once '../includes/db_connect.php';
require_once '../controle/class/livros.php';

// Instanciar a classe Livro
$livroObj = new Livro();
$livro = $livroObj->getLivro($livro_id);

// Verificar se o livro existe
if (!$livro) {
    echo "O livro não foi encontrado.";
    exit;
}

try {
    $conn = Conectar::getInstance();

    // Verificar o número de reservas ativas para este livro
    $query = "SELECT COUNT(*) as reservas_ativas FROM reservas WHERE livro_id = ? AND status = 'reservado'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $reservas_ativas = $resultado['reservas_ativas'];

    // Verificar se ainda há exemplares disponíveis para reserva
    if ($reservas_ativas >= $livro['exemplares']) {
        echo "Exemplares máximos atingidos.";
        exit;
    }

    // Verificar se o aluno já reservou o livro
    $query = "SELECT * FROM reservas WHERE livro_id = ? AND aluno_id = ? AND status = 'reservado'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id, $aluno_id]);

    if ($stmt->rowCount() > 0) {
        echo "Você já reservou este livro.";
        exit;
    }

    // Inserir a nova reserva
    $data_reserva = date('Y-m-d');
    $data_devolucao = date('Y-m-d', strtotime('+7 days'));

    $query = "INSERT INTO reservas (livro_id, aluno_id, data_reserva, data_devolucao, status) 
              VALUES (?, ?, ?, ?, 'reservado')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$livro_id, $aluno_id, $data_reserva, $data_devolucao]);

    echo "Reserva efetuada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao efetuar a reserva: " . $e->getMessage();
}
?>
