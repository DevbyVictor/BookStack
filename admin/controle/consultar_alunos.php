<?php
require_once '../includes/db_connect.php'; // Inclui a conexão com o banco de dados

// Cria uma instância da classe Conectar
$conn = Conectar::getInstance();

// Inicialize as variáveis de busca e paginação
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Configurações de paginação
$resultados_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina_atual < 1) {
    $pagina_atual = 1;
}
$offset = ($pagina_atual - 1) * $resultados_por_pagina;

// Prepara a consulta com base na busca
$query = "SELECT * FROM alunos WHERE 1=1";

$params = [];
if (!empty($search)) {
    $query .= " AND (rm LIKE :search OR nome LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " LIMIT :offset, :limit";

$stmt = $conn->prepare($query);

// Bind dos parâmetros
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $resultados_por_pagina, PDO::PARAM_INT);

$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter o número total de resultados sem limites de paginação
$query_total = "SELECT COUNT(*) AS total FROM alunos WHERE 1=1";

if (!empty($search)) {
    $query_total .= " AND (rm LIKE :search OR nome LIKE :search OR email LIKE :search)";
}

$stmt_total = $conn->prepare($query_total);
if (!empty($search)) {
    $stmt_total->bindValue(':search', $params[':search'], PDO::PARAM_STR);
}
$stmt_total->execute();
$total_resultados = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_resultados / $resultados_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Alunos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .table th {
            background-color: #f7d547;
            color: #212529;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filters input {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .filters .btn {
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters .form-control {
                margin-bottom: 10px;
            }

            .filters .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Consulta de Alunos</h1>

        <!-- Formulário de Busca -->
        <form method="GET" action="admin_dashboard.php" class="filters">
            <input type="hidden" name="p" value="consultar_alunos">

            <!-- Campo de Busca -->
            <div class="input-group flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Buscar por RM, Nome ou Email"
                    value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>

            <!-- Botão de Remover Filtros -->
            <button type="button" id="btn-remover-filtros" class="btn btn-secondary align-self-end">Remover
                Filtros</button>
        </form>

        <!-- Tabela de Resultados -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>RM</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Foto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($alunos) > 0): ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aluno['rm']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['telefone']); ?></td>
                                <td>
                                    <?php if ($aluno['foto']): ?>
                                        <img src="<?php echo htmlspecialchars($aluno['foto']); ?>" alt="Foto do aluno"
                                            style="width: 50px; height: 50px;">
                                    <?php else: ?>
                                        Sem foto
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="admin_dashboard.php?p=editar_alunos&rm=<?php echo htmlspecialchars($aluno['rm']); ?>"
                                        class="btn btn-warning btn-sm">Editar</a>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmarExclusao('<?php echo htmlspecialchars($aluno['rm']); ?>')">Excluir</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum aluno encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php if ($i == $pagina_atual)
                        echo 'active'; ?>">
                        <a class="page-link"
                            href="?p=consultar_alunos&search=<?php echo urlencode($search); ?>&pagina=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script>
        // Função para limpar filtros e recarregar a página
        document.getElementById("btn-remover-filtros").addEventListener("click", function () {
            const urlSemFiltros = window.location.pathname + "?p=consultar_alunos";
            window.location.href = urlSemFiltros;
        });

        function confirmarExclusao(rm) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Essa ação não pode ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `excluir_aluno.php?rm=${rm}`;
                }
            });
        }
    </script>
</body>

</html>