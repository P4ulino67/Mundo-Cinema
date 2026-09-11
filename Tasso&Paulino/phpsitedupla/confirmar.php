<?php
session_start();

$produto = isset($_POST['produto']) ? $_POST['produto'] : "Produto não informado";
$preco = isset($_POST['preco']) ? $_POST['preco'] : "0";
?>

<html>
<head>
    <title>Compra Confirmada</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<div class="header">
    <h1>🎬 Mundo do Cinema</h1>
</div>
<div class="container">
    <div class="card">
        <h2>✅ Compra realizada!</h2>
        <p><strong>Produto:</strong> <?= $produto ?></p>
        <p><strong>Preço:</strong> R$ <?= $preco ?></p>
        <br>
        <a href="index.php">
            <button>Voltar ao início</button>
        </a>
    </div>
</div>
</body>
</html>
