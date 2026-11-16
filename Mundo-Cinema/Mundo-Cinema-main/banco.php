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
    unset($_SESSION['Email']);
    unset($_SESSION['Foto']);

    // Confere o e-mail antes de gravar
    $email = trim($email ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_cadastro'] = "Informe um e-mail válido.";
        header("Location: cadastro1.php");
        exit();
    }

    $email = addslashes($email);

    // Foto de perfil (opcional): valida e move para a pasta uploads
    $foto = '';
    $erro_upload = '';

    if (isset($_FILES['foto'])) {

        $foto = salvar_foto($_FILES['foto'], $erro_upload);

        if ($foto === false) {
            $_SESSION['erro_cadastro'] = $erro_upload;
            header("Location: cadastro1.php");
            exit();
        }
    }

    $consulta = "INSERT INTO usuarios (id, nome, cpf, email, foto, endereco, bairro, cidade, estado, cep) VALUES (NULL, '$nome', '$cpf', '$email', '$foto', '$endereco', '$bairro', '$cidade', '$estado', '$cep')";

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

    if (!isset($_SESSION['UsuarioId'])) {
        header("Location: cadastro1.php");
        exit();
    }

    $usuario_id = $_SESSION['UsuarioId'];
    $senha = md5($senha);

    $consulta = "INSERT INTO login (id, login, senha, usuario_id) VALUES (NULL, '$login', '$senha', '$usuario_id')";

    banco($server, $user, $password, $db, $consulta);

    // Envia o e-mail de boas-vindas para o endereço informado na etapa 1
    $consulta = "SELECT nome, email FROM usuarios WHERE id = '$usuario_id'";
    $resultado = banco($server, $user, $password, $db, $consulta);
    $usuario = $resultado->fetch_assoc();

    $_SESSION['mensagem'] = "✅ Conta criada com sucesso! Faça seu login.";

    if ($usuario && !empty($usuario['email'])) {

        $assunto = "Bem-vindo ao Mundo do Cinema";

        $mensagem = "Olá, " . $usuario['nome'] . "!\r\n\r\n";
        $mensagem .= "Sua conta no Mundo do Cinema foi criada com sucesso.\r\n";
        $mensagem .= "Seu login de acesso é: " . $_POST['login'] . "\r\n\r\n";
        $mensagem .= "Aproveite a nossa loja de filmes!\r\n";

        if (enviar_email($usuario['email'], $assunto, $mensagem)) {
            $_SESSION['mensagem'] = "✅ Conta criada! Enviamos um e-mail de boas-vindas para " . htmlspecialchars($usuario['email']) . ".";
        }
    }

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
            $_SESSION['Email'] = $usuario['email'] ?? '';
            $_SESSION['Foto'] = $usuario['foto'] ?? '';

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

    $total = 0;
    $lista_email = "";

    foreach ($itens as $item) {

        $produto = $item['nome'];
        $valor = $item['preco'] * $item['quantidade'];

        $total += $valor;
        $lista_email .= "- " . $item['quantidade'] . "x " . $produto . " - R$ " . number_format($valor, 2, ',', '.') . "\r\n";

        $consulta = "INSERT INTO vendas (id, numero, login, cpf, produto, valor, data, hora, pagamento) VALUES (NULL, '$numero', '$login', '$cpf', '$produto', '$valor', '$data', '$hora', '$pagamento')";

        banco($server, $user, $password, $db, $consulta);
    }

    // Esvazia o carrinho desse usuário no banco
    $consulta = "DELETE FROM carrinho WHERE usuario_id = '$usuario_id'";
    banco($server, $user, $password, $db, $consulta);

    // Envia o e-mail de confirmação do pedido para o usuário
    $consulta = "SELECT nome, email FROM usuarios WHERE id = '$usuario_id'";
    $resultado = banco($server, $user, $password, $db, $consulta);
    $usuario = $resultado->fetch_assoc();

    $email_enviado = false;
    $email_usuario = $usuario['email'] ?? '';

    if (!empty($email_usuario)) {

        $assunto = "Confirmação do pedido " . $numero . " - Mundo do Cinema";

        $mensagem = "Olá, " . $usuario['nome'] . "!\r\n\r\n";
        $mensagem .= "Recebemos o seu pedido. Confira os detalhes:\r\n\r\n";
        $mensagem .= "Número do pedido: " . $numero . "\r\n";
        $mensagem .= "Data: " . $data . " às " . $hora . "\r\n";
        $mensagem .= "Forma de pagamento: " . $pagamento . "\r\n\r\n";
        $mensagem .= "Itens:\r\n";
        $mensagem .= $lista_email;
        $mensagem .= "\r\nTotal: R$ " . number_format($total, 2, ',', '.') . "\r\n\r\n";
        $mensagem .= "Obrigado por comprar no Mundo do Cinema!\r\n";

        $email_enviado = enviar_email($email_usuario, $assunto, $mensagem);
    }
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
                    <p><strong>Total:</strong> R$ <?php echo number_format($total, 2, ',', '.'); ?></p>

                    <?php
                    if ($email_enviado) {
                        echo "<p>📧 Enviamos a confirmação do pedido para " . htmlspecialchars($email_usuario) . ".</p>";
                    } else if (!empty($email_usuario)) {
                        echo "<p style='color:red;'>Não foi possível enviar o e-mail de confirmação.</p>";
                    } else {
                        echo "<p>Cadastre um e-mail no seu perfil para receber a confirmação dos pedidos.</p>";
                    }
                    ?>

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


// B5: Atualizar o perfil (e-mail e foto) do usuário logado
if (isset($B5)) {

    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }

    $usuario_id = $_SESSION['UsuarioId'];

    $email = trim($email ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_perfil'] = "Informe um e-mail válido.";
        header("Location: perfil.php");
        exit();
    }

    $foto = '';
    $erro_upload = '';

    if (isset($_FILES['foto'])) {

        $foto = salvar_foto($_FILES['foto'], $erro_upload);

        if ($foto === false) {
            $_SESSION['erro_perfil'] = $erro_upload;
            header("Location: perfil.php");
            exit();
        }
    }

    $consulta = "UPDATE usuarios SET email = '" . addslashes($email) . "' WHERE id = '$usuario_id'";
    banco($server, $user, $password, $db, $consulta);

    $_SESSION['Email'] = $email;

    // Se uma foto nova foi enviada, grava o caminho e apaga a antiga da pasta uploads
    if ($foto != '') {

        $consulta = "SELECT foto FROM usuarios WHERE id = '$usuario_id'";
        $resultado = banco($server, $user, $password, $db, $consulta);
        $linha = $resultado->fetch_assoc();

        $antiga = $linha['foto'] ?? '';

        $consulta = "UPDATE usuarios SET foto = '$foto' WHERE id = '$usuario_id'";
        banco($server, $user, $password, $db, $consulta);

        if ($antiga != '' && is_file(__DIR__ . '/uploads/' . basename($antiga))) {
            unlink(__DIR__ . '/uploads/' . basename($antiga));
        }

        $_SESSION['Foto'] = $foto;
    }

    $_SESSION['mensagem'] = "✅ Perfil atualizado!";

    header("Location: perfil.php");
    exit();
}
?>
