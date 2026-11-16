<?php
session_start();

include "cons.php";
require_once "DLL.php";

if (!isset($_SESSION['usuario'])) {
    $produto_id = $_POST['produto_id'] ?? '';
    $_SESSION['pendente'] = $produto_id;
    header("Location: login.php");
    exit();
}

$produto_id = $_POST['produto_id'];
$usuario_id = $_SESSION['UsuarioId'];

// Verifica se esse produto já está no carrinho do usuário
$consulta = "SELECT * FROM carrinho WHERE usuario_id = '$usuario_id' AND produto_id = '$produto_id'";
$resultado = banco($server, $user, $password, $db, $consulta);
$linha = $resultado->fetch_assoc();

if ($linha) {
    // Já está no carrinho: só aumenta a quantidade
    $nova_quantidade = $linha['quantidade'] + 1;
    $consulta = "UPDATE carrinho SET quantidade = '$nova_quantidade' WHERE id = '".$linha['id']."'";
} else {
    // Ainda não está no carrinho: insere com quantidade 1
    $consulta = "INSERT INTO carrinho (id, usuario_id, produto_id, quantidade) VALUES (NULL, '$usuario_id', '$produto_id', 1)";
}

banco($server, $user, $password, $db, $consulta);

$_SESSION['mensagem'] = "✅ Produto adicionado ao carrinho!";

header("Location: index.php");
exit();
?>
