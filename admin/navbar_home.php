<?php
ob_start();
@session_start();
require_once 'includes/db_connect.php';

// Verifica se o aluno está logado
if (isset($_SESSION['aluno_id'])) {
  $aluno_id = $_SESSION['aluno_id'];

  // Caso a foto não esteja salva na sessão, busca no banco
  if (!isset($_SESSION['aluno_foto'])) {
    try {
      $conn = Conectar::getInstance();
      $stmt = $conn->prepare("SELECT foto FROM alunos WHERE id = :id");
      $stmt->bindParam(':id', $aluno_id, PDO::PARAM_INT);
      $stmt->execute();
      $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($aluno && !empty($aluno['foto'])) {
        $fotoAluno = $aluno['foto']; // Caminho relativo salvo no banco
      } else {
        $fotoAluno = '../img/icon.png'; // Imagem padrão
      }
      // Salva o caminho na sessão para evitar consultas repetidas
      $_SESSION['aluno_foto'] = $fotoAluno;
    } catch (PDOException $e) {
      echo "<p class='error-message'>Erro ao buscar a foto: " . $e->getMessage() . "</p>";
      $fotoAluno = 'img/icon.png';
    }
  } else {
    $fotoAluno = $_SESSION['aluno_foto'];
  }
} else {
  $fotoAluno = '../img/icon.png';
}

// Corrige o caminho para garantir que inclua "controle/"

?>
<nav class="navbar navbar-expand-lg" style="background-color: #f7d547;">
  <div class="container-fluid">
    <a class="navbar-brand" href="?p=index">
      <img src="img/logo2.png" alt="" width="70px" height="50px">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar"
      aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="offcanvas offcanvas-end" style="background-color: #f7d547;" tabindex="-1" id="offcanvasDarkNavbar"
      aria-labelledby="offcanvasDarkNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Book Stack</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav flex-grow-1 pe-3">
          <li class="nav-item dropdown mt-1 mx-3 text-center justify-content-start">
            <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-list"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                  d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
              </svg>
            </a>
            <ul class="dropdown-menu px-3 py-2"
              style="background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); min-width: 220px;">
              <div class="row">
                <div class="col-6">
                  <li><a class="dropdown-item" href="?categoria=Romance&scroll=livros-disponiveis" onclick="filterAndScroll('Romance')">Romance</a></li>
                  <li><a class="dropdown-item" href="?categoria=Drama&scroll=livros-disponiveis" onclick="filterAndScroll('Drama')">Drama</a></li>
                  <li><a class="dropdown-item" href="?categoria=Novela&scroll=livros-disponiveis" onclick="filterAndScroll('Novela')">Novela</a></li>
                  <li><a class="dropdown-item" href="?categoria=Conto&scroll=livros-disponiveis" onclick="filterAndScroll('Conto')">Conto</a></li>
                  <li><a class="dropdown-item" href="?categoria=Crônica&scroll=livros-disponiveis" onclick="filterAndScroll('Crônica')">Crônica</a></li>
                </div>
                <div class="col-6">
                  <li><a class="dropdown-item" href="?categoria=Poesia&scroll=livros-disponiveis" onclick="filterAndScroll('Poesia')">Poesia</a></li>
                  <li><a class="dropdown-item" href="?categoria=Ficção&scroll=livros-disponiveis" onclick="filterAndScroll('Ficção')">Ficção</a></li>
                  <li><a class="dropdown-item" href="?categoria=Aventura&scroll=livros-disponiveis" onclick="filterAndScroll('Aventura')">Aventura</a></li>
                  <li><a class="dropdown-item" href="?categoria=HQ&scroll=livros-disponiveis" onclick="filterAndScroll('HQ')">HQ</a></li>
                  <li><a class="dropdown-item" href="?categoria=Terror&scroll=livros-disponiveis" onclick="filterAndScroll('Terror')">Terror</a></li>
                </div>
              </div>
              <hr class="dropdown-divider">
              <li><a class="dropdown-item" href="#" onclick="clearFilters()">Limpar Filtros</a></li>
            </ul>
          </li>
          <br>
          <li class="mx-auto col-4 justify-content-center" style="min-width: 300px; position: relative;">
            <form class="d-flex mt-2 align-items-center" role="search" autocomplete="off">
              <input id="searchInput" class="form-control me-2" style="border-radius: 50px;" type="search"
                placeholder="Buscar" aria-label="Search">
              <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="dark" class="bi bi-search"
                  viewBox="0 0 16 16">
                  <path
                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg>
              </a>
            </form>
            <ul id="searchResults" class="list-group position-absolute" style="width: 100%; z-index: 1000;"></ul>
            <!-- Lista para resultados -->
          </li>
        </ul>

        <div class="mx-3 d-flex mt-2 justify-content-between align-items-center">
          <a href="?p=livrosfav" class="nav-link mx-2 d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-heart"
              viewBox="0 0 16 16">
              <path
                d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
            </svg>
            <p class="my-auto ms-2 text-dark">Favoritos</p>
          </a>

          <a href="?p=login" class="nav-link mx-2 d-flex align-items-center">
            <img src="<?php echo htmlspecialchars('controle/' . $fotoAluno); ?>" alt="Foto do Aluno"
              class="rounded-circle" width="30" height="30" style="object-fit: cover;">
            <p class="my-auto ms-2 text-dark">Conta</p>
          </a>

        </div>
      </div>
    </div>
