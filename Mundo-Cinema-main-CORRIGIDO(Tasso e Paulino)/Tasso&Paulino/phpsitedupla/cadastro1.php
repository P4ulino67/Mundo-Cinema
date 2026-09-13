<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    include "cons.php";
    require_once "DLL.php";

    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];
    $login = $_POST['login'];
    $senha = md5($_POST['senha']);

    $consulta = "INSERT INTO usuarios 
    (nome, cpf, endereco, bairro, cidade, estado, cep, login, senha) 
    VALUES 
    ('$nome', '$cpf', '$endereco', '$bairro', '$cidade', '$estado', '$cep', '$login', '$senha')";

    banco($server, $user, $password, $db, $consulta);

    header("Location: login.php");
    exit();

}
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

        <form action="cadastro1.php" method="POST">

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