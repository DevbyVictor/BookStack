<?php
require_once 'class/livros.php';

// Cria uma instância da classe Livro
$livro = new Livro();

// Obtém a conexão
$conn = $livro->getConnection();

// Inicializa as variáveis de busca e filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Configurações de paginação
$resultados_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina_atual < 1) {
    $pagina_atual = 1;
}
$offset = ($pagina_atual - 1) * $resultados_por_pagina;

// Prepara a consulta com base na busca e nos filtros
$query = "SELECT * FROM livros WHERE 1=1";

if ($search) {
    $query .= " AND (id LIKE :search OR isbn LIKE :search OR titulo LIKE :search)";
}

if (!empty($categoria)) {
    $query .= " AND categoria = :categoria";
}

$query .= " LIMIT :offset, :limit";
$stmt = $conn->prepare($query);

// Bind dos parâmetros
if ($search) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}

if (!empty($categoria)) {
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $resultados_por_pagina, PDO::PARAM_INT);

$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter o número total de resultados sem limites de paginação
$query_total = "SELECT COUNT(*) AS total FROM livros WHERE 1=1";
if ($search) {
    $query_total .= " AND (id LIKE :search OR isbn LIKE :search OR titulo LIKE :search)";
}
if (!empty($categoria)) {
    $query_total .= " AND categoria = :categoria";
}

$stmt_total = $conn->prepare($query_total);
if ($search) {
    $stmt_total->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}
if (!empty($categoria)) {
    $stmt_total->bindParam(':categoria', $categoria, PDO::PARAM_STR);
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
    <title>Consultar Livros</title>
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

        .filters select,
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
        <h1 class="mb-4">Consulta de Livros</h1>

        <!-- Filtros -->
        <form method="GET" action="admin_dashboard.php" class="filters">
            <input type="hidden" name="p" value="consultar_livros">
            <input type="text" name="search" class="form-control" placeholder="Buscar por ID, ISBN ou Título"
                value="<?php echo htmlspecialchars($search); ?>">

            <select name="categoria" class="form-select">
                <option value="">Todas as categorias</option>
                <option value="Romance" <?php if ($categoria == 'Romance')
                    echo 'selected'; ?>>Romance</option>
                <option value="Drama" <?php if ($categoria == 'Drama')
                    echo 'selected'; ?>>Drama</option>
                <option value="Novela" <?php if ($categoria == 'Novela')
                    echo 'selected'; ?>>Novela</option>
                <option value="Conto" <?php if ($categoria == 'Conto')
                    echo 'selected'; ?>>Conto</option>
                <option value="Crônica" <?php if ($categoria == 'Crônica')
                    echo 'selected'; ?>>Crônica</option>
                <option value="Poesia" <?php if ($categoria == 'Poesia')
                    echo 'selected'; ?>>Poesia</option>
                <option value="Ficção" <?php if ($categoria == 'Ficção')
                    echo 'selected'; ?>>Ficção</option>
                <option value="Aventura" <?php if ($categoria == 'Aventura')
                    echo 'selected'; ?>>Aventura</option>
                <option value="HQ" <?php if ($categoria == 'HQ')
                    echo 'selected'; ?>>HQ</option>
                <option value="Terror" <?php if ($categoria == 'Terror')
                    echo 'selected'; ?>>Terror</option>
            </select>

            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            <button type="button" id="btn-remover-filtros" class="btn btn-secondary">Remover Filtros</button>
        </form>

        <!-- Tabela de Resultados -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Instituição</th>
                        <th>Ano</th>
                        <th>Categoria</th>
                        <th>N° Exemplares</th>
                        <th>Condição</th>
                        <th>ISBN</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($livros) > 0): ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($livro['id']); ?></td>
                                <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                                <td><?php echo htmlspecialchars($livro['instituicao']); ?></td>
                                <td><?php echo htmlspecialchars($livro['ano']); ?></td>
                                <td><?php echo htmlspecialchars($livro['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($livro['exemplares']); ?></td>
                                <td><?php echo htmlspecialchars($livro['condicao']); ?></td>
                                <td><?php echo htmlspecialchars($livro['isbn']); ?></td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Ações">
                                        <a href="admin_dashboard.php?p=editar_livro&id=<?php echo htmlspecialchars($livro['id']); ?>"
                                            class="btn btn-warning btn-sm">Editar</a>
                                        <a href="excluir_livros.php?id=<?php echo htmlspecialchars($livro['id']); ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Nenhum livro encontrado.</td>
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
                            href="admin_dashboard.php?p=consultar_livros&pagina=<?= $i ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoria) ?>">
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
            document.querySelector("select[name='categoria']").value = "";

            // Recarregar a página sem parâmetros de filtro
            const urlSemFiltros = window.location.pathname + "?p=consultar_livros";
            window.location.href = urlSemFiltros;
        });
    </script>
</body>

</html>