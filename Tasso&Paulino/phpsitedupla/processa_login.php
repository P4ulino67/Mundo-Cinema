<?php

session_start();

include "app/cons.php";
require_once "app/DLL.php";

$login = $_POST['login'];
$senha = md5($_POST['senha']);

$consulta = "SELECT * FROM usuarios 
WHERE login = '$login' AND senha = '$senha'";

$resultado = banco($server, $user, $password, $db, $consulta);

if ($linha = $resultado->fetch_assoc()) {

    $_SESSION['usuario'] = $linha['nome'];

    header("Location: index.php");
    exit();

} else {

    header("Location: login.php?erro=1");
    exit();

}

?>
