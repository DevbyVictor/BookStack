<?php
include_once(__DIR__ . '/../../includes/db_connect.php');
class Dashboard
{
    private $page;
    private $navbar;
    private $sidebar;

    public function __construct()
    {
        $this->page = new Page();
        $this->navbar = new Navbar();
        $this->sidebar = new Sidebar();
    }

    public function render()
    {
        $this->page->renderHeader('Painel Administrativo');
        $this->navbar->render();
        echo '<div class="container-fluid"><div class="row">';
        $this->sidebar->render();
        echo '<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">';
        echo '<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">';
        echo '<h1 class="h2">Dashboard</h1></div>';

        // Inclui dinamicamente o conteúdo da página de acordo com o parâmetro ?p=
        if (isset($_GET['p'])) {
            $pagina = $_GET['p'];

            switch ($pagina) {

                case 'cad_sub-adm':
                    include_once 'cad_sub-adm.php';
                    break;
                case 'consultar_sub-adm':
                    include_once 'consultar_sub-adm.php';
                    break;
                case 'editar_sub-adm':
                    include_once 'editar_sub-adm.php';
                    break;
                case 'cad_alunos':
                    include_once 'cad_alunos.php';
                    break;
                case 'consultar_alunos':
                    include_once 'consultar_alunos.php';
                    break;
                case 'cad_livros':
                    include_once 'cad_livros.php';
                    break;
                case 'consultar_livros':
                    include_once 'consultar_livros.php';
                    break;
                case 'editar_livro':
                    include_once 'editar_livros.php';
                    break;
                case 'reservas':
                    include_once 'reservas.php';
                    break;
                case 'excluir_livro':
                    break;
                default:
                    echo '<p>Página não encontrada</p>';
                    break;
            }
        } else {
            echo '<p>Selecione uma opção no menu lateral</p>';
        }

        echo '</main></div></div>';
        $this->page->renderFooter();
    }
}