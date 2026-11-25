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
    unset($_SESSION['Admin']);

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
            $_SESSION['Admin'] = (int) ($linha['admin'] ?? 0);

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


// B6: Recuperar senha, etapa 1 (recebe o e-mail e envia o código de verificação)
if (isset($B6)) {

    $email = trim($email ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_recuperar'] = "Informe um e-mail válido.";
        header("Location: recuperar.php");
        exit();
    }

    $email_sql = addslashes($email);

    // Apaga códigos antigos desse e-mail, para valer sempre só o último enviado
    $consulta = "DELETE FROM recuperacao WHERE email = '$email_sql'";
    banco($server, $user, $password, $db, $consulta);

    // Procura as contas (login) ligadas a esse e-mail
    $consulta = "SELECT usuarios.nome, login.login
                 FROM usuarios
                 JOIN login ON login.usuario_id = usuarios.id
                 WHERE usuarios.email = '$email_sql'";

    $resultado = banco($server, $user, $password, $db, $consulta);

    $contas = [];
    while ($linha = $resultado->fetch_assoc()) {
        $contas[] = $linha;
    }

    if (count($contas) > 0) {

        // Código de 6 dígitos; no banco fica só o hash, válido por 15 minutos
        $codigo = (string) random_int(100000, 999999);
        $codigo_hash = hash('sha256', $codigo);

        $consulta = "INSERT INTO recuperacao (id, email, codigo, expira, tentativas) VALUES (NULL, '$email_sql', '$codigo_hash', DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0)";
        banco($server, $user, $password, $db, $consulta);

        $logins = "";
        foreach ($contas as $conta) {
            $logins .= "- " . $conta['login'] . "\r\n";
        }

        $assunto = "Código para recuperar sua senha - Mundo do Cinema";

        $mensagem = "Olá, " . $contas[0]['nome'] . "!\r\n\r\n";
        $mensagem .= "Recebemos um pedido para trocar a senha da sua conta.\r\n\r\n";
        $mensagem .= "Seu código de verificação é: " . $codigo . "\r\n\r\n";
        $mensagem .= "O código vale por 15 minutos.\r\n";
        $mensagem .= "Login(s) ligado(s) a este e-mail:\r\n" . $logins . "\r\n";
        $mensagem .= "Se não foi você que pediu, ignore este e-mail. Sua senha continua a mesma.\r\n";

        if (!enviar_email($email, $assunto, $mensagem)) {

            $consulta = "DELETE FROM recuperacao WHERE email = '$email_sql'";
            banco($server, $user, $password, $db, $consulta);

            $_SESSION['erro_recuperar'] = "Não foi possível enviar o e-mail agora. Tente novamente mais tarde.";
            header("Location: recuperar.php");
            exit();
        }
    }

    // A mesma mensagem aparece mesmo se o e-mail não estiver cadastrado,
    // para o site não revelar quais e-mails têm conta
    $_SESSION['recuperar_email'] = $email;
    $_SESSION['mensagem'] = "📧 Se este e-mail estiver cadastrado, você receberá um código em instantes. Confira também a caixa de spam.";

    header("Location: recuperar.php");
    exit();
}


