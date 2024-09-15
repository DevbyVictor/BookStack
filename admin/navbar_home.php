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
            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-list"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                  d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
              </svg>
            </a>
            <ul class="dropdown-menu" style="background-color: #ffffff;">
              <div class="row">
                <li><a class="dropdown-item" href="#">Livros</a></li>
                <hr class="dropdown-divider">
                <div class="col-5">
                  <li><a class="dropdown-item" href="#">Romance</a></li>
                  <li><a class="dropdown-item" href="#">Drama</a></li>
                  <li><a class="dropdown-item" href="#">Novela</a></li>
                  <li><a class="dropdown-item" href="#">Conto</a></li>
                  <li><a class="dropdown-item" href="#">Crônica</a></li>
                </div>
                <div class="col-7">
                  <li><a class="dropdown-item" href="#">Poesia</a></li>
                  <li><a class="dropdown-item" href="#">Ficção</a></li>
                  <li><a class="dropdown-item" href="#">Aventura</a></li>
                  <li><a class="dropdown-item" href="#">HQ</a></li>
                  <li><a class="dropdown-item" href="#">Terror</a></li>
                  <li>
                </div>
                <hr class="dropdown-divider">
              </div>
          </li>
          <li><a class="dropdown-item" href="#">Sobre o Book Stack</a></li>
        </ul>
        </li>
        <br>
        <li class="mx-auto col-4 justify-content-center" style="min-width: 300px;">
          <form class="d-flex mt-2 align-items-center" role="search">
            <input class="form-control me-2" style="border-radius: 50px;" type="search" placeholder="Search"
              aria-label="Search">
            <a href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="dark" class="bi bi-search"
                viewBox="0 0 16 16">
                <path
                  d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
              </svg>
            </a>
          </form>
        </li>
        </ul>
        <br>
        <div class="mx-3 d-flex mt-2 justify-content-between align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-heart"
            viewBox="0 0 16 16">
            <path
              d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
          </svg>
          <a href="?p=livrosfav" class="nav-link mx-2">
            <p class="my-auto text-dark">Favoritos</p>
          </a>

          <div class="mx-3"></div>
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-person-circle"
            viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
            <path fill-rule="evenodd"
              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
          </svg>
          <a href="?p=login" class="nav-link mx-2">
            <p class="my-auto text-dark">Conta</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>