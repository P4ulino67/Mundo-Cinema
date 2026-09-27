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
    unset($_SESSION['UsuarioId']);
    unset($_SESSION['Cpf']);

    $consulta = "INSERT INTO usuarios (id, nome, cpf, endereco, bairro, cidade, estado, cep) VALUES (NULL, '$nome', '$cpf', '$endereco', '$bairro', '$cidade', '$estado', '$cep')";

    banco($server, $user, $password, $db, $consulta);

    // Busca o id que acabou de ser gerado para esse usuário, para usar na etapa 2
    $consulta = "SELECT id FROM usuarios WHERE cpf = '$cpf'";

    $resultado = banco($server, $user, $password, $db, $consulta);

    $linha = $resultado->fetch_assoc();

    $_SESSION['UsuarioId'] = $linha['id'];
    $_SESSION['Nome'] = $nome;

    header("Location: cadastro2.php");
    exit();
}


// B2: Cadastrar o login e a senha, vinculados ao usuário (etapa 2 do cadastro)
if (isset($B2)) {

    $usuario_id = $_SESSION['UsuarioId'];
    $senha = md5($senha);

    $consulta = "INSERT INTO login (id, login, senha, usuario_id) VALUES (NULL, '$login', '$senha', '$usuario_id')";

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

            $consulta = "SELECT * FROM usuarios WHERE id = '".$linha['usuario_id']."'";

            $resultado = banco($server, $user, $password, $db, $consulta);

            $usuario = $resultado->fetch_assoc();

            $_SESSION['usuario'] = $usuario['nome'];
            $_SESSION['Login'] = $linha['login'];
            $_SESSION['UsuarioId'] = $usuario['id'];
            $_SESSION['Cpf'] = $usuario['cpf'];

            // Se o usuário tentou comprar um filme antes de logar, completa a adição ao carrinho agora
            if (!empty($_POST['produto_id'])) {

                $produto_id = $_POST['produto_id'];
                $usuario_id = $usuario['id'];

                $consulta = "SELECT * FROM carrinho WHERE usuario_id = '$usuario_id' AND produto_id = '$produto_id'";
                $resultado = banco($server, $user, $password, $db, $consulta);
                $existente = $resultado->fetch_assoc();

                if ($existente) {
                    $nova_quantidade = $existente['quantidade'] + 1;
                    $consulta = "UPDATE carrinho SET quantidade = '$nova_quantidade' WHERE id = '".$existente['id']."'";
                } else {
                    $consulta = "INSERT INTO carrinho (id, usuario_id, produto_id, quantidade) VALUES (NULL, '$usuario_id', '$produto_id', 1)";
                }

                banco($server, $user, $password, $db, $consulta);

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

    $usuario_id = $_SESSION['UsuarioId'];

    $consulta = "SELECT carrinho.produto_id, carrinho.quantidade, produtos.nome, produtos.preco
                 FROM carrinho
                 JOIN produtos ON carrinho.produto_id = produtos.id
                 WHERE carrinho.usuario_id = '$usuario_id'";

    $resultado = banco($server, $user, $password, $db, $consulta);

    $itens = [];
    while ($linha = $resultado->fetch_assoc()) {
        $itens[] = $linha;
    }

    if (count($itens) == 0) {
        header("Location: carrinho.php");
        exit();
    }

    $pagamento = $_POST['pagamento'];

    $numero = rand(1000, 9999);
    $data = date('d/m/Y');
    $hora = date('H:i');

    $login = $_SESSION['Login'] ?? '';
    $cpf = $_SESSION['Cpf'] ?? '';

    foreach ($itens as $item) {

        $produto = $item['nome'];
        $valor = $item['preco'] * $item['quantidade'];

        $consulta = "INSERT INTO vendas (id, numero, login, cpf, produto, valor, data, hora, pagamento) VALUES (NULL, '$numero', '$login', '$cpf', '$produto', '$valor', '$data', '$hora', '$pagamento')";

        banco($server, $user, $password, $db, $consulta);
    }

    // Esvazia o carrinho desse usuário no banco
    $consulta = "DELETE FROM carrinho WHERE usuario_id = '$usuario_id'";
    banco($server, $user, $password, $db, $consulta);
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
