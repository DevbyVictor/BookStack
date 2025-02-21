<?php
ob_start(); // Ativa o buffer de saída
@session_start();
require_once 'includes/db_connect.php';

// Variável para armazenar mensagem de erro
$error_message = "";

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
            $_SESSION['aluno_id'] = $aluno['id'];
            $_SESSION['aluno_nome'] = $aluno['nome'];
            header('Location: index.php');
            exit();
        } else {
            $error_message = "RM ou senha incorretos. Tente novamente.";
        }
    } catch (PDOException $e) {
        $error_message = "Erro no servidor: " . $e->getMessage();
    }
}

if (isset($_SESSION['aluno_id'])) {
    $aluno_id = $_SESSION['aluno_id'];

    try {
        $conn = Conectar::getInstance();
        $stmt = $conn->prepare("SELECT rm, nome, email, telefone, foto FROM alunos WHERE id = :id");
        $stmt->bindParam(':id', $aluno_id, PDO::PARAM_INT);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($aluno) {
            ?>
            <!DOCTYPE html>
            <html lang="pt-BR">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Painel do Aluno</title>
            </head>

            <body>
                <div class="container aluno-container">
                    <div class="header">
                        <div class="title">
                            <span class="titletwo p-1 mx-1">Bem-vindo,</span>
                            <span class="aluno-nome"><?= htmlspecialchars($aluno['nome']) ?>!</span>
                        </div>
                        <div class="icon-container">
                            <a href="editar.php" class="m-2">
                                <i class="fas fa-pencil-alt fa-2x"></i>
                            </a>
                        </div>
                    </div>
                    <div class="content">
                        <div class="profile-info">
                            <div class="profile-pic">
                                <?= $aluno['foto'] ? "<img src='controle/" . htmlspecialchars($aluno['foto']) . "' alt='Foto do Aluno'>" : "<p>Foto não disponível</p>" ?>
                            </div>
                            <div class="info">
                                <p><i class="fas fa-user"></i> <strong>Nome: </strong><?= htmlspecialchars($aluno['nome']) ?></p>
                                <p><i class="fas fa-id-card"></i> <strong>RM: </strong><?= htmlspecialchars($aluno['rm']) ?></p>
                                <p><i class="fas fa-envelope"></i> <strong>Email: </strong><?= htmlspecialchars($aluno['email']) ?>
                                </p>
                                <p><i class="fas fa-phone"></i> <strong>Telefone:
                                    </strong><?= htmlspecialchars($aluno['telefone']) ?></p>
                            </div>
                        </div>
                        <div class="button-group mt-4">
                            <a href="logout_aluno.php" class="btn btn-md btn-outline-secondary">Sair</a>
                            <a href="livros/historico_reservas.php?p=historico" class="btn btn-md btn-outline-primary">Histórico de
                                Reservas</a>
                        </div>
                    </div>
                </div>
            </body>

            </html>
            <?php
        } else {
            $error_message = "Erro ao carregar as informações do aluno.";
        }
    } catch (PDOException $e) {
        $error_message = "Erro no servidor: " . $e->getMessage();
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="css/login_alunos.css">
    </head>

    <body>
        <div class="container">
            <div class="form-signin" style="background-color: #D3D3D3;">
                <img src="img/icon.png" alt="Ícone" width="60px" height="60px">
                <div class="text-center mt-3">
                    <form method="post" action="index.php?p=login" onsubmit="showLoading()">
                        <input type="text" id="inputRM" class="form-control mb-3" placeholder="Seu RM" required autofocus
                            name="rm" style="background-color: #f7d547;">
                        <input type="password" id="inputPassword" class="form-control mb-3" placeholder="Senha" required
                            name="senha" style="background-color: #f7d547;">
                        <input type="submit" class="btn btn-md btn-primary btn-block mt-3" name="btnlogar" value="Entrar">
                    </form>
                </div>
            </div>
        </div>

        <!-- SweetAlert2 Script -->
        <script>
            function showLoading() {
                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // Exibe mensagem de erro se definida
            document.addEventListener("DOMContentLoaded", function () {
                <?php if (!empty($error_message)): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Ops...',
                        text: '<?php echo $error_message; ?>',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f27474',
                    });
                <?php endif; ?>
            });
        </script>
        <script>
            function openHelpPopup() {
                document.getElementById('helpPopup').style.display = 'block';
            }

            function closeHelpPopup() {
                document.getElementById('helpPopup').style.display = 'none';
            }

            // Fecha o pop-up se o usuário clicar fora dele
            window.onclick = function (event) {
                const popup = document.getElementById('helpPopup');
                if (event.target === popup) {
                    closeHelpPopup();
                }
            };
        </script>

        <?php if (!isset($_SESSION['aluno_id'])): ?>
            <!-- Botão no canto inferior direito -->
            <div id="helpButton" class="help-button">
                <button type="button" onclick="openHelpPopup()">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>

            <!-- Pop-Up de ajuda -->
            <div id="helpPopup" class="help-popup">
                <div class="help-popup-content">
                    <span class="close-btn" onclick="closeHelpPopup()">&times;</span>
                    <h2>Como obter seu login?</h2>
                    <p>Se você ainda não possui um RM ou senha, entre em contato com a administração da biblioteca da sua
                        instituição para criar uma conta. Os dados necessários serão fornecidos exclusivamente pela equipe
                        responsável, e o cadastro não pode ser realizado de forma autônoma.</p>
                    <p>Certifique-se de ter o seguinte documento em mãos:</p>
                    <ul>
                        <li>Carteirinha de Estudante</li>
                    </ul>
                    <p>Em caso de dúvidas, entre em contato com o suporte pelo e-mail: <strong>suporte@bookstack.com</strong>.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </body>

    </html>
    <?php
}
?>

