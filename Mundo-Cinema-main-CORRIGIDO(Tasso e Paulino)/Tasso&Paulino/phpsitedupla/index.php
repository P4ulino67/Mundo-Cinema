<?php
session_start();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}
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
                    <a href="carrinho.php"> Carrinho 🛒 (<?php echo count($_SESSION['carrinho']); ?>) </a>
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
                <div class="card">
                    <img src="img/acao2.png" alt="Rambo 4">

                    <h3> Rambo 4 </h3>

                    <p> John Rambo retorna em uma missão brutal para salvar reféns em meio a uma guerra extremamente violenta. </p>

                    <p> <strong> R$ 20,00 </strong> </p>

                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Rambo 4">
                        <input type="hidden" name="preco" value="20">
                        <button type="submit"> Comprar </button>
                    </form>

                </div>
                <div class="card">
                    <img src="img/drama.jpg" alt="Como Eu Era Antes de Você">
                    <h3> Como Eu Era Antes de Você </h3>

                    <p> Uma emocionante história de amor, superação e escolhas difíceis. </p>
                    <p> <strong> R$ 15,00 </strong> </p>

                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Como Eu Era Antes de Você">
                        <input type="hidden" name="preco" value="15">
                        <button type="submit">
                            Comprar
                        </button>
                    </form>

                </div>
                <div class="card">
                    <img src="img/comedia.jpg" alt="Gente Grande">
                    <h3> Gente Grande </h3>
                    <p>Cinco amigos revivem momentos hilários em uma divertida viagem. </p>

                    <p> <strong> R$ 10,00 </strong> </p>
                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Gente Grande">
                        <input type="hidden" name="preco" value="10">
                        <button type="submit">
                            Comprar
                        </button>
                    </form>
                </div>
                <div class="card">
                    <img src="img/romance.jpg" alt="Diário de uma Paixão">

                    <h3> Diário de uma Paixão </h3>
                    <p> Um romance emocionante que atravessa os anos e marca gerações. </p>

                    <p> <strong> R$ 18,00 </strong> </p>

                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Diario de uma Paixao">
                        <input type="hidden" name="preco" value="18">
                        <button type="submit">
                            Comprar
                        </button>
                    </form>
                </div>
                <div class="card">
                    <img src="img/terror2.png" alt="Invocação do Mal">
                    <h3> Invocação do Mal </h3>

                    <p> Investigadores paranormais enfrentam forças malignas assustadoras. </p>

                    <p> <strong> R$ 12,00 </strong> </p>

                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Invocacao do Mal">
                        <input type="hidden" name="preco" value="12">
                        <button type="submit">
                            Comprar
                        </button>
                    </form>
                </div>
                <div class="card">
                    <img src="img/ficcao.jpg" alt="Jurassic Park">
                    <h3> Jurassic Park </h3>

                    <p> Dinossauros ganham vida em um parque temático que foge do controle. </p>

                    <p> <strong> R$ 20,00 </strong> </p>

                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Jurassic Park">
                        <input type="hidden" name="preco" value="20">
                        <button type="submit">
                            Comprar
                        </button>
                    </form>
                </div>
                <div class="card">
                    <img src="img/animacao.jpg" alt="Gato de Botas 2">
                    <h3> Gato de Botas 2 </h3>
                    <p> Uma aventura divertida, emocionante e cheia de ação para toda família. </p>
                    <p> <strong> R$ 14,00 </strong> </p>

                    <form action="adicionar.php" method="POST">
                        <input type="hidden" name="produto" value="Gato de Botas 2">
                        <input type="hidden" name="preco" value="14">
                        <button type="submit">
                            Comprar
                        </button>
                    </form>
                </div>
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
