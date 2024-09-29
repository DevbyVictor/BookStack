<?php
ob_start();

class Sidebar {
    public function render() {
        // Verifica se o administrador está logado
        if (!isset($_SESSION['admin_id'])) {
            header('Location: login.php');
            exit();
        }

        // Função para obter informações do administrador
        function getAdminInfo($admin_id) {
            $conn = Conectar::getInstance();
            $stmt = $conn->prepare('SELECT foto, nome, email FROM adminstradores WHERE id = :id');
            $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Obter informações do administrador
        $admin_id = $_SESSION['admin_id'];
        $admin_info = getAdminInfo($admin_id);

        // Renderizar a sidebar com as informações do administrador
        echo <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <style>
        /* Estilo geral da sidebar */
        <style>
    /* Estilo geral da sidebar */
    .sidebar {
        background-color: #333; /* Preto fosco para o fundo */
        color: #f8f9fa; /* Texto branco suave */
        height: 100%; /* Garante que a sidebar preencha o espaço */
        min-height: 100vh; /* Garante que a sidebar preencha a tela */
        padding: 20px 15px; /* Ajuste de padding para a sidebar */
        border-right: 1px solid #444; /* Borda lateral cinza escuro */
        transition: all 0.3s ease;
        position: sticky; /* Fixa a sidebar ao rolar a página */
        top: 0; /* Posiciona a sidebar no topo */
        display: flex; /* Flexbox para o layout */
        flex-direction: column; /* Organiza o conteúdo em coluna */
        justify-content: space-between; /* Espaçamento entre o conteúdo */
    }

    /* Seção de informações do administrador */
    .admin-info {
        background-color: #444; /* Fundo cinza escuro */
        color: #f8f9fa; /* Texto branco suave */
        border-radius: 8px;
        padding: 10px;
        display: flex;
        align-items: center;
        margin-bottom: 20px; /* Espaçamento inferior */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
    }

    .admin-photo {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        margin-right: 10px;
        border: 2px solid #f8f9fa; /* Borda branca ao redor da foto */
    }

    .admin-details h6 {
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 14px; /* Tamanho de fonte menor */
        color: #f8f9fa; /* Branco suave */
    }

    .admin-details small {
        color: #ddd; /* Texto cinza claro */
        font-size: 12px; /* Tamanho de fonte menor */
    }

    .online-status {
        font-size: 12px;
        color: #28a745; /* Verde */
    }

    /* Estilo do menu accordion */
    .accordion-button {
        background-color: #f8f9fa; /* Cabeçalho do accordion em branco */
        color: #333; /* Texto preto fosco */
        border: 1px solid #ddd; /* Borda cinza clara */
        border-radius: 5px;
        font-weight: 500;
        padding: 8px 15px; /* Ajuste de padding */
        font-size: 14px; /* Tamanho de fonte menor */
        transition: background-color 0.3s ease, border 0.3s ease;
        margin-bottom: 5px; /* Espaçamento inferior entre botões */
    }

    .accordion-button:hover {
        background-color: #e6e6e6; /* Cinza claro ao passar o mouse */
        border-color: #ccc; /* Borda cinza mais escuro */
    }

    .accordion-button:not(.collapsed) {
        background-color: #e6e6e6; /* Fundo cinza claro ao expandir */
        color: #333; /* Texto preto fosco */
    }

    .accordion-header {
        margin-bottom: 0.5rem; /* Espaçamento entre os headers */
    }

    .accordion-body {
        background-color: #fff; /* Fundo branco para o corpo do accordion */
        color: #333; /* Texto preto fosco */
        border-left: 3px solid #f7d547; /* Borda amarela à esquerda */
        padding: 10px 15px; /* Padding ajustado */
        font-size: 13px; /* Tamanho de fonte menor */
        border-radius: 5px; /* Bordas arredondadas */
    }

    /* Estilo dos links do accordion */
    .accordion-body .nav-link {
        color: #333; /* Preto fosco para os links */
        padding-left: 10px; /* Indentação dos links */
        font-size: 13px; /* Tamanho de fonte menor */
    }

    .accordion-body .nav-link:hover {
        color: #f7d547; /* Texto amarelo ao passar o mouse */
        background-color: #f0f0f0; /* Fundo claro ao passar o mouse */
        border-radius: 5px; /* Bordas arredondadas */
    }

    /* Estilo dos itens fora do accordion */
    .nav-item {
        margin: 0.5rem 0; /* Espaçamento entre itens */
        padding: 5px 0;
    }

    /* Estilo para estado ativo */
    .nav-item .nav-link.active {
        background-color: #f7d547; /* Fundo amarelo */
        color: #333; /* Texto preto fosco */
        border-radius: 5px;
        padding: 10px 15px;
    }
</style>
</head>
<body>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <!-- Seção de informações do administrador -->
        <div class="admin-info">
            <img src="{$admin_info['foto']}" alt="Foto do Administrador" class="admin-photo">
            <div class="admin-details">
                <h6>{$admin_info['nome']}</h6>
                <small>{$admin_info['email']}</small>
                <div class="online-status"><i class="fa fa-circle"></i> Online</div>
            </div>
        </div>
        
        <div class="accordion" id="accordionSidebar">
            <!-- Menu Accordion Atualizado -->
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
            <!-- Menu Accordion Livros -->
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
            <!-- Menu Accordion Alunos -->
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
            <!-- Menu Accordion Reservas -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingReservas">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReservas" aria-expanded="false" aria-controls="collapseReservas">
                        Reservas/Empréstimos
                    </button>
                </h2>
                <div id="collapseReservas" class="accordion-collapse collapse" aria-labelledby="headingReservas" data-bs-parent="#accordionSidebar">
                    <div class="accordion-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#">Cadastrar Reservas</a>
                            </li>
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

<!-- jQuery e Bootstrap JS para funcionamento do Accordion -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }
}
?>