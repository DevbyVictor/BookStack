<?php
ob_start();

class Sidebar
{
    public function render()
    {
        // Verifique se o usuário está logado antes de qualquer saída
        if (!isset($_SESSION['admin_id'])) {
            // Redireciona para a página de login
            header('Location: login.php');
            exit();
        }

        // Função para obter informações do administrador
        function getAdminInfo($admin_id)
        {
            $conn = Conectar::getInstance();
            $stmt = $conn->prepare('SELECT foto, nome, email FROM adminstradores WHERE id = :id');
            $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Obtém informações do administrador
        $admin_id = $_SESSION['admin_id'];
        $admin_info = getAdminInfo($admin_id);

        echo <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>

    <style>
    /* Estilo geral da sidebar */
    /* Sidebar */
.sidebar {
    background-color: #ffffff; /* Fundo branco */
    color: #333; /* Texto preto */
    height: 100%;
    min-height: 100vh;
    padding: 20px 15px;
    border-right: 1px solid #ccc; /* Borda cinza claro */
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    transform: translateX(-100%); /* Oculta a sidebar por padrão */
    transition: transform 0.3s ease;
    z-index: 1040;
}

.sidebar.show {
    transform: translateX(0); /* Exibe a sidebar */
}

@media (min-width: 768px) {
    .sidebar {
        transform: translateX(0); /* Mostra a sidebar por padrão em telas maiores */
        position: sticky;
    }
}

    /* Estilo da seção de informações do administrador */
.sidebar .admin-info {
    background-color: #444; /* Fundo cinza escuro */
    color: #f8f9fa; /* Texto branco suave */
    border-radius: 8px;
    padding: 10px;
    display: flex;
    align-items: center;
    margin-bottom: 20px; /* Espaçamento inferior */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
}

.sidebar .admin-photo {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid #f8f9fa; /* Borda branca ao redor da foto */
}
.sidebar .admin-details h6 {
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 14px;
    color: #f8f9fa; /* Branco suave */
}

.sidebar .admin-details small {
    color: #ddd; /* Texto cinza claro */
    font-size: 12px;
}

/* Accordion */
.sidebar .accordion-button {
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px 15px;
    font-size: 14px;
    transition: background-color 0.3s ease, border 0.3s ease;
}

.sidebar .accordion-button:hover {
    background-color: #e6e6e6; /* Cinza claro ao passar o mouse */
    border-color: #ccc;
}

.sidebar .accordion-button:not(.collapsed) {
    background-color: #e6e6e6;
    color: #333;
}

.sidebar .accordion-body {
    background-color: #fff;
    color: #333;
    border-left: 3px solid #f7d547;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 5px;
}

.sidebar .accordion-body .nav-link {
    color: #333;
    padding-left: 10px;
    font-size: 13px;
}

.sidebar .accordion-body .nav-link:hover {
    color: #f7d547; /* Texto amarelo ao passar o mouse */
    background-color: #f0f0f0;
    border-radius: 5px;
}

.sidebar .nav-item {
    margin: 0.5rem 0;
    padding: 5px 0;
}

.sidebar .nav-item .nav-link.active {
    background-color: #f7d547; /* Fundo amarelo */
    color: #333;
    border-radius: 5px;
    padding: 10px 15px;
}

.online-status{
    color: green;
}
</style>
</head>
<body>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="sidebar-sticky pt-3">
        <!-- Informações do Administrador -->
        <div class="admin-info">
            <img src="{$admin_info['foto']}" alt="Foto do Administrador" class="admin-photo">
            <div class="admin-details">
                <h6>{$admin_info['nome']}</h6>
                <small>{$admin_info['email']}</small>
                <div class="online-status"><i class="fa fa-circle"></i> Online</div>
            </div>
        </div>
        
        <!-- Menu Accordion -->
        <div class="accordion" id="accordionSidebar">
            <!-- Sub-Administradores -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDashboard">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDashboard" aria-expanded="false" aria-controls="collapseDashboard">
                        Sub-Administradores
                    </button>
                </h2>
                <div id="collapseDashboard" class="accordion-collapse collapse" aria-labelledby="headingDashboard" data-bs-parent="#accordionSidebar">
                    <div class="accordion-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="?p=cad_sub-adm">Cadastrar Sub-Adm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?p=consultar_sub-adm">Consultar Sub-Adm</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Livros -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingLivros">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLivros" aria-expanded="false" aria-controls="collapseLivros">
                        Livros
                    </button>
                </h2>
                <div id="collapseLivros" class="accordion-collapse collapse" aria-labelledby="headingLivros" data-bs-parent="#accordionSidebar">
                    <div class="accordion-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="?p=cad_livros">Cadastrar Livros</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?p=consultar_livros">Consultar Livros</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Alunos -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAlunos">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlunos" aria-expanded="false" aria-controls="collapseAlunos">
                        Alunos
                    </button>
                </h2>
                <div id="collapseAlunos" class="accordion-collapse collapse" aria-labelledby="headingAlunos" data-bs-parent="#accordionSidebar">
                    <div class="accordion-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="?p=cad_alunos">Cadastrar Alunos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?p=consultar_alunos">Consultar Alunos</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Reservas -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingReservas">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReservas" aria-expanded="false" aria-controls="collapseReservas">
                        Reservas
                    </button>
                </h2>
                <div id="collapseReservas" class="accordion-collapse collapse" aria-labelledby="headingReservas" data-bs-parent="#accordionSidebar">
                    <div class="accordion-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="?p=consultar_reservas">Consultar Reservas</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
HTML;
    }
}
ob_end_flush()
?>
<!-- jQuery e Bootstrap JS para funcionamento do Accordion -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebarMenu');
        sidebar.classList.toggle('show');
    }
</script>
</body>
</html>