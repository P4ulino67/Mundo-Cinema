<?php

session_start();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    unset($_SESSION['carrinho'][$id]);
    $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
}

header("Location: carrinho.php");
exit();

?>
