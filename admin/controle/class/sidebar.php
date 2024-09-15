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
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <!-- Seção de informações do administrador -->
        <div class="admin-info d-flex align-items-center p-4 mb-3 bg-dark rounded">
            <img src="{$admin_info['foto']}" alt="Foto do Administrador" class="admin-photo me-2">
            <div class="admin-details p-2">
                <h6 class="mb-0">{$admin_info['nome']}</h6>
                <small>{$admin_info['email']}</small>
                <div class="online-status mt-1"><i class="fa fa-circle text-success"></i> Online</div>
            </div>
        </div>
        
        <div class="accordion" id="accordionSidebar">
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
                                <a class="nav-link" href="#">Cadastrar Reservas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?p=reservas">Consultar Reservas</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.admin-info {
    display: flex;
    align-items: center;
}
.admin-photo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}
.online-status {
    font-size: 0.9em;
    color: green;
}
</style>
HTML;
    }
}
?>
