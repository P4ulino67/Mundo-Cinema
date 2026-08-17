<?php
$login = $_POST['login'];
$senha = md5($_POST['senha']);
$cpf = $_POST['cpf'];

$dados = "$login|$senha|$cpf";

file_put_contents("login/$login.dat", $dados);

header("Location: login.php");
?>