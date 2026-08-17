<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}
?>

<html>

<head>
    <title>Carrinho</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

    <div class="header">

        <h1>🛒 Seu Carrinho</h1>

    </div>

    <div class="container">

        <div class="section">

            <?php

            if (count($_SESSION['carrinho']) == 0) {

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

                foreach ($_SESSION['carrinho'] as $item) {

                    echo "
                    <div class='card'>

                        <h2>
                            {$item['produto']}
                        </h2>

                        <p>
                            Preço: R$ {$item['preco']}
                        </p>

                    </div>
                    ";

                    $total += $item['preco'];
                }

                echo "
                <div class='card'>

                    <h2>
                        Total: R$ $total
                    </h2>

                    <br>

                    <form action='finalizar.php' method='post'>

                        <button type='submit'>
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