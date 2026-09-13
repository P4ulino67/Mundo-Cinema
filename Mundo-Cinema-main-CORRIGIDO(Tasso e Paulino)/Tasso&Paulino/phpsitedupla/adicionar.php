<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    $produto = $_POST['produto'] ?? '';
    $preco = $_POST['preco'] ?? '';
    header("Location: login.php?produto=" . urlencode($produto) . "&preco=" . urlencode($preco));
    exit();
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$produto = $_POST['produto'];
$preco = $_POST['preco'];

$item = [
    "produto" => $produto,
    "preco" => $preco
];

$_SESSION['carrinho'][] = $item;

$_SESSION['mensagem'] = "✅ Produto adicionado ao carrinho!";

header("Location: index.php");
exit();
?>