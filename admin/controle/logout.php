<?php
// Inclua o arquivo de conexão com o banco de dados e as funções necessárias
include_once '../includes/db_connect.php';
include_once 'class/auth.php';

// Inicie a sessão se ainda não estiver iniciada
session_start();

// Debug: Verifique o status da sessão
error_log("Sessão ativa: " . (session_status() === PHP_SESSION_ACTIVE ? "Sim" : "Não"));
error_log("Admin ID na sessão: " . (isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : "Não definido"));

// Verifique se o usuário está autenticado e obtenha o ID do administrador
if (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];

    // Define o administrador como offline
    if (!setOffline($adminId)) {
        echo "Falha ao definir o status como offline.";
    }
} else {
    echo "ID do administrador não encontrado na sessão.";
}

// Limpa a sessão e redireciona para a página de login
$_SESSION = array();
session_destroy();
header('Location: loginadm.php');
exit;
?>