</nav>

<style>
  #searchResults {
    background-color: white;
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
    display: none;
    /* Começa oculto */
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 5px;
    position: absolute;
    z-index: 1000;
    width: 100%;
  }

  #searchResults li {
    padding: 10px;
    cursor: pointer;
    font-size: 16px;
    border-bottom: 1px solid #f1f1f1;
  }

  #searchResults li:last-child {
    border-bottom: none;
  }

  #searchResults li:hover {
    background-color: #f7d547;
    color: black;
  }

  #searchInput {
    border-radius: 50px;
    padding: 10px 20px;
    width: 100%;
    box-shadow: none;
    border: 1px solid #ccc;
    transition: all 0.3s ease;
  }

  #searchInput:focus {
    border-color: #f7d547;
    outline: none;
    box-shadow: 0 0 5px rgba(247, 213, 71, 0.5);
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function () {
      const query = this.value;

      if (query.length > 2) { // Começa a pesquisar após 3 caracteres
        // Faz a requisição AJAX
        fetch(`controle/class/search.php?q=${encodeURIComponent(query)}`)
          .then(response => response.json())
          .then(data => {
            searchResults.innerHTML = ''; // Limpa os resultados anteriores

            if (data.length > 0) {
              searchResults.style.display = 'block'; // Exibe a lista de resultados

              data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.titulo; // Adapta para o campo correto (por exemplo, título do livro)
                li.classList.add('list-group-item');

                li.addEventListener('click', () => {
                  window.location.href = `livros/livrosinfo.php?id=${item.id}`; // Redireciona para a página do item
                });

                searchResults.appendChild(li);
              });
            } else {
              searchResults.style.display = 'none'; // Oculta se não houver resultados
            }
          })
          .catch(err => console.error(err));
      } else {
        searchResults.style.display = 'none'; // Oculta a lista se a consulta for muito curta
      }
    });

    // Ocultar resultados quando clicar fora da barra de pesquisa
    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target)) {
        searchResults.style.display = 'none';
      }
    });
  });

  function filterByCategory(category) {
    window.location.href = `?categoria=${category}`;
  }

  function clearFilters() {
    window.location.href = '?p=index'; // Exemplo de como limpar os filtros redirecionando para a página inicial
  }

  function filterAndScroll(category) {
    // Atualiza a URL com o filtro
    window.location.href = `?categoria=${category}`;

    // Aguarda o carregamento da nova página e rola para a seção "Livros Disponíveis"
    setTimeout(() => {
      const section = document.querySelector('#livros-disponiveis'); // ID da seção "Livros Disponíveis"
      if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
      }
    }, 500);
  }

</script>