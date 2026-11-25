<?php
session_start();

include "cons.php";
require_once "DLL.php";

// Só a conta administradora entra no painel
exigir_admin();

// Filme escolhido para editar (botão "Editar" ou volta depois de um erro ao salvar)
$editar_id = (int) ($_POST['editar'] ?? $_SESSION['editar_id'] ?? 0);
unset($_SESSION['editar_id']);

$editando = null;

if ($editar_id > 0) {
    $consulta = "SELECT * FROM produtos WHERE id = '$editar_id'";
    $resultado = banco($server, $user, $password, $db, $consulta);
    $editando = $resultado->fetch_assoc();
}

// Valores do formulário: os do filme em edição, ou vazios para um filme novo
$form_nome = $editando['nome'] ?? '';
$form_tipo = $editando['tipo'] ?? '';
$form_descricao = $editando['descricao'] ?? '';
$form_preco = isset($editando['preco']) ? number_format($editando['preco'], 2, ',', '') : '';

// Lista de filmes da loja
$consulta = "SELECT * FROM produtos ORDER BY nome";
$produtos = banco($server, $user, $password, $db, $consulta);
?>

<html>
<head>
    <title>Painel de Administração - Mundo do Cinema</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="header">
        <img src="img/logo-mundo-cinema.jpg" alt="Mundo Cinema" class="logo-site">
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="admin.php">Painel Admin</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </nav>
    </div>

    <?php
    if (isset($_SESSION['mensagem'])) {
        echo "<div class='mensagem'>" . $_SESSION['mensagem'] . "</div>";
        unset($_SESSION['mensagem']);
    }
    ?>

    <div class="container">
        <div class="section">

            <div class="card admin-form">

                <?php if ($editando) { ?>
                    <h2>✏️ Editar filme</h2>
                <?php } else { ?>
                    <h2>➕ Adicionar filme</h2>
                <?php } ?>

                <?php
                if (isset($_SESSION['erro_admin'])) {
                    echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['erro_admin']) . "</p>";
                    unset($_SESSION['erro_admin']);
                }
                ?>

                <form action="banco.php" method="POST" enctype="multipart/form-data">

                    <?php if ($editando) { ?>
                        <input type="hidden" name="id" value="<?php echo (int) $editando['id']; ?>">
                    <?php } ?>

                    <label for="nome">Nome do filme:</label>
                    <input type="text" id="nome" name="nome" maxlength="255" value="<?php echo htmlspecialchars($form_nome); ?>" required>

                    <label for="tipo">Gênero:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Escolha o gênero</option>
                        <?php
                        foreach ($generos as $genero) {
                            $selecionado = ($genero == $form_tipo) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($genero) . "' $selecionado>" . htmlspecialchars($genero) . "</option>";
                        }
                        ?>
                    </select>

                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" maxlength="500" rows="4" required><?php echo htmlspecialchars($form_descricao); ?></textarea>

                    <label for="preco">Preço (R$):</label>
                    <input type="text" id="preco" name="preco" placeholder="Ex.: 19,90" inputmode="decimal" value="<?php echo htmlspecialchars($form_preco); ?>" required>

                    <?php if ($editando) { ?>
                        <label>Imagem atual:</label>
                        <img src="<?php echo htmlspecialchars($editando['imagem']); ?>" alt="Imagem atual" class="admin-miniatura">

                        <label for="imagem">Trocar imagem (opcional, JPG ou PNG até 2 MB):</label>
                        <input type="file" id="imagem" name="imagem" accept=".jpg,.jpeg,.png">

                        <button type="submit" name="B10" value="1">Salvar alterações</button>
                    <?php } else { ?>
                        <label for="imagem">Imagem do filme (JPG ou PNG até 2 MB):</label>
                        <input type="file" id="imagem" name="imagem" accept=".jpg,.jpeg,.png" required>

                        <button type="submit" name="B9" value="1">Adicionar filme</button>
                    <?php } ?>

                </form>

                <?php if ($editando) { ?>
                    <a href="admin.php"><button type="button">Cancelar edição</button></a>
                <?php } ?>

            </div>

            <h2>🎬 Filmes da loja</h2>

            <div class="grid-filmes">
                <?php
                while ($produto = $produtos->fetch_assoc()) {

                    $id_seguro = (int) $produto['id'];
                    $nome_seguro = htmlspecialchars($produto['nome']);
                    $tipo_seguro = htmlspecialchars($produto['tipo'] ?? '');
                    $descricao_segura = htmlspecialchars($produto['descricao']);
                    $imagem_segura = htmlspecialchars($produto['imagem']);
                    $preco_formatado = number_format($produto['preco'], 2, ',', '.');

                    echo "
                    <div class='card'>
                        <img src='{$imagem_segura}' alt='{$nome_seguro}'>

                        <h3> {$nome_seguro} </h3>

                        <p class='genero'> {$tipo_seguro} </p>

                        <p> {$descricao_segura} </p>

                        <p> <strong> R$ {$preco_formatado} </strong> </p>

                        <form action='admin.php' method='POST'>
                            <input type='hidden' name='editar' value='{$id_seguro}'>
                            <button type='submit'> Editar </button>
                        </form>

                        <form action='banco.php' method='POST' onsubmit=\"return confirm('Remover este filme da loja?');\">
                            <input type='hidden' name='id' value='{$id_seguro}'>
                            <button type='submit' name='B11' value='1' class='botao-remover'> Remover </button>
                        </form>
                    </div>
                    ";
                }
                ?>
            </div>

        </div>
    </div>
    <div class="footer">
        <p>&copy; Tasso Farias e Paulino Mendes</p>
    </div>
</body>
</html>
