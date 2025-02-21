<?php
// Inclui o arquivo da classe de conexão
include('../../includes/db_connect.php'); // Certifique-se de ajustar o caminho corretamente

// Cria uma instância da conexão
$conn = Conectar::getInstance();

// Verifica se há uma consulta
if (isset($_GET['q'])) {
    $query = $_GET['q'];
    
    // Prepara e executa a consulta no banco de dados
    $stmt = $conn->prepare("SELECT id, titulo FROM livros WHERE titulo LIKE :query LIMIT 5");
    $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
    $stmt->execute();
    
    // Obtém os resultados
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retorna os resultados como JSON
    echo json_encode($results);
}
?>
