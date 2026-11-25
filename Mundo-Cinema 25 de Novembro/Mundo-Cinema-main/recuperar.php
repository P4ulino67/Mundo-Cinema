<?php
session_start();

// Se a pessoa já pediu o código, mostra a etapa 2 (código + senha nova)
$email_recuperacao = $_SESSION['recuperar_email'] ?? '';
?>

<html>
<head>
    <title>Recuperar Senha - Mundo do Cinema</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="header">
        <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="generos.html">Gêneros</a></li>
                <li><a href="login.php">Entrar</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <div class="card">
            <h2>Recuperar senha</h2>

            <?php
            if (isset($_SESSION['mensagem'])) {
                echo "<div class='mensagem'>" . $_SESSION['mensagem'] . "</div>";
                unset($_SESSION['mensagem']);
            }

            if (isset($_SESSION['erro_recuperar'])) {
                echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['erro_recuperar']) . "</p>";
                unset($_SESSION['erro_recuperar']);
            }
            ?>

            <?php if ($email_recuperacao == '') { ?>

                <!-- Etapa 1: informar o e-mail usado no cadastro -->
                <p>Informe o e-mail que você usou para criar a conta. Vamos enviar um código de verificação.</p>

                <form action="banco.php" method="POST">
                    <input type="email" name="email" placeholder="E-mail" required><br><br>
                    <button type="submit" name="B6" value="1">Enviar código</button>
                </form>

            <?php } else { ?>

                <!-- Etapa 2: código recebido por e-mail + senha nova -->
                <p>Digite o código enviado para <strong><?php echo htmlspecialchars($email_recuperacao); ?></strong> e escolha a nova senha.</p>

                <form action="banco.php" method="POST">
                    <input type="text" name="codigo" placeholder="Código de 6 dígitos" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" required><br><br>
                    <input type="password" name="senha" placeholder="Nova senha" required><br><br>
                    <input type="password" name="confirmar" placeholder="Repita a nova senha" required><br><br>
                    <button type="submit" name="B7" value="1">Trocar senha</button>
                </form>

                <form action="banco.php" method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_recuperacao); ?>">
                    <button type="submit" name="B6" value="1">Reenviar código</button>
                </form>

                <form action="banco.php" method="POST">
                    <button type="submit" name="B8" value="1">Usar outro e-mail</button>
                </form>

            <?php } ?>

            <p>Lembrou a senha?</p>
            <a href="login.php">Voltar para o login</a>
        </div>
    </div>
    <div class="footer">
        <p>&copy; Tasso Farias e Paulino Mendes</p>
    </div>
</body>
</html>
