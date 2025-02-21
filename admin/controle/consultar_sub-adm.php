<?php
// Inclua o arquivo de conexão com o banco de dados
include_once '../includes/db_connect.php';
include_once 'class/auth.php';

// Verificar acesso para nível 1
verificarAcesso(1, 'exato');

// Obtém a instância da conexão PDO
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
$query = "SELECT * FROM adminstradores WHERE nivel_acesso <> 1"; // Inclui todos os sub-administradores

if ($search) {
    $query .= " AND (nome LIKE :search OR email LIKE :search)";
}

$query .= " LIMIT :offset, :limit";

$stmt = $conn->prepare($query);

// Bind dos parâmetros
if ($search) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $resultados_por_pagina, PDO::PARAM_INT);

$stmt->execute();
$sub_adms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter o número total de resultados sem limites de paginação
$query_total = "SELECT COUNT(*) AS total FROM adminstradores WHERE nivel_acesso <> 1";
if ($search) {
    $query_total .= " AND (nome LIKE :search OR email LIKE :search)";
}

$stmt_total = $conn->prepare($query_total);
if ($search) {
    $stmt_total->bindParam(':search', $searchTerm, PDO::PARAM_STR);
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
    <title>Consultar Sub-Administradores</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th {
            background-color: #f7d547;
            /* Amarelo */
            color: #212529;
            /* Preto */
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .pagination .page-item .page-link {
            color: #343a40;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 5px;
            margin: 0 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination .page-item .page-link:hover {
            background-color: #f7d547;
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background-color: #f7d547;
            color: #fff;
            border-color: #f7d547;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
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
        <h1 class="mb-4">Consulta de Sub-Administradores</h1>

        <!-- Filtros -->
        <form method="GET" action="admin_dashboard.php" class="filters">
            <input type="hidden" name="p" value="consultar_sub-adm">
            <input type="text" name="search" class="form-control" placeholder="Buscar por Nome ou Email"
                value="<?php echo htmlspecialchars($search); ?>">

            <button type="submit" class="btn btn-primary">Buscar</button>
            <button type="button" id="btn-remover-filtros" class="btn btn-secondary">Remover Filtros</button>
        </form>

        <!-- Tabela de Resultados -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Nível de Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sub_adms) > 0): ?>
                        <?php foreach ($sub_adms as $sub_adm): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sub_adm['id']); ?></td>
                                <td><?php echo htmlspecialchars($sub_adm['nome']); ?></td>
                                <td><?php echo htmlspecialchars($sub_adm['email']); ?></td>
                                <td><?php echo $sub_adm['status_online'] ? 'Online' : 'Offline'; ?></td>
                                <td><?php echo htmlspecialchars($sub_adm['nivel_acesso']); ?></td>
                                <td>
                                    <a href="admin_dashboard.php?p=editar_sub-adm&id=<?php echo htmlspecialchars($sub_adm['id']); ?>"
                                        class="btn btn-warning btn-sm">Editar</a>
                                    <a href="excluir_sub-adm.php?id=<?php echo htmlspecialchars($sub_adm['id']); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Tem certeza que deseja excluir este sub-administrador?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum sub-administrador encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina_atual ? 'active' : '' ?>">
                        <a class="page-link"
                            href="admin_dashboard.php?p=consultar_sub-adm&pagina=<?= $i ?>&search=<?= urlencode($search) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <script>
        // Função para limpar filtros e recarregar a página
        document.getElementById("btn-remover-filtros").addEventListener("click", function () {
            // Limpar os campos de filtro
            document.querySelector("input[name='search']").value = "";

            // Recarregar a página sem parâmetros de filtro
            const urlSemFiltros = window.location.pathname + "?p=consultar_sub-adm";
            window.location.href = urlSemFiltros;
        });
    </script>
</body>

</html>