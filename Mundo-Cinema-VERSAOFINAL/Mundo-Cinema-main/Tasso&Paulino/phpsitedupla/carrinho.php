<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include "cons.php";
require_once "DLL.php";

$usuario_id = $_SESSION['UsuarioId'];

$consulta = "SELECT carrinho.id, carrinho.quantidade, produtos.nome, produtos.preco
             FROM carrinho
             JOIN produtos ON carrinho.produto_id = produtos.id
             WHERE carrinho.usuario_id = '$usuario_id'";

$resultado = banco($server, $user, $password, $db, $consulta);

$itens = [];
while ($linha = $resultado->fetch_assoc()) {
    $itens[] = $linha;
}
?>

<html>

<head>
    <title>Carrinho</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <div class="header">

        <h1>🛒 Seu Carrinho</h1>

    </div>

    <div class="container">

        <div class="section">

            <?php

            if (count($itens) == 0) {

                echo "
                <div class='card'>

                    <h2>
                        Seu carrinho está vazio.
                    </h2>

                    <br>

                    <a href='index.php'>
                        <button>
                            Voltar para Loja
                        </button>
                    </a>

                </div>
                ";

            } else {

                $total = 0;

                foreach ($itens as $item) {

                    $nome_seguro = htmlspecialchars($item['nome']);
                    $preco = $item['preco'];
                    $quantidade = $item['quantidade'];
                    $subtotal = $preco * $quantidade;

                    echo "
                    <div class='card'>

                        <h2>
                            {$nome_seguro}
                        </h2>

                        <p>
                            Preço unitário: R$ {$preco}
                        </p>

                        <p>
                            Quantidade: {$quantidade}
                        </p>

                        <p>
                            Subtotal: R$ {$subtotal}
                        </p>

                        <form action='remover.php' method='post'>
                            <input type='hidden' name='id' value='{$item['id']}'>
                            <button type='submit'>
                                Remover
                            </button>
                        </form>

                    </div>
                    ";

                    $total += $subtotal;
                }

                echo "
                <div class='card'>

                    <h2>
                        Total: R$ $total
                    </h2>

                    <br>

                    <form action='banco.php' method='post'>

                        <select name='pagamento' required>
                            <option value=''>Forma de pagamento</option>
                            <option value='Pix'>Pix</option>
                            <option value='Cartao de Credito'>Cartão de Crédito</option>
                            <option value='Boleto'>Boleto</option>
                        </select>

                        <br><br>

                        <button type='submit' name='B4' value='1'>
                            Finalizar Compra
                        </button>

                    </form>

                    <br>

                    <a href='index.php'>

                        <button>
                            Continuar Comprando
                        </button>

                    </a>

                </div>
                ";
            }

            ?>

        </div>

    </div>

</body>

</html>
