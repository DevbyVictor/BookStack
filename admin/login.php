<?php

@session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rm = $_POST['rm'];
    $senha = $_POST['senha'];

    try {
        $conn = Conectar::getInstance();
        $stmt = $conn->prepare("SELECT id, rm, nome, senha FROM alunos WHERE rm = :rm");
        $stmt->bindParam(':rm', $rm);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($aluno && password_verify($senha, $aluno['senha'])) {
            // Login bem-sucedido, armazenar informações na sessão
            $_SESSION['aluno_id'] = $aluno['id'];
            $_SESSION['aluno_nome'] = $aluno['nome'];

            // Redirecionar para o dashboard do aluno
            header('Location: index.php');
            exit();
        } else {
            echo "<p class='error-message'>RM ou senha incorretos.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error-message'>Erro: " . $e->getMessage() . "</p>";
    }
}

// Exibe as informações do aluno e o formulário de login
if (isset($_SESSION['aluno_id'])) {
    $aluno_id = $_SESSION['aluno_id'];

    try {
        $conn = Conectar::getInstance();
        $stmt = $conn->prepare("SELECT rm, nome, email, telefone, foto FROM alunos WHERE id = :id");
        $stmt->bindParam(':id', $aluno_id);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($aluno) {
            echo "
<div class='container'>
    <div class='header'>
        <div class='title'>
            <span class='titletwo p-1 mx-1'>Olá, <span>
            <span class='aluno-nome'> " . htmlspecialchars($aluno['nome']) . "!</span>
        </div>
        <div class='icon-container'>
            <a href='configuracoes.php' class='m-2'><i class='fas fa-cog fa-2x'></i></a>
            <a href='escrever_resenha.php' class='m-2'><i class='fas fa-pencil-alt fa-2x'></i></a>
        </div>
    </div>
    <div class='content'>
        <div class='profile-info'>
            <div class='profile-pic'>
                " . ($aluno['foto'] ? "<img src='controle/" . htmlspecialchars($aluno['foto']) . "' alt='Foto do Aluno'>" : "<p>Foto não disponível</p>") . "
            </div>
            <div class='info'>
                <p><strong>Nome: </strong>" . htmlspecialchars($aluno['nome']) . "</p> 
                <p><strong>RM: </strong>" . htmlspecialchars($aluno['rm']) . "</p>
                <p><strong>Email: </strong>" . htmlspecialchars($aluno['email']) . "</p>
                <p><strong>Telefone: </strong>" . htmlspecialchars($aluno['telefone']) . "</p>
            </div>
        </div>
        <a href='logout_aluno.php'><button type='button' class='button m-2 btn btn-md btn-outline-warning btn-dark'>Sair</button></a>
        <a href='historico_reservas.php'><button type='button' class='button m-2 btn btn-md btn-outline-success btn-dark'>Histórico de Reservas</button></a>    
    </div>
</div>
";


        } else {
            echo "<p class='error-message'>Erro ao carregar as informações do aluno.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error-message'>Erro: " . $e->getMessage() . "</p>";
    }
} else {
    // Exibe o formulário de login se o aluno não estiver logado
    ?>
    <link rel="stylesheet" href="css/login_alunos.css">
    <div class="container">
        <div class="form-signin" style="background-color: #D3D3D3;">
            <img src="img/icon.png" alt="Ícone" width="60px" height="60px">
            <div class="text-center mt-3">
                <form method="post" action="login.php">
                    <input type="text" id="inputRM" class="form-control mb-3" placeholder="Seu RM" required autofocus
                        name="rm" style="background-color: #f7d547;">
                    <input type="password" id="inputPassword" class="form-control mb-3" placeholder="Senha" required
                        name="senha" style="background-color: #f7d547;">
                    <input type="submit" class="btn btn-md btn-primary btn-block mt-3" name="btnlogar" value="Entrar">
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>
<style>
    body {
        background-color: #f5f7f9;
        /* Tom de cinza claro */
        margin: 0;
        padding: 0;
        height: 100vh;
        position: relative;
    }

    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #e0e0e0;
        /* Tom de cinza muito claro */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.8;
        /* Menor opacidade para um tom mais suave */
        z-index: -1;
        pointer-events: none;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        background-color: #ffffff;
        /* Fundo branco para o cabeçalho */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Sombra sutil no cabeçalho */
    }

    .title {
        font-size: 40px;
        font-weight: bold;
        color: #f7d547;
        /* Amarelo destaque */
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }

    .icon-container {
        display: flex;
        gap: 10px;
    }

    .icon-container a {
        color: #333;
    }

    .icon-container a:hover {
        color: #555;
    }

    .container {
        position: relative;
        border-radius: 10px;
        background-color: #ffffff;
        /* Branco para o container principal */
        margin-top: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Sombra suave */
        padding-top: 20px;
    }

    .content {
        padding: 20px;
    }

    .title {
        font-size: 40px;
        font-weight: bold;
        color: #f7d547;
        margin-bottom: 10px;
    }

    .titletwo {
        color: black;
    }

    .aluno-nome {
        font-weight: bold;
        font-size: 1.0em;
    }

    .description {
        font-size: 16px;
        color: #333;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .profile-pic {
        width: 100%;
        max-width: 300px;
        height: auto;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 20px;
        display: block;
    }

    .profile-pic img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 50%;
    }

    @media (max-width: 768px) {
        .profile-info {
            flex-direction: column;
            align-items: center;
        }

        .profile-pic {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .info {
            text-align: center;
        }
    }

    .profile-info {
        display: flex;
        align-items: center;
    }

    .info {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
        font-family: Helvetica, Sans-Serif;
    }

    .button {
        background-color: #27ae60;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    .form-signin {
        background-color: #eaf4f4;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        padding: 20px;
        max-width: 400px;
        margin: auto;
    }
</style>