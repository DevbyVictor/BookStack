<?php
session_start(); // Adicione isso para garantir que a sessão esteja disponível

session_unset(); // Limpa todas as variáveis de sessão
session_destroy(); // Destroi a sessão

// Debug para verificar se a sessão foi destruída
echo "Sessão destruída.";

header('Location: index.php?p=login');
exit();
?>
