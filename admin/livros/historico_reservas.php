<?php
// Conectar ao banco de dados
session_start();  // Iniciar a sessão

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo "Você precisa estar logado para ver o histórico de reservas.";
    exit;
}

$aluno_id = $_SESSION['aluno_id'];  // Pega o ID do aluno da sessão

require_once '../includes/db_connect.php';  // Conectar ao banco de dados
$conn = Conectar::getInstance();

// Consulta para buscar o histórico de reservas do aluno
$query = "
    SELECT r.*, l.titulo AS livro_titulo, r.data_reserva, r.status
    FROM reservas r 
    JOIN livros l ON r.livro_id = l.id 
    WHERE r.aluno_id = ? 
    ORDER BY r.data_reserva DESC
";
$stmt = $conn->prepare($query);
$stmt->execute([$aluno_id]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);   
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .hero {
            position: relative;
            background: url('img/background-reservas.jpeg') no-repeat center center;
            background-size: cover;
            color: #fff;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .hero p {
            font-size: 1.25rem;
            line-height: 1.5;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .container {
            padding-top: 40px;
        }

        .section-title {
            margin-bottom: 40px;
            font-size: 2rem;
            font-weight: bold;
        }

        .table-section {
            background-color: #f5f5f5;
            padding: 60px 0;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
    </style>
</head>

<body>
    <!-- Seção Hero -->
    <header class="hero">
        <div class="hero-content">
            <h1>Seu Histórico de Reservas</h1>
            <p>Acompanhe todas as suas reservas de livros realizadas!</p>
        </div>
    </header>

    <!-- Container Principal -->
    <div class="container">
        <h2 class="section-title text-center">Histórico de Reservas</h2>

        <?php if (empty($reservas)): ?>
            <div class="alert alert-info text-center">Você ainda não fez nenhuma reserva.</div>
        <?php else: ?>
            <div class="table-responsive table-section">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Livro</th>
                            <th scope="col">Data da Reserva</th>
                            <th scope="col">Prazo para Retirada</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                        <?php
                            // Calcular a data limite de retirada (2 dias após a data da reserva)
                            $reserva_data = new DateTime($reserva['data_reserva']);
                            $prazo_retirada = clone $reserva_data;
                            $prazo_retirada->modify('+2 days');

                            // Comparar com a data atual
                            $hoje = new DateTime();
                            if ($hoje > $prazo_retirada && $reserva['status'] != 'retirado') {
                                $status_retirada = 'Expirado';
                            } elseif ($reserva['status'] == 'retirado') {
                                $status_retirada = 'Livro Retirado';
                            } else {
                                $status_retirada = 'Disponível para retirada até ' . $prazo_retirada->format('d/m/Y');
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['livro_titulo']) ?></td>
                            <td><?= htmlspecialchars($reserva['data_reserva']) ?></td>
                            <td><?= $status_retirada ?></td>
                            <td><?= htmlspecialchars($reserva['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rodapé -->
    <footer class="bg-light py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 BookStack. Todos os direitos reservados.</p>
            <a href="#top" class="btn btn-outline-dark mt-3">Voltar ao Topo</a>
        </div>
    </footer>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
