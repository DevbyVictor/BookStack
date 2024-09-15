<?php
// Inclui o arquivo de conexão com o banco de dados
include('../includes/db_connect.php');

// Defina o caminho base correto onde suas imagens estão armazenadas
$baseUploadPath = 'http://localhost/bookstack2/admin/controle/'; // Ajuste conforme o caminho correto no seu sistema

// Obtém a instância da conexão
$conn = Conectar::getInstance();
// Verifica se o ID foi passado pela URL
if (isset($_GET['id'])) {
    $idLivro = $_GET['id'];

    // Prepara e executa a consulta para buscar o livro com base no ID
    $query = "SELECT * FROM livros WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idLivro, PDO::PARAM_INT);
    $stmt->execute();

    // Recupera os dados do livro
    $livro = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o livro foi encontrado
    if (!$livro) {
        die('Livro não encontrado.');
    }
} else {
    die('ID do livro não fornecido.');
}
?>

<!-- HTML para exibir as informações do livro -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($livro['titulo']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .livro-capa {
            max-height: 300px; /* Altura máxima */
            width: 100%; /* Largura completa */
            object-fit: contain; /* Garante que a imagem se ajuste sem ser cortada */
        }
    </style>
</head>
<body style="font-family: 'Montserrat', sans-serif;">

<div class="container mt-5">
  <div class="row g-4 justify-content-center align-items-center featurette">
    
    <!-- Coluna com as informações do livro -->
    <div class="col-md-7 order-md-2">
      <h2 class="featurette-heading"><?php echo htmlspecialchars($livro['titulo']); ?></h2>
      <small>Por <span class="text-danger"><?php echo htmlspecialchars($livro['autor']); ?></span></small>
      <p class="lead mt-3"><?php echo htmlspecialchars($livro['sinopse']); ?></p>
      
      <h3 class="featurette-heading mt-4">Características do Livro</h3>
      <table class="table table-striped mt-3">
        <tbody>
          <tr>
            <th scope="row">Autor</th>
            <td><?php echo htmlspecialchars($livro['autor']); ?></td>
          </tr>
          <tr>
            <th scope="row">Instituição</th>
            <td><?php echo htmlspecialchars($livro['instituicao']); ?></td>
          </tr>
          <tr>
            <th scope="row">Ano</th>
            <td><?php echo htmlspecialchars($livro['ano']); ?></td>
          </tr>
          <tr>
            <th scope="row">N° de páginas</th>
            <td><?php echo htmlspecialchars($livro['exemplares']); ?></td>
          </tr>
          <tr>
            <th scope="row">Categoria</th>
            <td><?php echo htmlspecialchars($livro['categoria']); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Coluna com a imagem do livro e botão de reserva -->
    <div class="col-md-5 order-md-1 d-flex justify-content-center">
      <div class="card shadow-sm col-lg-5 col-md-10 col-sm-12">
        <?php if (!empty($livro['capa'])): ?>
          <img src="<?php echo $baseUploadPath . $livro['capa']; ?>" class="card-img-top img-fluid livro-capa"
          alt="Imagem do Livro">
        <?php else: ?>
          <img src="controle/uploads/imagem-nao-disponivel.jpg" class="card-img-top img-fluid livro-capa"
          alt="Imagem não disponível">
        <?php endif; ?>
        <div class="card-body text-center">
          <a href="controle/reserva.php?id=<?php echo htmlspecialchars($livro['id']); ?>" class="btn btn-outline-warning btn-lg" style="border-radius: 50px;">Reservar</a>
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
