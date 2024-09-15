<?php
include_once '../includes/db_connect.php';
$conn = Conectar::getInstance();


// Consulta ao banco de dados para exibir reservas
$query = "SELECT r.*, l.titulo AS livro_titulo, a.nome AS aluno_nome, a.rm AS aluno_rm 
          FROM reservas r 
          JOIN livros l ON r.livro_id = l.id 
          JOIN alunos a ON r.aluno_id = a.id 
          WHERE r.status != 'devolvido'";
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
        <h2 class="mb-4">Reservas de Livros</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Livro</th>
                    <th>Aluno</th>
                    <th>RM</th>
                    <th>Data de Reserva</th>
                    <th>Data de Devolução</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= $reserva['livro_titulo'] ?></td>
                        <td><?= $reserva['aluno_nome'] ?></td>
                        <td><?= $reserva['aluno_rm'] ?></td>
                        <td><?= $reserva['data_reserva'] ?></td>
                        <td><?= $reserva['data_devolucao'] ?></td>
                        <td><?= ($reserva['status'] == 'atrasado') ? '<span class="text-danger">Atrasado</span>' : 'Em dia' ?></td>
                        <td><button class="btn btn-primary" onclick="confirmReservation(<?= $reserva['livro_id'] ?>)">Reservar</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
