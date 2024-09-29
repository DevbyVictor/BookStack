<?php
// Inclui o arquivo de conexão com o banco de dados
session_start();
include('../includes/db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['aluno_id'])) {
    die('Faça login para reservar o livro.');
}

$baseUploadPath = 'http://localhost/bookstack/admin/controle/'; // Ajuste conforme o caminho correto no seu sistema

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <th scope="row">Exemplares Disponíveis</th>
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
          <button onclick="reservarLivro(<?php echo htmlspecialchars($livro['id']); ?>)" class="btn btn-outline-warning btn-lg" style="border-radius: 50px;">Reservar</button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
    function reservarLivro(livro_id) {
        Swal.fire({
            title: 'Confirmação de Reserva',
            text: "Tem certeza que deseja reservar este livro?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, reservar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Faz a requisição Ajax para reservar o livro
                $.ajax({
                    url: '../controle/reservar.php', // URL para o arquivo PHP de reserva
                    type: 'POST',
                    data: { livro_id: livro_id }, // Envia o ID do livro
                    success: function (response) {
                        // Verifica a resposta do servidor
                        if (response === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Reservado com sucesso!',
                                text: 'O livro foi reservado com sucesso.',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Atualizar a página ou o botão de reserva após o sucesso
                            setTimeout(function () {
                                location.reload(); // Atualiza a página após a reserva
                            }, 2000);
                        } else if (response === 'already_reserved') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Já Reservado!',
                                text: 'Você já reservou este livro.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else if (response === 'not_logged_in') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Você precisa estar logado para reservar o livro.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Houve um erro ao reservar o livro. Tente novamente.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Houve um erro ao reservar o livro. Tente novamente.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    }
</script>

</body>
</html>
