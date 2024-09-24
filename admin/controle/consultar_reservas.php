<?php
session_start();  // Iniciar a sessão

include_once '../includes/db_connect.php';
$conn = Conectar::getInstance();

// Função para limpar reservas canceladas, chamada via AJAX
if (isset($_POST['acao']) && $_POST['acao'] == 'limpar_canceladas') {
    try {
        $deleteQuery = "DELETE FROM reservas WHERE status = 'cancelado'";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute();
        
        // Mensagem de sucesso
        echo json_encode(['status' => 'success', 'mensagem' => 'Reservas canceladas removidas com sucesso!']);
    } catch (PDOException $e) {
        // Mensagem de erro
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao remover reservas canceladas: ' . $e->getMessage()]);
    }
    exit;
}

// Filtro de Reservas
$filtro_aluno = isset($_POST['filtro_aluno']) ? $_POST['filtro_aluno'] : '';
$filtro_status = isset($_POST['filtro_status']) ? $_POST['filtro_status'] : '';
$filtro_data = isset($_POST['filtro_data']) ? $_POST['filtro_data'] : '';

$query = "
    SELECT r.*, l.titulo AS livro_titulo, a.nome AS aluno_nome, a.rm AS aluno_rm 
    FROM reservas r 
    JOIN livros l ON r.livro_id = l.id 
    JOIN alunos a ON r.aluno_id = a.id
    WHERE (a.nome LIKE :filtro_aluno OR :filtro_aluno = '')
    AND (r.status = :filtro_status OR :filtro_status = '')
    AND (r.data_reserva = :filtro_data OR :filtro_data = '')
    ORDER BY r.data_reserva DESC
";
$stmt = $conn->prepare($query);
$stmt->execute([
    ':filtro_aluno' => "%$filtro_aluno%",
    ':filtro_status' => $filtro_status,
    ':filtro_data' => $filtro_data
]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico Completo de Reservas</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css"> <!-- CSS comum da aplicação -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
        }
        .container {
            margin-top: 20px;
        }
        .table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #f7d547; /* Amarelo */
            color: #212529; /* Preto */
            border-bottom: 2px solid #e6c236; /* Sombra */
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9; /* Fundo das linhas ímpares */
        }
        .table tbody tr:hover {
            background-color: #f0f0f0; /* Fundo ao passar o mouse */
        }
        .table .icon-cancelled {
            color: red;
            font-size: 1.2em;
            margin-left: 10px;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .filters select, .filters input {
            margin-right: 10px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Histórico Completo de Reservas</h2>

        <!-- Botão para Limpar Reservas Canceladas -->
        <button id="btn-limpar-canceladas" class="btn btn-danger mb-4">
            <i class="fas fa-trash-alt"></i> Limpar Reservas Canceladas
        </button>

        <!-- Filtros de Reservas -->
        <form method="POST" class="filters mb-4">
            <input type="text" name="filtro_aluno" placeholder="Filtrar por Aluno" value="<?= htmlspecialchars($filtro_aluno) ?>">
            <input type="date" name="filtro_data" placeholder="Filtrar por Data" value="<?= htmlspecialchars($filtro_data) ?>">
            <select name="filtro_status">
                <option value="">Todos os Status</option>
                <option value="reservado" <?= $filtro_status == 'reservado' ? 'selected' : '' ?>>Reservado</option>
                <option value="cancelado" <?= $filtro_status == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                <option value="retirado" <?= $filtro_status == 'retirado' ? 'selected' : '' ?>>Retirado</option>
            </select>
            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
        </form>

        <!-- Tabela de Reservas -->
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
                    <td>
                        <?= htmlspecialchars($reserva['status']) ?>
                        <?php if ($reserva['status'] == 'cancelado'): ?>
                            <i class="fas fa-exclamation-circle icon-cancelled" title="Reserva Cancelada"></i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- SweetAlert para mensagem de sucesso/erro -->
    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '<?= $_SESSION['mensagem_sucesso'] ?>',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php elseif (isset($_SESSION['mensagem_erro'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: '<?= $_SESSION['mensagem_erro'] ?>',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script> <!-- Arquivo JS comum da aplicação -->
    <script>
        // Tooltip para ícone de reserva cancelada
        document.querySelectorAll('.icon-cancelled').forEach(icon => {
            icon.addEventListener('mouseover', function() {
                this.title = "Esta reserva foi cancelada";
            });
        });

        // Função AJAX para limpar reservas canceladas
        document.getElementById('btn-limpar-canceladas').addEventListener('click', function() {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Esta ação irá remover todas as reservas canceladas.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e65a50',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, limpar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'historico_reservas_admin.php', // Mesmo arquivo PHP
                        type: 'POST',
                        data: { acao: 'limpar_canceladas' },
                        success: function(response) {
                            var resultado = JSON.parse(response);
                            if (resultado.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: resultado.mensagem,
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                                // Atualiza a tabela para remover as reservas canceladas
                                setTimeout(function() {
                                    location.reload(); // Recarrega a página para atualizar a tabela
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro!',
                                    text: resultado.mensagem,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
