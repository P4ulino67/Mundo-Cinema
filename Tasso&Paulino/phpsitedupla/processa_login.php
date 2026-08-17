<?php
session_start();

$login = $_POST['login'];
$senha = $_POST['senha'];

$arquivo = fopen("usuarios.txt", "r");

$encontrado = false;

while (!feof($arquivo)) {
    $linha = fgets($arquivo);

    $dados = explode(";", $linha);

    if (count($dados) >= 9) {
        if ($dados[7] == $login && trim($dados[8]) == $senha) {
            $encontrado = true;
            $_SESSION['usuario'] = $dados[0]; // nome
        }
    }
}

fclose($arquivo);

if ($encontrado) {
    header("Location: index.php");
} else {
    header("Location: login.php?erro=1");
}
?>