<style>
    /* Configurações Gerais */
    body {
        background-color: #f5f7f9;
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
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.8;
        z-index: -1;
        pointer-events: none;
    }

    .aluno-container {
        max-width: 850px;
        background-color: #ffffff;
        margin: 50px auto;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .title {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }

    .icon-container a {
        color: #333;
        transition: color 0.3s ease;
    }

    .icon-container a:hover {
        color: #007bff;
    }

    .content {
        padding: 25px;
    }

    .profile-info {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .profile-pic {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 25px;
    }

    .profile-pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .info {
        font-size: 1.1rem;
        color: #333;
        font-family: Helvetica, Sans-Serif;
        max-width: calc(100% - 220px);
    }

    .info p {
        margin: 0;
        line-height: 1.8;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .button-group {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-outline-secondary {
        color: #333;
        background-color: transparent;
        border-color: #6c757d;
        padding: 10px 20px;
        /* Mantendo botão como estava */
        font-size: 1rem;
        /* Tamanho original */
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-outline-primary {
        color: #007bff;
        background-color: transparent;
        border-color: #007bff;
        padding: 10px 20px;
        font-size: 1rem;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: #fff;
    }

    /* Regras de Responsividade */
    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            align-items: flex-start;
        }

        .profile-info {
            flex-direction: column;
            align-items: center;
        }

        .profile-pic {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .info {
            max-width: 100%;
            text-align: center;
        }

        .button-group {
            justify-content: center;
        }
    }

    /* Estilo do botão de ajuda */
    .help-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .help-button button {
        background-color: #f7d547;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #333;
        transition: all 0.3s ease;
    }

    .help-button button:hover {
        background-color: #e6c236;
        transform: scale(1.1);
    }

    /* Estilo do pop-up */
    .help-popup {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1001;
        padding: 20px;
        animation: fadeIn 0.3s ease-in-out;
    }

    .help-popup-content {
        position: relative;
    }

    .help-popup-content .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        color: #333;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .help-popup-content .close-btn:hover {
        color: #e63946;
    }

    .help-popup h2 {
        margin: 0 0 10px;
        font-size: 18px;
        color: #333;
    }

    .help-popup p,
    .help-popup ul {
        font-size: 14px;
        color: #666;
    }

    .help-popup ul {
        list-style-type: disc;
        margin-left: 20px;
    }

    /* Animação para o pop-up */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>