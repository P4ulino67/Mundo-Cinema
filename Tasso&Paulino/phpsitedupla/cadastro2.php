<html>
<head>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

    <div class="container">
        <div class="card">

            <h2>Crie seu login</h2>

            <form action="salvar_login.php" method="POST">
                <input type="text" name="login" placeholder="Login" required><br><br>
                <input type="password" name="senha" placeholder="Senha" required><br><br>

                <input type="hidden" name="cpf" value="<?= $_GET['cpf'] ?>">

                <button>Cadastrar</button>
            </form>

        </div>
    </div>

</body>
</html>