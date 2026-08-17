<?php

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$endereco = $_POST['endereco'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];
$login = $_POST['login'];
$senha = $_POST['senha'];

$arquivo = fopen("usuarios.txt", "a");

fwrite($arquivo, "$nome;$cpf;$endereco;$bairro;$cidade;$estado;$cep;$login;$senha\n");

fclose($arquivo);

header("Location: login.php");
?>