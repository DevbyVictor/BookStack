<?php
include_once '../includes/db_connect.php';
$conn = Conectar::getInstance();

// Consulta ao banco de dados para exibir todas as reservas
$query = "
    SELECT r.*, l.titulo AS livro_titulo, a.nome AS aluno_nome, a.rm AS aluno_rm 
    FROM reservas r 
    JOIN livros l ON r.livro_id = l.id 
    JOIN alunos a ON r.aluno_id = a.id
    ORDER BY r.data_reserva DESC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Histórico Completo de Reservas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Livro</th>
                    <th>Aluno</th>
                    <th>RM</th>
                    <th>Data da Reserva</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['livro_titulo']) ?></td>
                    <td><?= htmlspecialchars($reserva['aluno_nome']) ?></td>
                    <td><?= htmlspecialchars($reserva['aluno_rm']) ?></td>
                    <td><?= htmlspecialchars($reserva['data_reserva']) ?></td>
                    <td><?= htmlspecialchars($reserva['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
