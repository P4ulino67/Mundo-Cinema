<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$_SESSION['carrinho'] = [];
?>

<html>
<head>
    <title>Compra Finalizada</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="header">
        <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">
    </div>
    <div class="container">
        <div class="section">
            <div class="card">
                <h2>✅ Compra Finalizada!</h2>
                <p>
                    Obrigado pela sua compra.
                    Seu pedido foi realizado com sucesso.
                </p>
                <br>
                <a href="index.php">
                    <button>
                        Voltar para Loja
                    </button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
