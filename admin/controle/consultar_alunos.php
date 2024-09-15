<?php
require_once '../includes/db_connect.php'; // Inclui a conexão com o banco de dados

// Cria uma instância da classe Conectar
$conn = Conectar::getInstance();

// Obtém o termo de busca e a categoria, se houver
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepara a consulta com base na busca
$query = "SELECT * FROM alunos WHERE 1=1"; // '1=1' é uma forma de garantir que a cláusula WHERE sempre exista

if ($search) {
    $query .= " AND (rm LIKE :search OR nome LIKE :search OR email LIKE :search)";
}

$stmt = $conn->prepare($query);

// Bind dos parâmetros
if ($search) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}

$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Alunos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th,
        .table td {
            text-align: center;
        }

        .table td a {
            margin: 0 5px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Consulta de Alunos</h1>
        <!-- Formulário de Busca -->
        <form method="GET" action="consultar_alunos.php">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar por RM, Nome ou Email"
                    value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary mx-1" type="submit">Buscar</button>
            </div>
        </form>

        <!-- Tabela de Resultados -->
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
                                    <img src="<?php echo htmlspecialchars($aluno['foto']); ?>" alt="Foto do aluno" style="width: 50px; height: 50px;">
                                <?php else: ?>
                                    Sem foto
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar_aluno.php?rm=<?php echo htmlspecialchars($aluno['rm']); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="excluir_aluno.php?rm=<?php echo htmlspecialchars($aluno['rm']); ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</a>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
</body>

</html>
