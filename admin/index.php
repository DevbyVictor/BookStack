<?php
session_start();

// Verifica se o usuário já viu a página de apresentação
if (!isset($_SESSION['viewed_presentation']) && !isset($_SESSION['skipped_presentation'])) {
    $_SESSION['viewed_presentation'] = true;
    header("Location: index.php?p=presentation");
    exit();
}

// Verifica se o usuário optou por pular a apresentação
if (isset($_SESSION['skipped_presentation'])) {
    unset($_SESSION['viewed_presentation']); // Garante que a apresentação não será redirecionada
}

$pagina = isset($_GET['p']) ? $_GET['p'] : 'index'; // Default para página inicial
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">
    <title>TCC</title>
</head>

<body>
    <?php include_once './navbar_home.php'; ?>
    <div>
        <div class="col-md-12 col-sm-12">
            <?php
            if (empty($pagina) || $pagina == "index") {
                include_once './home.php';
            } else {
                if (file_exists($pagina . '.php')) {
                    include_once $pagina . '.php';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Erro 404, página não encontrada!</div>';
                }
            }
            ?>
        </div>
    </div>
    <?php include_once './plugins.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
