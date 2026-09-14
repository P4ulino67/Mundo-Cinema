<?php
session_start();
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

            <?php
                $pendente = $_SESSION['pendente'] ?? ['produto' => '', 'preco' => ''];
                unset($_SESSION['pendente']);
            ?>

            <form action="banco.php" method="POST">
                <input type="text" name="login" placeholder="Login" required><br><br>
                <input type="password" name="senha" placeholder="Senha" required><br><br>
                <input type="hidden" name="produto" value="<?= htmlspecialchars($pendente['produto']) ?>">
                <input type="hidden" name="preco" value="<?= htmlspecialchars($pendente['preco']) ?>">
                <button type="submit" name="B3" value="1">Entrar</button>
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
