<?php
require_once 'class/livros.php';

// Cria uma instância da classe Livro
$livro = new Livro();

// Obtém a conexão
$conn = $livro->getConnection();

// Obtém o termo de busca e a categoria, se houver
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Prepara a consulta com base na busca e na categoria
$query = "SELECT * FROM livros WHERE 1=1"; // '1=1' é uma forma de garantir que a cláusula WHERE sempre exista

if ($search) {
    $query .= " AND (id LIKE :search OR isbn LIKE :search OR titulo LIKE :search)";
}

if (!empty($categoria)) {
    $query .= " AND categoria = :categoria";
}

$stmt = $conn->prepare($query);

// Bind dos parâmetros
if ($search) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}

if (!empty($categoria)) {
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
}

$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Livros</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th,
        .table td {
            text-align: center;
        }

        .table td a {
            margin: 0 5px;
        }

        /* Espaçamento entre botões dentro de btn-group */
        .btn-group .btn {
            margin-right: 5px;
            /* Espaçamento entre os botões */
        }

        /* Remover margem do último botão */
        .btn-group .btn:last-child {
            margin-right: 0;
            /* Remove margem do último botão */
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Consulta de Livros</h1>
        <!-- Formulário de Busca -->
        <form method="GET" action="admin_dashboard.php">
            <input type="hidden" name="p" value="consultar_livros">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar por ID, ISBN ou Título"
                    value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary mx-1" type="submit">Buscar</button>
            </div>
            <div class="mb-3">
                <label for="searchCategoria" class="form-label">Buscar por Categoria</label>
                <select class="form-select" id="searchCategoria" name="categoria">
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
            </div>
        </form>

        <!-- Tabela de Resultados -->
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
</body>

</html>