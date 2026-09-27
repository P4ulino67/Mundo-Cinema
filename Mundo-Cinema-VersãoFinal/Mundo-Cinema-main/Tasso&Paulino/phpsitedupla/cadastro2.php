<?php
session_start();
?>

<html>
<head>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="header">
        <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">
    </div>
    <div class="container">
        <div class="card">
            <h2>Crie seu login</h2>
            <form action="banco.php" method="POST">
                <input type="text" name="login" placeholder="Login" required><br><br>
                <input type="password" name="senha" placeholder="Senha" required><br><br>
                <button type="submit" name="B2" value="1">Cadastrar</button>
            </form>
        </div>
    </div>
</body>
</html>
