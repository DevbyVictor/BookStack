<?php
ob_start();
@session_start();  // Iniciar a sessão
header('Content-Type: application/json; charset=utf-8'); // Sempre envie JSON
include_once '../includes/db_connect.php';
$conn = Conectar::getInstance();

ob_clean();

if (isset($_POST['acao']) && $_POST['acao'] === 'limpar_canceladas') {
    header('Content-Type: application/json; charset=utf-8');
    if (!isset($_POST['reserva_ids']) || empty($_POST['reserva_ids'])) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Nenhuma reserva selecionada.']);
        exit;
    }

    $reserva_ids = json_decode($_POST['reserva_ids'], true);
    try {
        // Monta a consulta para excluir os IDs selecionados
        $deleteQuery = "DELETE FROM reservas WHERE id IN (" . implode(',', array_map('intval', $reserva_ids)) . ")";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'mensagem' => 'Reservas selecionadas removidas com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao remover reservas selecionadas: ' . $e->getMessage()]);
    }
    exit;
}

// Inicialize as variáveis
$filtro_aluno = isset($_POST['filtro_aluno']) ? $_POST['filtro_aluno'] : '';
$filtro_status = isset($_POST['filtro_status']) ? $_POST['filtro_status'] : '';
$filtro_data = isset($_POST['filtro_data']) ? $_POST['filtro_data'] : '';

// Paginação
$resultados_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina_atual < 1) {
    $pagina_atual = 1;
}
$offset = ($pagina_atual - 1) * $resultados_por_pagina;

// Condições dinâmicas para o filtro de status
$status_condition = '';
$params = [':filtro_aluno' => "%$filtro_aluno%", ':filtro_data' => $filtro_data];

if ($filtro_status === 'vencer') {
    $status_condition = "DATE_ADD(r.data_retirada, INTERVAL 7 DAY) >= CURDATE()";
} elseif ($filtro_status === 'vencido') {
    $status_condition = "DATE_ADD(r.data_retirada, INTERVAL 7 DAY) < CURDATE()";
} elseif ($filtro_status === 'cancelado') {
    $status_condition = "r.status = 'cancelado'"; // Condição para cancelado
} elseif ($filtro_status) {
    $status_condition = "r.status = :filtro_status";
    $params[':filtro_status'] = $filtro_status;
} else {
    $status_condition = '1=1'; // Sempre verdadeiro
}

// Consulta principal com limites para paginação
$query = "
    SELECT r.*, l.titulo AS livro_titulo, a.nome AS aluno_nome, a.rm AS aluno_rm 
    FROM reservas r 
    JOIN livros l ON r.livro_id = l.id 
    JOIN alunos a ON r.aluno_id = a.id
    WHERE (a.nome LIKE :filtro_aluno)
    AND ($status_condition)
    AND (:filtro_data = '' OR r.data_reserva = :filtro_data)
    ORDER BY r.data_reserva DESC
    LIMIT $offset, $resultados_por_pagina
";

// Agora prepare e execute a consulta
$stmt = $conn->prepare($query);
$stmt->execute($params);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obter o número total de resultados (sem limites de paginação)
$query_total = "
    SELECT COUNT(*) AS total
    FROM reservas r
    JOIN livros l ON r.livro_id = l.id 
    JOIN alunos a ON r.aluno_id = a.id
    WHERE (a.nome LIKE :filtro_aluno)
    AND ($status_condition)
    AND (:filtro_data = '' OR r.data_reserva = :filtro_data)
";

$stmt_total = $conn->prepare($query_total);
$stmt_total->execute($params);
$total_resultados = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_resultados / $resultados_por_pagina);

