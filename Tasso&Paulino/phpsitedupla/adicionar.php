<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$produto = $_GET['produto'];
$preco = $_GET['preco'];

$item = [
    "produto" => $produto,
    "preco" => $preco
];

$_SESSION['carrinho'][] = $item;

$_SESSION['mensagem'] = "✅ Produto adicionado ao carrinho!";

header("Location: index.php");
exit();
?>