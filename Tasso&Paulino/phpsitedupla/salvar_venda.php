<?php

session_start();

include "app/cons.php";
require_once "app/DLL.php";

$usuario = $_SESSION['usuario'];
$produto = $_SESSION['produto'];
$preco = $_SESSION['preco'];
$pagamento = $_POST['pagamento'];

$data = date("Y-m-d H:i:s");

$consulta = "INSERT INTO vendas
(usuario, produto, preco, pagamento, data)
VALUES
('$usuario', '$produto', '$preco', '$pagamento', '$data')";

banco($server, $user, $password, $db, $consulta);

echo "<h2 style='color:white;text-align:center;'>Compra realizada com sucesso!</h2>";

?>
