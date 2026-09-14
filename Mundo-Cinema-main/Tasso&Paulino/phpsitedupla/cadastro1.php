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

        <form action="banco.php" method="POST">

            <input type="text" name="nome" placeholder="Nome completo" required><br><br>

            <input type="text" name="cpf" placeholder="CPF" required><br><br>

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