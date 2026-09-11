<?php

include "app/cons.php";
require_once "app/DLL.php";

$login = $_POST['login'];
$senha = md5($_POST['senha']);
$cpf = $_POST['cpf'];

$consulta = "SELECT * FROM usuarios 
WHERE login = '$login' AND cpf = '$cpf'";

$resultado = banco($server, $user, $password, $db, $consulta);

if ($linha = $resultado->fetch_assoc()) {
    header("Location: login.php");
} 
else {
    header("Location: cadastro1.php?erro=1");
}

exit();

?>
