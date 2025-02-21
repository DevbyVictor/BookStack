<?php
class Navbar
{
    public function render()
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstack Administrator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- FontAwesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Bookstack Administrator</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <!-- Botões dentro do menu hamburguer -->
            <ul class="navbar-nav">
                <li class="nav-item d-lg-none">
                    <button class="btn btn-outline-light btn-rounded me-2" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i> Menu
                    </button>
                </li>
                <li class="nav-item">
                    <a href="./logout.php" class="btn btn-outline-danger btn-rounded">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
// Fechar o menu ao clicar fora
document.addEventListener("click", function (event) {
    const navbarToggler = document.querySelector(".navbar-toggler");
    const navbarCollapse = document.getElementById("navbarNav");

    // Verifica se o clique está dentro do menu ou no botão
    const isClickInside = navbarCollapse.contains(event.target) || navbarToggler.contains(event.target);

    if (!isClickInside && navbarCollapse.classList.contains("show")) {
        navbarToggler.click(); // Fecha o menu hamburguer
    }
});

// Fecha o menu ao redimensionar a tela para tamanhos maiores
window.addEventListener("resize", function () {
    const navbarCollapse = document.getElementById("navbarNav");
    const navbarToggler = document.querySelector(".navbar-toggler");

    if (window.innerWidth > 992 && navbarCollapse.classList.contains("show")) {
        navbarToggler.click();
    }
});
</script>
</body>
</html>
HTML;
    }
}
ob_end_flush()
?>