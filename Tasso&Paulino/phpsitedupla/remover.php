<?php

session_start();

$id = $_POST['id'];

unset($_SESSION['carrinho'][$id]);

$_SESSION['carrinho'] = array_values($_SESSION['carrinho']);

header("Location: carrinho.php");
exit();

?>
