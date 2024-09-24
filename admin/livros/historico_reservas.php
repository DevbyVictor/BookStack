<?php
session_start();  // Iniciar a sessão

// Verificar se existe uma mensagem de sucesso ou erro na sessão
$showAlert = false; // Controle para exibir o alerta
if (isset($_SESSION['mensagem_sucesso']) || isset($_SESSION['mensagem_erro'])) {
    $showAlert = true;
}

// Definir a mensagem que será exibida no SweetAlert
$mensagem = '';
$tipoMensagem = '';  // Define o tipo da mensagem para aplicar classes diferentes
if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    $tipoMensagem = 'success';  // Tipo de mensagem para SweetAlert (sucesso)
    unset($_SESSION['mensagem_sucesso']);  // Remover a mensagem da sessão após exibir
} elseif (isset($_SESSION['mensagem_erro'])) {
    $mensagem = $_SESSION['mensagem_erro'];
    $tipoMensagem = 'error';  // Tipo de mensagem para SweetAlert (erro)
    unset($_SESSION['mensagem_erro']);  // Remover a mensagem da sessão após exibir
}

$aluno_id = $_SESSION['aluno_id'];  // Pega o ID do aluno da sessão

require_once '../includes/db_connect.php';  // Conectar ao banco de dados
$conn = Conectar::getInstance();

// Consulta para buscar o histórico de reservas do aluno, incluindo canceladas
$query = "
    SELECT r.*, l.titulo AS livro_titulo, r.data_reserva, r.status
    FROM reservas r 
    JOIN livros l ON r.livro_id = l.id 
    WHERE r.aluno_id = ? 
    ORDER BY r.data_reserva DESC
";
$stmt = $conn->prepare($query);
$stmt->execute([$aluno_id]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Usar fetchAll() para obter todas as linhas

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        .hero {
            position: relative;
            background: url('https://source.unsplash.com/1600x600/?library,books') no-repeat center center;
            background-size: cover;
            color: #212529;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: inset 0 0 0 1000px rgba(255, 255, 255, 0.8);
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: bold;
            margin: 0;
            color: #212529;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 1s ease-in-out;
        }

        .hero p {
            font-size: 1.5rem;
            line-height: 1.5;
            margin: 0;
            color: #555;
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            padding-top: 40px;
        }

        .section-title {
            margin-bottom: 40px;
            font-size: 2.5rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #212529;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            width: 100px;
            height: 3px;
            background-color: #f7d547;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .table-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .table thead {
            background-color: #f7d547;
            color: #212529;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f7f7f7;
        }

        .btn-primary {
            background-color: #f7d547;
            color: #212529;
            border: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e6c236;
        }

        .btn-danger {
            background-color: #ff6f61;
            color: #fff;
            border: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #e65a50;
        }

        footer {
            background-color: #212529;
            color: #f7d547;
            padding: 20px 0;
            text-align: center;
        }

        footer a {
            color: #f7d547;
        }

        footer a:hover {
            color: #e6c236;
        }

        .icon {
            font-size: 1.5rem;
            margin-right: 5px;
        }

        .tooltip-inner {
            background-color: #212529 !important;
            color: #f7d547 !important;
        }

        .tooltip-arrow {
            border-top-color: #212529 !important;
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
        <h2 class="section-title">Histórico de Reservas</h2>

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
                            <th scope="col">Ação</th> <!-- Coluna de Ação -->
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
                            if ($reserva['status'] == 'cancelado') {
                                $status_retirada = 'Cancelado';
                            } elseif ($hoje > $prazo_retirada && $reserva['status'] != 'retirado') {
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
                                <td>
                                    <?php if ($reserva['status'] == 'reservado'): ?>
                                        <!-- Botão para cancelar a reserva -->
                                        <button class="btn btn-danger" onclick="cancelarReserva(<?= $reserva['id'] ?>)">
                                            <i class="fas fa-times icon"></i> Cancelar
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">Indisponível</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rodapé -->
    <footer class="mt-5">
        <div class="container">
            <p>&copy; 2024 BookStack. Todos os direitos reservados.</p>
            <a href="#top">Voltar ao Topo</a>
        </div>
    </footer>

    <!-- SweetAlert Script para Cancelar Reserva -->
    <script>
        function cancelarReserva(reserva_id) {
            Swal.fire({
                title: 'Confirmação de Cancelamento',
                text: "Tem certeza que deseja cancelar esta reserva?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, cancelar!',
                cancelButtonText: 'Não, manter reserva'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Faz a requisição Ajax para cancelar a reserva
                    $.ajax({
                        url: '../controle/cancelar_reserva.php',
                        type: 'POST',
                        data: { reserva_id: reserva_id },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelada com sucesso!',
                                text: 'A reserva foi cancelada.',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Atualizar a página ou o botão de reserva após o sucesso
                            setTimeout(function () {
                                location.reload(); // Atualiza a página após o cancelamento
                            }, 2000);
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Houve um erro ao cancelar a reserva. Tente novamente.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                }
            });
        }
    </script>

    <!-- Mostrar SweetAlert se houver mensagem na sessão -->
    <?php if ($showAlert): ?>
    <script>
        Swal.fire({
            icon: '<?= $tipoMensagem ?>',
            title: '<?= $tipoMensagem === 'success' ? 'Sucesso!' : 'Erro!' ?>',
            text: '<?= $mensagem ?>',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    <?php endif; ?>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
