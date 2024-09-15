<?php
// Inclua o arquivo de conexão com o banco de dados
include_once '../includes/db_connect.php';
include_once 'class/auth.php';

// Verificar acesso para nível 1
verificarAcesso(1, 'exato');

// Obtém a instância da conexão PDO
$conn = Conectar::getInstance();

// Obtém o termo de busca, se houver
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepara a consulta com base na busca
$query = "SELECT * FROM adminstradores WHERE nivel_acesso <> 1"; // Inclui todos os sub-administradores

if ($search) {
    $query .= " AND (nome LIKE :search OR email LIKE :search)";
}

$stmt = $conn->prepare($query);

// Bind dos parâmetros
if ($search) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}

$stmt->execute();
$sub_adms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Sub-Administradores</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th,
        .table td {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Consulta de Sub-Administradores</h1>
        <!-- Formulário de Busca -->
        <form method="GET" action="admin_dashboard.php">
            <input type="hidden" name="p" value="consultar_sub-adm">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar por ID ou Email"
                    value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary mx-1" type="submit">Buscar</button>
            </div>
        </form>

        <!-- Tabela de Resultados -->
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
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
</body>

</html>
