<?php
session_start();

$showAlert = false;
if (isset($_SESSION['mensagem_sucesso']) || isset($_SESSION['mensagem_erro'])) {
    $showAlert = true;
}

$mensagem = '';
$tipoMensagem = '';
if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    $tipoMensagem = 'success';
    unset($_SESSION['mensagem_sucesso']);
} elseif (isset($_SESSION['mensagem_erro'])) {
    $mensagem = $_SESSION['mensagem_erro'];
    $tipoMensagem = 'error';
    unset($_SESSION['mensagem_erro']);
}

$aluno_id = $_SESSION['aluno_id'];

require_once '../includes/db_connect.php';
$conn = Conectar::getInstance();

$query = "
    SELECT r.*, 
           l.titulo AS livro_titulo, 
           r.data_reserva, 
           r.data_retirada, 
           r.data_devolucao, 
           r.status, 
           r.data_cancelamento
    FROM reservas r 
    JOIN livros l ON r.livro_id = l.id 
    WHERE r.aluno_id = ? 
    ORDER BY r.data_reserva DESC
";
$stmt = $conn->prepare($query);
$stmt->execute([$aluno_id]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/// Função para calcular status do livro
function calcularStatus($reserva)
{
    $hoje = new DateTime();
    $status = $reserva['status'];

    if ($status === 'reservado') {
        return 'Reservado';
    } elseif ($status === 'cancelado') {
        return 'Cancelado em ' . (new DateTime($reserva['data_cancelamento']))->format('d/m/Y');
    } elseif ($status === 'retirado') {
        return '<span class="text-success"><i class="fas fa-hand-holding"></i> Livro em mãos</span>';
    } elseif ($status === 'devolvido') {
        return 'Devolvido em ' . (new DateTime($reserva['data_devolucao']))->format('d/m/Y');
    }
    return 'Status desconhecido';
}

// Função para calcular prazo para retirada ou devolução
function calcularPrazoRetirada($reserva)
{
    $hoje = new DateTime();

    if ($reserva['status'] === 'reservado') {
        $data_reserva = new DateTime($reserva['data_reserva']);
        $prazo_retirada = $data_reserva->modify('+2 days');

        if ($hoje > $prazo_retirada) {
            return 'Prazo expirado';
        }
        $dias_restantes = $prazo_retirada->diff($hoje)->days;
        return "Disponível por mais {$dias_restantes} dias";
    } elseif ($reserva['status'] === 'retirado') {
        if (!empty($reserva['data_devolucao'])) {
            $data_devolucao = new DateTime($reserva['data_devolucao']);
            if ($hoje > $data_devolucao) {
                $dias_atraso = $hoje->diff($data_devolucao)->days;
                return "Vencido há {$dias_atraso} dias";
            }
            $dias_restantes = $data_devolucao->diff($hoje)->days;
            return "Devolução em {$dias_restantes} dias";
        }
    }
    return ''; // Retorna vazio para outros casos
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- FontAwesome -->
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

        .back-button {
            margin-top: 20px;
            margin-bottom: 20px;
            color: #212529;
            background-color: #f7d547;
            border: none;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: bold;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: #e6c236;
        }
    </style>
</head>

<body>
    <header class="hero">
        <div class="hero-content">
            <h1>Seu Histórico de Reservas</h1>
            <p>Acompanhe todas as suas reservas de livros realizadas!</p>
        </div>
    </header>

    <div class="container text-center">
        <button class="back-button" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Voltar</button>
    </div>

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
                            <th scope="col">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr id="reserva-<?= $reserva['id'] ?>">
                                <td><?= htmlspecialchars($reserva['livro_titulo']) ?></td>
                                <td><?= htmlspecialchars((new DateTime($reserva['data_reserva']))->format('d/m/Y')) ?></td>
                                <td>
                                    <?= calcularPrazoRetirada($reserva) ?>
                                </td>
                                <td>
                                    <?= calcularStatus($reserva) ?>
                                </td>
                                <td class="acao">
                                    <?php if ($reserva['status'] === 'reservado'): ?>
                                        <button class="btn btn-danger" onclick="cancelarReserva(<?= $reserva['id'] ?>)">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">Sem ações disponíveis</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer class="mt-5">
        <div class="container">
            <p>&copy; 2024 BookStack. Todos os direitos reservados.</p>
            <a href="#top">Voltar ao Topo</a>
        </div>
    </footer>

    <script>
        async function cancelarReserva(reserva_id) {
            try {
                const result = await Swal.fire({
                    title: 'Confirmação de Cancelamento',
                    text: "Tem certeza que deseja cancelar esta reserva?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, cancelar!',
                    cancelButtonText: 'Não, manter reserva'
                });

                if (result.isConfirmed) {
                    const response = await $.ajax({
                        url: '../controle/cancelar_reserva.php',
                        type: 'POST',
                        dataType: 'json', // Certifique-se de que o controlador retorna JSON
                        data: { reserva_id: reserva_id }
                    });

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cancelada com sucesso!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        $(`#reserva-${reserva_id} .status`).text('Cancelado');
                        $(`#reserva-${reserva_id} .acao`).html('<span class="text-muted">Cancelada</span>');

                    } else if (response.status === 'notificacao') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção!',
                            text: response.mensagem,
                            showConfirmButton: true
                        });

                        // Exibe mensagem de notificação sem recarregar
                        console.log('Notificação recebida:', response.mensagem);

                    } else if (response.status === 'punido') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Punição Aplicada!',
                            text: `Você foi punido e não poderá fazer novas reservas por ${response.horas_punicao} horas.`,
                            showConfirmButton: true
                        });

                        $(`#reserva-${reserva_id} .status`).text('Punição Ativa');
                    } else if (response.status === 'error') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: response.message,
                            showConfirmButton: true
                        });
                    }
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Houve um erro ao cancelar a reserva. Tente novamente.',
                    showConfirmButton: false,
                    timer: 2000
                });
                console.error('Erro ao cancelar a reserva:', error);
            }
        }
    </script>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>