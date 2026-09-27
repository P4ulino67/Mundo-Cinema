<?php
session_start();

include "cons.php";
require_once "DLL.php";

// Conta quantos itens (somando as quantidades) o usuário logado tem no carrinho
$total_carrinho = 0;
if (isset($_SESSION['usuario'])) {
    $usuario_id = $_SESSION['UsuarioId'];
    $consulta = "SELECT SUM(quantidade) AS total FROM carrinho WHERE usuario_id = '$usuario_id'";
    $resultado = banco($server, $user, $password, $db, $consulta);
    $linha = $resultado->fetch_assoc();
    $total_carrinho = $linha['total'] ?? 0;
}

// Busca todos os produtos cadastrados no banco
$consulta = "SELECT * FROM produtos";
$resultado = banco($server, $user, $password, $db, $consulta);
?>

<html>

<head>
    <title>Mundo do Cinema</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <div class="header">
        <div class="topo-site">
            <div class="titulo-site">
                <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">

                <p> Sua loja de filmes favorita </p>
            </div>

            <div class="usuario">
                <?php

                if (isset($_SESSION['usuario'])) {

                    echo "
                    Bem-vindo, " . $_SESSION['usuario'] . "
                    | 
                    <a href='logout.php'>
                        Sair
                    </a>
                    ";

                } else {

                    echo "
                    <a href='login.php'>
                        Entrar
                    </a>
                    ";
                }

                ?>
            </div>
        </div>
        <nav class="navbar">
            <ul>
                <li>
                    <a href="index.php"> Início </a>
                </li>
                <li>
                    <a href="generos.html"> Gêneros </a>
                </li>
                <li>
                    <a href="carrinho.php"> Carrinho 🛒 (<?php echo $total_carrinho; ?>) </a>
                </li>
            </ul>
        </nav>
    </div>

    <?php
    if (isset($_SESSION['mensagem'])) {

        echo "
        <div class='mensagem'>
            " . $_SESSION['mensagem'] . "
        </div>
        ";

        unset($_SESSION['mensagem']);
    }

    ?>

    <div class="container">
        <div class="section">
            <h2> 🛒 Filmes Disponíveis </h2>
            <div class="grid-filmes">
                <?php
                while ($produto = $resultado->fetch_assoc()) {

                    $id_seguro = (int) $produto['id'];
                    $nome_seguro = htmlspecialchars($produto['nome']);
                    $descricao_segura = htmlspecialchars($produto['descricao']);
                    $imagem_segura = htmlspecialchars($produto['imagem']);
                    $preco_formatado = number_format($produto['preco'], 2, ',', '.');

                    echo "
                    <div class='card'>
                        <img src='{$imagem_segura}' alt='{$nome_seguro}'>

                        <h3> {$nome_seguro} </h3>

                        <p> {$descricao_segura} </p>

                        <p> <strong> R$ {$preco_formatado} </strong> </p>

                        <form action='adicionar.php' method='POST'>
                            <input type='hidden' name='produto_id' value='{$id_seguro}'>
                            <button type='submit'> Comprar </button>
                        </form>

                    </div>
                    ";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>
            &copy; Tasso e Paulino
        </p>
    </div>
</body>
</html>
