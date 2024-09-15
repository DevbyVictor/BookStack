<?php
require_once 'class/livros.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    $livro = new Livro();
    
    if ($livro->deleteLivro($id)) {
        header("Location: admin_dashboard.php?p=consultar_livros");
        exit;
    } else {
        echo "Erro ao excluir o livro.";
    }
}
?>
