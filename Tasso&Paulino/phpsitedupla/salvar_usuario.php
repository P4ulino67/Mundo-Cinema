<?php

include "app/cons.php";
require_once "app/DLL.php";

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

?>
