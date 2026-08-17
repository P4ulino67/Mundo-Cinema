<?php
session_start();

$usuario = $_SESSION['usuario'];
$produto = $_SESSION['produto'];
$preco = $_SESSION['preco'];
$pagamento = $_POST['pagamento'];

$data = date("d/m/Y H:i");

$venda = "$usuario|$produto|$preco|$pagamento|$data\n";

file_put_contents("vendas/vendas.dat", $venda, FILE_APPEND);

echo "<h2 style='color:white;text-align:center;'>Compra realizada com sucesso!</h2>";
?>