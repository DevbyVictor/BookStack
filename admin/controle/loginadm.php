<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title>Acesso ao sistema</title>

    <!-- Principal CSS do Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Estilos customizados para esse template -->
    <link href="../css/loginadm.css" rel="stylesheet">
</head>

<body class="text-center">
    <div class="container">
        <div class="col-sm-12 col-md-12">
            <form class="form-signin" method="post">
                <h1 class="h3 mb-3 font-weight-normal">Bookstack Administrator</h1>
                <label for="inputEmail" class="sr-only">Endereço de email</label>
                <input type="email" id="inputEmail" class="form-control mb-3" placeholder="Seu email" required autofocus
                    name="txtemail">
                <label for="inputPassword" class="sr-only">Senha</label>
                <input type="password" id="inputPassword" class="form-control" placeholder="Senha" required
                    name="txtsenha">
                <input type="submit" class="btn btn-lg btn-secondary btn-block" name="btnlogar" value="Login" />
                <p class="mt-3 text-muted">&copy; 2024</p>
            </form>
        </div>
</body>

</html>
<?php
if (filter_input(INPUT_POST, 'btnlogar')) {
    $email = filter_input(INPUT_POST, 'txtemail');
    $senha = filter_input(INPUT_POST, 'txtsenha');

    include_once './class/user.php';
    $user = new Usuario();

    $user->setEmail(trim($email));
    $user->setSenha(trim($senha));

    $resultado = $user->consultar();
    if (!$resultado) {
        echo '<div class="col-sm-12 col-md-12">'
            . '<div class="alert alert-danger" role="alert">'
            . '<h3>E-mail e/ou senha incorreta(s)</h3>'
            . '<p>Verifique seu email e senha!</p>'
            . '</div>'
            . '</div>';
    } else {
        session_start();
        $_SESSION['acesso'] = 'b8d66a4634503dcf530ce1b3704ca5dfae3d34bb';
        $_SESSION['admin_id'] = $resultado['id'];
        $_SESSION['admin_email'] = $resultado['email'];
        $_SESSION['admin_nivel_acesso'] = $user->getNivelAcesso();

        // Define o status como online
        include_once 'class/auth.php';
        setOnline($_SESSION['admin_id']);

        header("location:admin_dashboard.php");
    }
}
?>

</div>