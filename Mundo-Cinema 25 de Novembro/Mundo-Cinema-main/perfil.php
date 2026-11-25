<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include "cons.php";
require_once "DLL.php";

$usuario_id = $_SESSION['UsuarioId'];

$consulta = "SELECT nome, email, foto FROM usuarios WHERE id = '$usuario_id'";
$resultado = banco($server, $user, $password, $db, $consulta);
$usuario = $resultado->fetch_assoc();

$email_atual = $usuario['email'] ?? '';
$foto_atual = $usuario['foto'] ?? '';
?>

<html>
<head>
    <title>Meu Perfil - Mundo do Cinema</title>
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
                <li><a href="carrinho.php">Carrinho 🛒</a></li>
            </ul>
        </nav>
    </div>

    <?php
    if (isset($_SESSION['mensagem'])) {
        echo "<div class='mensagem'>" . $_SESSION['mensagem'] . "</div>";
        unset($_SESSION['mensagem']);
    }
    ?>

    <div class="container">
        <div class="card">
            <h2>Meu Perfil</h2>

            <?php
            if (isset($_SESSION['erro_perfil'])) {
                echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['erro_perfil']) . "</p>";
                unset($_SESSION['erro_perfil']);
            }

            if ($foto_atual != '') {
                echo "<img src='" . htmlspecialchars($foto_atual) . "' alt='Foto de perfil' class='foto-perfil-grande'>";
            }
            ?>

            <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['nome']); ?></p>

            <form action="banco.php" method="POST" enctype="multipart/form-data">
                <label for="email">E-mail:</label><br>
                <input type="email" id="email" name="email" placeholder="E-mail" value="<?php echo htmlspecialchars($email_atual); ?>" required><br><br>

                <label for="foto">Nova foto de perfil (JPG ou PNG até 2 MB):</label><br>
                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png"><br><br>

                <button type="submit" name="B5" value="1">Salvar</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>&copy; Tasso Farias e Paulino Mendes</p>
    </div>
</body>
</html>