// B7: Recuperar senha, etapa 2 (confere o código e grava a senha nova)
if (isset($B7)) {

    if (!isset($_SESSION['recuperar_email'])) {
        header("Location: recuperar.php");
        exit();
    }

    $email_sql = addslashes($_SESSION['recuperar_email']);

    $codigo = trim($codigo ?? '');
    $senha = $senha ?? '';
    $confirmar = $confirmar ?? '';

    if (!preg_match('/^[0-9]{6}$/', $codigo)) {
        $_SESSION['erro_recuperar'] = "O código deve ter 6 números.";
        header("Location: recuperar.php");
        exit();
    }

    if ($senha == '' || $senha !== $confirmar) {
        $_SESSION['erro_recuperar'] = "As senhas não conferem.";
        header("Location: recuperar.php");
        exit();
    }

    // Só vale o código que ainda não expirou
    $consulta = "SELECT * FROM recuperacao WHERE email = '$email_sql' AND expira > NOW() ORDER BY id DESC LIMIT 1";
    $resultado = banco($server, $user, $password, $db, $consulta);
    $pedido = $resultado->fetch_assoc();

    if (!$pedido) {
        $_SESSION['erro_recuperar'] = "Código inválido ou expirado. Clique em \"Reenviar código\".";
        header("Location: recuperar.php");
        exit();
    }

    // Depois de 5 tentativas erradas o código é descartado
    if ($pedido['tentativas'] >= 5) {

        $consulta = "DELETE FROM recuperacao WHERE id = '" . $pedido['id'] . "'";
        banco($server, $user, $password, $db, $consulta);

        $_SESSION['erro_recuperar'] = "Muitas tentativas erradas. Clique em \"Reenviar código\" para receber outro.";
        header("Location: recuperar.php");
        exit();
    }

    if (!hash_equals($pedido['codigo'], hash('sha256', $codigo))) {

        $consulta = "UPDATE recuperacao SET tentativas = tentativas + 1 WHERE id = '" . $pedido['id'] . "'";
        banco($server, $user, $password, $db, $consulta);

        $_SESSION['erro_recuperar'] = "Código incorreto.";
        header("Location: recuperar.php");
        exit();
    }

    // Código certo: grava a senha nova nas contas ligadas a esse e-mail
    $senha = md5($senha);

    $consulta = "UPDATE login
                 JOIN usuarios ON login.usuario_id = usuarios.id
                 SET login.senha = '$senha'
                 WHERE usuarios.email = '$email_sql'";

    banco($server, $user, $password, $db, $consulta);

    $consulta = "DELETE FROM recuperacao WHERE email = '$email_sql'";
    banco($server, $user, $password, $db, $consulta);

    // Avisa por e-mail que a senha foi trocada
    $assunto = "Sua senha foi alterada - Mundo do Cinema";

    $mensagem = "Olá!\r\n\r\n";
    $mensagem .= "A senha da sua conta no Mundo do Cinema foi alterada em " . date('d/m/Y') . " às " . date('H:i') . ".\r\n\r\n";
    $mensagem .= "Se não foi você, recupere a senha novamente pelo site o quanto antes.\r\n";

    enviar_email($_SESSION['recuperar_email'], $assunto, $mensagem);

    unset($_SESSION['recuperar_email']);

    $_SESSION['mensagem'] = "✅ Senha alterada com sucesso! Faça seu login com a senha nova.";

    header("Location: login.php");
    exit();
}


// B8: Recuperar senha, voltar para a etapa 1 (usar outro e-mail)
if (isset($B8)) {

    unset($_SESSION['recuperar_email']);

    header("Location: recuperar.php");
    exit();
}


