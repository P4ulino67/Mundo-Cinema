<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    include "cons.php";
    require_once "DLL.php";

    $login = $_POST['login'];
    $senha = md5($_POST['senha']);

    $consulta = "SELECT * FROM usuarios 
    WHERE login = '$login' AND senha = '$senha'";

    $resultado = banco($server, $user, $password, $db, $consulta);

    if ($linha = $resultado->fetch_assoc()) {

        $_SESSION['usuario'] = $linha['nome'];

        // Se o usuário tentou comprar um filme antes de logar, completa a adição ao carrinho agora
        if (!empty($_POST['produto'])) {
            if (!isset($_SESSION['carrinho'])) {
                $_SESSION['carrinho'] = [];
            }
            $_SESSION['carrinho'][] = [
                "produto" => $_POST['produto'],
                "preco" => $_POST['preco'] ?? ''
            ];
            $_SESSION['mensagem'] = "✅ Produto adicionado ao carrinho!";
        }

        header("Location: index.php");
        exit();

    } else {

        $_SESSION['erro'] = 1;
        header("Location: login.php");
        exit();

    }

}
?>

<html>
<head>
    <title>Login - Mundo do Cinema</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="header">
        <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="generos.html">Gêneros</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <div class="card">
            <h2>Entrar na sua conta</h2>
            <?php
            if (isset($_SESSION['erro'])) {
                echo "<p style='color:red;'>Login inválido!</p>";
                unset($_SESSION['erro']);
            }
            ?>

            <form action="login.php" method="POST">
                <input type="text" name="login" placeholder="Login" required><br><br>
                <input type="password" name="senha" placeholder="Senha" required><br><br>
                <input type="hidden" name="produto" value="<?= htmlspecialchars($_GET['produto'] ?? '') ?>">
                <input type="hidden" name="preco" value="<?= htmlspecialchars($_GET['preco'] ?? '') ?>">
                <button type="submit">Entrar</button>
            </form>
            <p>Ainda não tem conta?</p>
            <a href="cadastro1.php">Criar conta</a>
        </div>
    </div>
    <div class="footer">
        <p>&copy; Tasso Farias e Paulino Mendes</p>
    </div>
</body>

</html>
