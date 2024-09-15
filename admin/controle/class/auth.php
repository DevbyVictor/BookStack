<?php
function setOnline($adminId)
{
    // Obtém a instância da conexão PDO
    $conn = Conectar::getInstance();
    $query = "UPDATE adminstradores SET status_online = TRUE WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $adminId, PDO::PARAM_INT);
    $stmt->execute();
}

function setOffline($adminId) {
    try {
        $conn = Conectar::getInstance();
        $query = "UPDATE adminstradores SET status_online = 0 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $adminId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            error_log("Status offline atualizado para ID: $adminId");
            return true;
        } else {
            error_log("Falha ao executar a atualização para ID: $adminId");
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erro ao definir offline: " . $e->getMessage());
        return false;
    }
}

function verificarAcesso($nivelNecessario, $comparacao = 'exato')
{
    // Inicia a sessão se ainda não estiver iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica se o usuário está autenticado
    if (!isset($_SESSION['acesso']) || $_SESSION['acesso'] != 'b8d66a4634503dcf530ce1b3704ca5dfae3d34bb') {
        header("Location: login_adm.php");
        exit();
    }

    // Verifica o nível de acesso baseado na comparação fornecida
    if (!isset($_SESSION['admin_nivel_acesso'])) {
        echo '<div class="alert alert-danger">Nível de acesso não definido.</div>';
        exit();
    }

    $mensagemErro = '';
    switch ($comparacao) {
        case 'exato':
            if ($_SESSION['admin_nivel_acesso'] != $nivelNecessario) {
                $mensagemErro = 'Você não tem permissão para acessar esta página.';
                $paginaRedirecionamento = 'admin_dashboard.php';
            }
            break;

        case 'superior':
            if ($_SESSION['admin_nivel_acesso'] < $nivelNecessario) {
                $mensagemErro = 'Você não tem permissão para acessar esta página.';
                $paginaRedirecionamento = 'admin_dashboard.php';
            }
            break;

        default:
            $mensagemErro = 'Tipo de comparação inválido.';
            $paginaRedirecionamento = 'admin_dashboard.php';
    }

    if ($mensagemErro) {
        echo '<div class="alert alert-danger">' . $mensagemErro . '</div>';
        echo '<div id="redirect-message" class="alert alert-warning" style="display: none;">Redirecionando...</div>';
        echo '<script>
            setTimeout(function() {
                document.getElementById("redirect-message").style.display = "block";
                setTimeout(function() {
                    window.location.href = "' . $paginaRedirecionamento . '";
                }, 3000);
            }, 1000);
        </script>';
        exit();
    }
}



