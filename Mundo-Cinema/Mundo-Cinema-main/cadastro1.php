<?php
session_start();
?>

<html>
<head>
    <title>Cadastro</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<div class="header">
    <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">
</div>

<div class="container">
    <div class="card">

        <h2>Criar Conta</h2>

        <?php
        if (isset($_SESSION['erro_cadastro'])) {
            echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['erro_cadastro']) . "</p>";
            unset($_SESSION['erro_cadastro']);
        }
        ?>

        <form action="banco.php" method="POST" enctype="multipart/form-data">

            <input type="text" name="nome" placeholder="Nome completo" required><br><br>

            <input type="text" name="cpf" placeholder="CPF" required><br><br>

            <input type="email" name="email" placeholder="E-mail" required><br><br>

            <label for="foto">Foto de perfil (opcional, JPG ou PNG até 2 MB):</label><br>
            <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png"><br><br>

            <input type="text" name="endereco" placeholder="Endereço" required><br><br>

            <input type="text" name="bairro" placeholder="Bairro" required><br><br>

            <input type="text" name="cidade" placeholder="Cidade" required><br><br>

            <input type="text" name="estado" placeholder="Estado" required><br><br>

            <input type="text" name="cep" placeholder="CEP" required><br><br>

            <button type="submit" name="B1" value="1">Continuar</button>

        </form>

    </div>
</div>

</body>
</html>