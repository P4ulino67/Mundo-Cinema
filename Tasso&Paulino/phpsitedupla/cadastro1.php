<?php
session_start();
?>

<html>
<head>
    <title>Cadastro</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

<div class="header">
    <h1>🎬 Mundo do Cinema</h1>
</div>

<div class="container">
    <div class="card">

        <h2>Criar Conta</h2>

        <form action="salvar_usuario.php" method="POST">

            <input type="text" name="nome" placeholder="Nome completo" required><br><br>

            <input type="text" name="cpf" placeholder="CPF" required><br><br>

            <input type="text" name="endereco" placeholder="Endereço" required><br><br>

            <input type="text" name="bairro" placeholder="Bairro" required><br><br>

            <input type="text" name="cidade" placeholder="Cidade" required><br><br>

            <input type="text" name="estado" placeholder="Estado" required><br><br>

            <input type="text" name="cep" placeholder="CEP" required><br><br>

            <input type="text" name="login" placeholder="Login" required><br><br>

            <input type="password" name="senha" placeholder="Senha" required><br><br>

            <button type="submit">Cadastrar</button>

        </form>

    </div>
</div>

</body>
</html>