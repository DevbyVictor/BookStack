<?php
session_start(); // Inicia a sessão se ainda não estiver iniciada
// Verifica se o usuário está autenticado
if (!isset($_SESSION['acesso']) || $_SESSION['acesso'] != 'b8d66a4634503dcf530ce1b3704ca5dfae3d34bb') {
    header('location: logout.php');
    exit; // Encerra o script se não estiver autenticado
}
require_once 'class/Page.php';
require_once 'class/Navbar.php';
require_once 'class/Sidebar.php';
require_once 'class/Dashboard.php';
$dashboard = new Dashboard();
$dashboard->render();
?>