// Confirmar retirada
if (isset($_POST['acao']) && $_POST['acao'] === 'confirmar_retirada') {
    header('Content-Type: application/json; charset=utf-8');
    if (!isset($_POST['reserva_ids']) || empty($_POST['reserva_ids'])) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Nenhuma reserva selecionada.']);
        exit;
    }

    $reserva_ids = json_decode($_POST['reserva_ids'], true);
    try {
        foreach ($reserva_ids as $reserva_id) {
            // Buscar os dados da reserva (livro e aluno)
            $queryReserva = "SELECT livro_id, aluno_id FROM reservas WHERE id = :id";
            $stmt = $conn->prepare($queryReserva);
            $stmt->bindParam(':id', $reserva_id, PDO::PARAM_INT);
            $stmt->execute();
            $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reserva) {
                echo json_encode(['status' => 'error', 'mensagem' => 'Reserva não encontrada.']);
                exit;
            }

            $livro_id = $reserva['livro_id'];
            $aluno_id = $reserva['aluno_id'];

            // Atualizar a reserva para "retirado" e registrar a data de retirada
            $updateReserva = "UPDATE reservas SET status = 'retirado', data_retirada = NOW() WHERE id = :id";
            $stmt = $conn->prepare($updateReserva);
            $stmt->bindParam(':id', $reserva_id, PDO::PARAM_INT);
            $stmt->execute();

            // Decrementar o número de exemplares disponíveis
            $updateExemplaresDisponiveis = "
                UPDATE livros 
                SET exemplares_disponiveis = exemplares_disponiveis - 1 
                WHERE id = :livro_id AND exemplares_disponiveis > 0";
            $stmt = $conn->prepare($updateExemplaresDisponiveis);
            $stmt->bindParam(':livro_id', $livro_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Retorne a data atual para o frontend
        $dataAtual = date('Y-m-d H:i:s');
        echo json_encode([
            'status' => 'success',
            'mensagem' => 'Retirada(s) confirmada(s) com sucesso!',
            'data_retirada' => $dataAtual, // Data atual para atualizar no frontend
            'reserva_ids' => $reserva_ids // IDs das reservas atualizadas
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao confirmar retirada: ' . $e->getMessage()]);
    }
    exit;
}
$updateQuery = "UPDATE reservas SET status = 'retirado', data_retirada = NOW() WHERE id = :id";

// Confirmar devolução
if (isset($_POST['acao']) && $_POST['acao'] === 'confirmar_devolucao') {
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_POST['reserva_ids']) || empty($_POST['reserva_ids'])) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Nenhuma reserva selecionada.']);
        exit;
    }

    $reserva_ids = json_decode($_POST['reserva_ids'], true);

    try {
        foreach ($reserva_ids as $reserva_id) {
            // Buscar o ID do livro associado à reserva
            $queryLivro = "SELECT livro_id FROM reservas WHERE id = :id";
            $stmt = $conn->prepare($queryLivro);
            $stmt->bindParam(':id', $reserva_id, PDO::PARAM_INT);
            $stmt->execute();
            $livro_id = $stmt->fetchColumn();

            if (!$livro_id) {
                echo json_encode(['status' => 'error', 'mensagem' => 'Reserva não encontrada.']);
                exit;
            }

            // Atualizar a reserva para "devolvido" e registrar a data de devolução
            $updateReserva = "UPDATE reservas SET status = 'devolvido', data_devolucao = NOW() WHERE id = :id";
            $stmt = $conn->prepare($updateReserva);
            $stmt->bindParam(':id', $reserva_id, PDO::PARAM_INT);
            $stmt->execute();

            // Incrementar o número de exemplares disponíveis
            $updateExemplares = "
                UPDATE livros 
                SET exemplares_disponiveis = exemplares_disponiveis + 1 
                WHERE id = :livro_id";
            $stmt = $conn->prepare($updateExemplares);
            $stmt->bindParam(':livro_id', $livro_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success', 'mensagem' => 'Devolução(ões) confirmada(s) com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao confirmar devolução: ' . $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico Completo de Reservas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            background-color: #f7d547;
            /* Amarelo */
            color: #212529;
            /* Preto */
            border-bottom: 2px solid #e6c236;
            /* Sombra */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
            /* Fundo das linhas ímpares */
        }

        .table tbody tr:hover {
            background-color: #f0f0f0;
            /* Fundo ao passar o mouse */
        }

        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }

        .filters select,
        .filters input {
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

        /* Responsividade para tabelas */
        .table-responsive {
            overflow-x: auto;
            /* Adiciona rolagem horizontal */
            -webkit-overflow-scrolling: touch;
            /* Suaviza a rolagem em dispositivos móveis */
        }

        /* Ajusta os filtros em dispositivos menores */
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
            }

            .filters .form-control {
                margin-bottom: 10px;
            }

            .btn-danger {
                width: 100%;
                /* Botão ocupará toda a largura */
            }
        }

        /* Estilo da Paginação */
        .pagination {
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .pagination .page-item .page-link {
            color: #343a40;
            /* Preto */
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 5px;
            margin: 0 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination .page-item .page-link:hover {
            background-color: #f7d547;
            /* Amarelo */
            color: #fff;
            /* Branco */
        }

        .pagination .page-item.active .page-link {
            background-color: #f7d547;
            /* Amarelo */
            color: #fff;
            /* Branco */
            border-color: #f7d547;
            /* Borda Amarela */
        }

        /* Feedback visual para status */
        .status-cancelado {
            color: red;
            font-weight: 500;
            font-size: 1.0rem;
            display: inline-flex;
            /* Alinhamento horizontal */
            align-items: center;
            /* Alinha ícone e texto verticalmente */
            gap: 5px;
            /* Espaço entre o texto e o ícone */
        }

        .status-reservado {
            color: #17a2b8;
            /* Azul claro */
            font-weight: 500;
            font-size: 1.0rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-vencer {
            color: #28a745;
            /* Verde */
            font-weight: 500;
            font-size: 1.0rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-vencido {
            color: #ffc107;
            /* Amarelo */
            font-weight: 500;
            font-size: 1.0rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Ajusta alinhamento para manter a consistência */
        .table .status {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<div class="container mt-5">
    <h2 class="mb-4">Histórico Completo de Reservas</h2>

    <!-- Botão para Limpar Reservas Canceladas -->
    <button id="btn-limpar-canceladas" class="btn btn-danger mb-4">
        <i class="fas fa-trash-alt"></i> Limpar Reservas Canceladas
    </button>

    <!-- Botão para Confirmar Retirada -->
    <button id="btn-confirmar-retirada" class="btn btn-success mb-4">
        <i class="fas fa-check-circle"></i> Confirmar Retirada
    </button>

    <!-- Filtros de Reservas -->
    <form method="POST" class="filters mb-4 d-flex flex-wrap justify-content-between">
        <input type="text" name="filtro_aluno" class="form-control mb-2" placeholder="Filtrar por Aluno"
            value="<?= htmlspecialchars($filtro_aluno) ?>">
        <input type="date" name="filtro_data" class="form-control mb-2" placeholder="Filtrar por Data"
            value="<?= htmlspecialchars($filtro_data) ?>">
        <select name="filtro_status" class="form-control mb-2">
            <option value="">Todos os Status</option>
            <option value="reservado" <?= $filtro_status == 'reservado' ? 'selected' : '' ?>>Reservado</option>
            <option value="cancelado" <?= $filtro_status == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
            <option value="vencer" <?= $filtro_status == 'vencer' ? 'selected' : '' ?>>A Vencer</option>
            <option value="vencido" <?= $filtro_status == 'vencido' ? 'selected' : '' ?>>Vencido</option>
        </select>
        <button type="submit" class="btn btn-primary mb-2">Aplicar Filtros</button>
        <button type="button" id="btn-remover-filtros" class="btn btn-secondary mb-2">Remover Filtros</button>
    </form>

    <!-- Tabela de Reservas -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Livro</th>
                    <th>Aluno</th>
                    <th>RM</th>
                    <th>Data da Reserva</th>
                    <th>Data da Retirada</th>
                    <th>Status de Devolução</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <?php
                    $data_reserva = new DateTime($reserva['data_reserva']);
                    $prazo_retirada = clone $data_reserva;
                    $prazo_retirada->modify('+2 days'); // Prazo de retirada
                
                    $prazo_devolucao = null;
                    $status_devolucao = '';
                    $class_status = '';
                    $status_texto = ''; // Inicialização padrão
                
                    if ($reserva['status'] === 'cancelado') {
                        $class_status = 'status-cancelado';
                        $status_texto = "<span class='status-cancelado'><i class='fas fa-times-circle'></i> Cancelado</span>";
                        $status_devolucao = ''; // Devolução vazia para cancelado
                    } elseif ($reserva['status'] === 'reservado') {
                        $class_status = 'status-reservado';
                        $status_texto = "<span class='status-reservado'><i class='fas fa-check-circle'></i> Reservado</span>";
                        $status_devolucao = '-';
                    } elseif ($reserva['status'] === 'retirado' || empty($reserva['status'])) {
                        // Considera o status 'retirado' ou um status vazio/indefinido como 'retirado'
                        $data_retirada = new DateTime($reserva['data_retirada']);
                        $prazo_devolucao = clone $data_retirada;
                        $prazo_devolucao->modify('+7 days'); // Prazo de devolução
                
                        $hoje = new DateTime();
                        if ($hoje > $prazo_devolucao) {
                            $atraso = $prazo_devolucao->diff($hoje)->days;
                            $class_status = 'status-vencido';
                            $status_texto = "<span class='status-vencido'><i class='fas fa-exclamation-circle'></i> Atrasado</span>";
                            $status_devolucao = "<span class='status-vencido'><i class='fas fa-clock'></i> Atrasado há $atraso dias</span>";
                        } else {
                            $dias_restantes = $hoje->diff($prazo_devolucao)->days;
                            $class_status = 'status-vencer';
                            $status_texto = "<span class='status-vencer'><i class='fas fa-clock'></i> No prazo</span>";
                            $status_devolucao = "Vence em $dias_restantes dias";
                        }
                    } else {
                        // Caso algum status inesperado seja recebido, trata como 'retirado'
                        $data_retirada = new DateTime($reserva['data_reserva']);
                        $prazo_devolucao = clone $data_retirada;
                        $prazo_devolucao->modify('+7 days'); // Prazo de devolução
                
                        $hoje = new DateTime();
                        if ($hoje > $prazo_devolucao) {
                            $atraso = $prazo_devolucao->diff($hoje)->days;
                            $class_status = 'status-vencido';
                            $status_texto = "<span class='status-vencido'><i class='fas fa-exclamation-circle'></i> Atrasado</span>";
                            $status_devolucao = "<span class='status-vencido'><i class='fas fa-clock'></i> Atrasado há $atraso dias</span>";
                        } else {
                            $dias_restantes = $hoje->diff($prazo_devolucao)->days;
                            $class_status = 'status-vencer';
                            $status_texto = "<span class='status-vencer'><i class='fas fa-clock'></i> A Vencer</span>";
                            $status_devolucao = "Vence em $dias_restantes dias";
                        }
                    }
                    ?>
                    <tr>
                        <td><input type="checkbox" class="select-reserva" data-id="<?= htmlspecialchars($reserva['id']) ?>">
                        </td>
                        <td><?= htmlspecialchars($reserva['livro_titulo']) ?></td>
                        <td><?= htmlspecialchars($reserva['aluno_nome']) ?></td>
                        <td><?= htmlspecialchars($reserva['aluno_rm']) ?></td>
                        <td><?= htmlspecialchars($reserva['data_reserva']) ?></td>
                        <td>
                            <?= !empty($reserva['data_retirada']) ? htmlspecialchars($reserva['data_retirada']) : '-' ?>
                        </td>
                        <td><?= $status_devolucao ?></td>
                        <td class="<?= htmlspecialchars($class_status) ?>"><?= $status_texto ?></td>
                        <td>
                            <?php if ($reserva['status'] === 'reservado'): ?>
                                <button class="btn btn-success btn-confirmar" data-id="<?= htmlspecialchars($reserva['id']) ?>">
                                    Confirmar Retirada
                                </button>
                            <?php endif; ?>
                            <?php if ($reserva['status'] === 'retirado'): ?>
                                <button class="btn btn-primary btn-devolver" data-id="<?= htmlspecialchars($reserva['id']) ?>">
                                    Devolver
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <!-- Paginação -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $i == $pagina_atual ? 'active' : '' ?>">
                    <a class="page-link"
                        href="admin_dashboard.php?p=consultar_reservas&pagina=<?= $i ?>&filtro_aluno=<?= urlencode($filtro_aluno) ?>&filtro_status=<?= urlencode($filtro_status) ?>&filtro_data=<?= urlencode($filtro_data) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>



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
    <script>
        // Função para excluir reservas selecionadas
        document.getElementById("btn-limpar-canceladas").addEventListener("click", function () {
            // Coletar IDs das reservas selecionadas
            const idsSelecionados = Array.from(document.querySelectorAll(".select-reserva:checked"))
                .map(input => input.dataset.id); // Obtemos os IDs das reservas a partir do atributo data-id

            if (!idsSelecionados || idsSelecionados.length === 0) {
                Swal.fire("Atenção!", "Nenhuma reserva selecionada.", "warning");
                return;
            }

            Swal.fire({
                title: "Tem certeza?",
                text: "As reservas selecionadas serão excluídas permanentemente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e65a50",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Sim, excluir!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    const payload = `acao=limpar_canceladas&reserva_ids=${JSON.stringify(idsSelecionados)}`;

                    fetch("consultar_reservas.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: payload
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Erro na resposta do servidor: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: data.status === "success" ? "success" : "error",
                                title: data.status === "success" ? "Sucesso!" : "Erro!",
                                text: data.mensagem
                            });

                            if (data.status === "success") {
                                setTimeout(() => location.reload(), 2000);
                            }
                        })
                        .catch(error => {
                            console.error("Erro na solicitação:", error);
                            Swal.fire("Erro!", "Ocorreu um erro inesperado.", "error");
                        });
                }
            });
        });


        // Função para confirmar retirada
        document.getElementById("btn-confirmar-retirada").addEventListener("click", function () {
            const idsSelecionados = Array.from(document.querySelectorAll(".select-reserva:checked"))
                .map(input => input.dataset.id);

            if (!idsSelecionados || idsSelecionados.length === 0) {
                Swal.fire("Atenção!", "Nenhuma reserva selecionada.", "warning");
                return;
            }

            Swal.fire({
                title: "Confirmar retiradas?",
                text: "Essa ação irá alterar o status das reservas selecionadas.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, confirmar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    const payload = `acao=confirmar_retirada&reserva_ids=${JSON.stringify(idsSelecionados)}`;

                    fetch("consultar_reservas.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: payload
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Erro na resposta do servidor: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === "success") {
                                // Atualize a Data da Retirada no DOM
                                const dataAtual = new Date().toISOString().split("T")[0]; // Data no formato YYYY-MM-DD
                                idsSelecionados.forEach(id => {
                                    const row = document.querySelector(`.select-reserva[data-id="${id}"]`).closest("tr");
                                    const cellDataRetirada = row.querySelector("td:nth-child(6)"); // Coluna "Data da Retirada"
                                    cellDataRetirada.textContent = dataAtual; // Atualiza com a data atual
                                });

                                Swal.fire({
                                    icon: "success",
                                    title: "Sucesso!",
                                    text: data.mensagem
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Erro!",
                                    text: data.mensagem
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Erro na solicitação:", error);
                            Swal.fire("Erro!", "Ocorreu um erro inesperado.", "error");
                        });
                }
            });
        });

        document.querySelectorAll(".btn-confirmar").forEach(button => {
            button.addEventListener("click", function () {
                const reservaId = this.dataset.id; // Obter o ID da reserva do atributo data-id

                Swal.fire({
                    title: "Confirmar retirada?",
                    text: "Essa ação irá alterar o status desta reserva para 'No prazo'.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sim, confirmar!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const payload = `acao=confirmar_retirada&reserva_ids=${JSON.stringify([reservaId])}`;

                        fetch("consultar_reservas.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: payload
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Erro na resposta do servidor: ${response.statusText}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    icon: data.status === "success" ? "success" : "error",
                                    title: data.status === "success" ? "Sucesso!" : "Erro!",
                                    text: data.mensagem
                                });

                                if (data.status === "success") {
                                    setTimeout(() => location.reload(), 2000);
                                }
                            })
                            .catch(error => {
                                console.error("Erro na solicitação:", error);
                                Swal.fire("Erro!", "Ocorreu um erro inesperado.", "error");
                            });
                    }
                });
            });
        });

        // Função para limpar filtros e recarregar a página
        document.getElementById("btn-remover-filtros").addEventListener("click", function () {
            // Limpar os campos de filtro
            document.querySelector("input[name='filtro_aluno']").value = "";
            document.querySelector("input[name='filtro_data']").value = "";
            document.querySelector("select[name='filtro_status']").value = "";

            // Recarregar a página sem parâmetros de filtro
            const urlSemFiltros = window.location.pathname + "?p=consultar_reservas";
            window.location.href = urlSemFiltros;
        });

        document.querySelectorAll(".btn-devolver").forEach(button => {
            button.addEventListener("click", function () {
                const reservaId = this.dataset.id;

                Swal.fire({
                    title: "Confirmar devolução?",
                    text: "Essa ação irá registrar a devolução deste livro.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sim, devolver!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const payload = `acao=confirmar_devolucao&reserva_ids=${JSON.stringify([reservaId])}`;

                        fetch("consultar_reservas.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: payload
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Erro na resposta do servidor: ${response.statusText}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    icon: data.status === "success" ? "success" : "error",
                                    title: data.status === "success" ? "Sucesso!" : "Erro!",
                                    text: data.mensagem
                                });

                                if (data.status === "success") {
                                    setTimeout(() => location.reload(), 2000);
                                }
                            })
                            .catch(error => {
                                console.error("Erro na solicitação:", error);
                                Swal.fire("Erro!", "Ocorreu um erro inesperado.", "error");
                            });
                    }
                });
            });
        });


    </script>
    </body>

</html>