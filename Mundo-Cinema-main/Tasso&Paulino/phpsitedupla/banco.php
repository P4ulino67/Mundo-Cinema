<?php
session_start();

include "cons.php";
require_once "DLL.php";

extract($_POST);

// B1: Cadastrar os dados pessoais do usuário (etapa 1 do cadastro)
if (isset($B1)) {

    // Limpa qualquer sessão de login anterior, para não misturar com a conta nova
    unset($_SESSION['usuario']);
    unset($_SESSION['Login']);
    unset($_SESSION['Cpf']);
    unset($_SESSION['carrinho']);

    $_SESSION['Cpf'] = $cpf;
    $_SESSION['Nome'] = $nome;

    $consulta = "INSERT INTO usuarios (id, nome, cpf, endereco, bairro, cidade, estado, cep) VALUES (NULL, '$nome', '$cpf', '$endereco', '$bairro', '$cidade', '$estado', '$cep')";

    banco($server, $user, $password, $db, $consulta);

    header("Location: cadastro2.php");
    exit();
}


// B2: Cadastrar o login e a senha, vinculados ao CPF (etapa 2 do cadastro)
if (isset($B2)) {

    $cpf = $_SESSION['Cpf'];
    $senha = md5($senha);

    $consulta = "INSERT INTO login (id, login, senha, cpf) VALUES (NULL, '$login', '$senha', '$cpf')";

    banco($server, $user, $password, $db, $consulta);

    header("Location: login.php");
    exit();
}


// B3: Fazer login (autenticação de verdade, cruzando as duas tabelas)
if (isset($B3)) {

    $consulta = "SELECT * FROM login WHERE login = '$login'";

    $resultado = banco($server, $user, $password, $db, $consulta);

    $linha = $resultado->fetch_assoc();

    if ($linha) {

        $senha = md5($senha);

        if ($senha == $linha['senha']) {

            $consulta = "SELECT * FROM usuarios WHERE cpf = '".$linha['cpf']."'";

            $resultado = banco($server, $user, $password, $db, $consulta);

            $usuario = $resultado->fetch_assoc();

            $_SESSION['usuario'] = $usuario['nome'];
            $_SESSION['Login'] = $linha['login'];
            $_SESSION['Cpf'] = $linha['cpf'];

            // Se o usuário tentou comprar um filme antes de logar, completa a adição ao carrinho agora
            if (!empty($_POST['produto'])) {
                if (!isset($_SESSION['carrinho'])) {
                    $_SESSION['carrinho'] = [];
                }
                $_SESSION['carrinho'][] = [
                    "produto" => $_POST['produto'],
                    "preco" => $_POST['preco'] ?? ''
                ];
                $_SESSION['mensagem'] = "✅ Produto adicionado ao carrinho!";
            }

            header("Location: index.php");
            exit();

        } else {

            $_SESSION['erro'] = 1;
            header("Location: login.php");
            exit();
        }

    } else {

        $_SESSION['erro'] = 1;
        header("Location: login.php");
        exit();
    }
}


// B4: Finalizar a compra, gravando cada item do carrinho como uma venda
if (isset($B4)) {

    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
        header("Location: carrinho.php");
        exit();
    }

    $pagamento = $_POST['pagamento'];

    $numero = rand(1000, 9999);
    $data = date('d/m/Y');
    $hora = date('H:i');

    $login = $_SESSION['Login'] ?? '';
    $cpf = $_SESSION['Cpf'] ?? '';

    foreach ($_SESSION['carrinho'] as $item) {

        $produto = $item['produto'];
        $valor = $item['preco'];

        $consulta = "INSERT INTO vendas (id, numero, login, cpf, produto, valor, data, hora, pagamento) VALUES (NULL, '$numero', '$login', '$cpf', '$produto', '$valor', '$data', '$hora', '$pagamento')";

        banco($server, $user, $password, $db, $consulta);
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

                    <p><strong>Número do pedido:</strong> <?php echo $numero; ?></p>
                    <p><strong>Forma de pagamento:</strong> <?php echo htmlspecialchars($pagamento); ?></p>

                    <br>

                    <a href="index.php">
                        <button>Voltar para Loja</button>
                    </a>

                </div>
            </div>
        </div>

    </body>

    </html>
    <?php
    exit();
}
?>