// B9 e B10: Painel de administração, adicionar (B9) ou alterar (B10) um filme
if (isset($B9) || isset($B10)) {

    exigir_admin();

    $produto_id = (int) ($_POST['id'] ?? 0);
    $voltar = "admin.php";

    // No caso de erro ao alterar, o painel reabre o formulário de edição desse filme
    if (isset($B10)) {
        $_SESSION['editar_id'] = $produto_id;
    }

    $nome = trim($_POST['nome'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));

    $erro = '';

    if ($nome == '' || mb_strlen($nome) > 255) {
        $erro = "Informe o nome do filme (até 255 caracteres).";
    } else if (!in_array($tipo, $generos)) {
        $erro = "Escolha um gênero da lista.";
    } else if ($descricao == '' || mb_strlen($descricao) > 500) {
        $erro = "Informe a descrição (até 500 caracteres).";
    } else if (!is_numeric($preco) || $preco <= 0 || $preco > 99999) {
        $erro = "Informe um preço válido, por exemplo 19,90.";
    }

    if ($erro != '') {
        $_SESSION['erro_admin'] = $erro;
        header("Location: $voltar");
        exit();
    }

    $atual = null;

    if (isset($B10)) {

        $consulta = "SELECT * FROM produtos WHERE id = '$produto_id'";
        $resultado = banco($server, $user, $password, $db, $consulta);
        $atual = $resultado->fetch_assoc();

        if (!$atual) {
            unset($_SESSION['editar_id']);
            $_SESSION['erro_admin'] = "Filme não encontrado.";
            header("Location: $voltar");
            exit();
        }
    }

    // Imagem do filme: obrigatória ao adicionar, opcional ao alterar
    $erro_upload = '';
    $imagem = salvar_foto($_FILES['imagem'] ?? null, $erro_upload, 'img/produtos');

    if ($imagem === false) {
        $_SESSION['erro_admin'] = $erro_upload;
        header("Location: $voltar");
        exit();
    }

    if (isset($B9) && $imagem == '') {
        $_SESSION['erro_admin'] = "Escolha a imagem do filme.";
        header("Location: $voltar");
        exit();
    }

    $nome_sql = addslashes($nome);
    $tipo_sql = addslashes($tipo);
    $descricao_sql = addslashes($descricao);
    $preco = number_format((float) $preco, 2, '.', '');

    if (isset($B9)) {

        $consulta = "INSERT INTO produtos (id, nome, tipo, descricao, preco, imagem) VALUES (NULL, '$nome_sql', '$tipo_sql', '$descricao_sql', '$preco', '$imagem')";
        banco($server, $user, $password, $db, $consulta);

        $_SESSION['mensagem'] = "✅ Filme \"" . htmlspecialchars($nome) . "\" adicionado à loja!";

    } else {

        $consulta = "UPDATE produtos SET nome = '$nome_sql', tipo = '$tipo_sql', descricao = '$descricao_sql', preco = '$preco' WHERE id = '$produto_id'";
        banco($server, $user, $password, $db, $consulta);

        // Imagem nova: grava o caminho e apaga a antiga, se ela foi enviada pelo painel
        if ($imagem != '') {

            $consulta = "UPDATE produtos SET imagem = '$imagem' WHERE id = '$produto_id'";
            banco($server, $user, $password, $db, $consulta);

            apagar_imagem_produto($atual['imagem']);
        }

        unset($_SESSION['editar_id']);

        $_SESSION['mensagem'] = "✅ Filme \"" . htmlspecialchars($nome) . "\" atualizado!";
    }

    header("Location: $voltar");
    exit();
}


// B11: Painel de administração, remover um filme da loja
if (isset($B11)) {

    exigir_admin();

    $produto_id = (int) ($_POST['id'] ?? 0);

    $consulta = "SELECT * FROM produtos WHERE id = '$produto_id'";
    $resultado = banco($server, $user, $password, $db, $consulta);
    $produto = $resultado->fetch_assoc();

    if ($produto) {

        // Tira o filme dos carrinhos antes, por causa da ligação entre as tabelas
        $consulta = "DELETE FROM carrinho WHERE produto_id = '$produto_id'";
        banco($server, $user, $password, $db, $consulta);

        $consulta = "DELETE FROM produtos WHERE id = '$produto_id'";
        banco($server, $user, $password, $db, $consulta);

        apagar_imagem_produto($produto['imagem']);

        $_SESSION['mensagem'] = "🗑️ Filme \"" . htmlspecialchars($produto['nome']) . "\" removido da loja.";
    }

    header("Location: admin.php");
    exit();
}


// B12: Cadastro, voltar da etapa 2 para a etapa 1
// Apaga os dados pessoais gravados na etapa 1 (ainda sem login), para o CPF poder ser usado de novo
if (isset($B12)) {

    if (isset($_SESSION['UsuarioId']) && !isset($_SESSION['usuario'])) {

        $usuario_id = (int) $_SESSION['UsuarioId'];

        // Só apaga se essa pessoa ainda não tiver login criado
        $consulta = "SELECT id FROM login WHERE usuario_id = '$usuario_id'";
        $resultado = banco($server, $user, $password, $db, $consulta);

        if (!$resultado->fetch_assoc()) {

            $consulta = "SELECT foto FROM usuarios WHERE id = '$usuario_id'";
            $resultado = banco($server, $user, $password, $db, $consulta);
            $linha = $resultado->fetch_assoc();

            $consulta = "DELETE FROM usuarios WHERE id = '$usuario_id'";
            banco($server, $user, $password, $db, $consulta);

            // Apaga também a foto enviada na etapa 1, se houver
            $foto = $linha['foto'] ?? '';
            if ($foto != '' && is_file(__DIR__ . '/uploads/' . basename($foto))) {
                unlink(__DIR__ . '/uploads/' . basename($foto));
            }
        }

        unset($_SESSION['UsuarioId']);
        unset($_SESSION['Nome']);
    }

    header("Location: cadastro1.php");
    exit();
}
?>
