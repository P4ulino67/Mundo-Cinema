<?php

Function teste_login($sessao) {
   if($sessao != "ok"){
        header("Location: index.php?erro=1");
        exit;
    }
	}

Function banco($server, $user, $password, $db, $consulta)
	{
		
	 $banco =  new mysqli($server, $user, $password, $db);
	 if ($banco->connect_error) {
            echo "Falha de conexão referência: (".$banco->connect_errno.") - ".$banco->connect_error;
				echo "<a href = 'incluir.php'> <img src = 'img/fundo/voltar.png' / width = 40 heigth= 40> </a>";
            exit();
	 }
	 if (!$resultado = $banco->query($consulta)) {
		        echo "Falha na consulta referência: (".$banco->errno.") - ".$banco->error;
				  echo "<a href = 'incluir.php'> <img src = 'img/fundo/voltar.png' / width = 40 heigth= 40> </a>";
				  exit();
	 }
    $banco->close();
	 return $resultado;
	
	}
	
    

// para usar o form  escreva null nos campos e botões não usados
// $action = action do form
// $var = campos
// $b = botão
function form($action,$var1,$var2,$var3,$var4,$var5,$var6,$var7,$b1,$b2,$b3){
                echo "
                <style type='text/css'>
                label.incluir {
                        display: inline-block;
                        width: 120px;
                }
                </style>";
               echo"<fieldset>";
               echo"<form action='$action'method='post'>";
               if(isset($var1)){
                               echo "<label for= $var1 class='incluir'>$var1:</label>";
                               echo "<input type='text' name='$var1'/><br/>";
               }
                if(isset($var2)){
                               echo "<label for= $var2 class='incluir'>$var2:</label>";
                               echo "<input type='text' name='$var2'/><br/>";
               }
                if(isset($var3)){
                               echo "<label for= $var3 class='incluir'>$var3:</label>";
                               echo "<input type='text' name='$var3'/><br/>";
               }
                if(isset($var4)){
                               echo "<label for= $var4 class='incluir'>$var4:</label>";
                               echo "<input type='text' name='$var4'/><br/>";
               }
                if(isset($var5)){
                               echo "<label for= $var5 class='incluir'>$var5:</label>";
                               echo "<input type='text' name='$var5'/><br/>";
               }
                if(isset($var6)){
                               echo "<label for= $var6 class='incluir'>$var6:</label>";
                               echo "<input type='text' name='$var6'/><br/>";
               }
                if(isset($var7)){
                               echo "<label for= $var7 class='incluir'>$var7:</label>";
                               echo "<input type='text' name='$var7'/><br/>";
               }
	      
	      if(isset($b1)) echo"<input type='submit' value='$b1' name='$b1'/>";
	      if(isset($b2)) echo"<input type='submit' value='$b2' name='$b2'/>";
	      if(isset($b3)) echo"<input type='submit' value='$b3' name='$b3'/>";

        echo"</form>";
       echo"</fieldset>";
}

function XML($label,$x1,$x2,$x3,$x4,$x5,$x6,$x7,$x8,$x9,$x10,$file){
	
   $xml = '<?xml version="1.0" encoding="utf-8"?>';
   $xml .= '<links>';
	$xml .= '<link>';
	if(isset($x1)) $xml .= '<'.$label[0].'>'. $x1 .'</'.$label[0].'>';
	if(isset($x2))$xml .= '<'.$label[1].'>'. $x2.'</'.$label[1].'>';
	if(isset($x3))$xml .= '<'.$label[2].'>'. $x3.'</'.$label[2].'>';
   if(isset($x4))	$xml .= '<'.$label[3].'>'. $x4.'</'.$label[3].'>';
	if(isset($x5))$xml .= '<'.$label[4].'>'. $x5.'</'.$label[4].'>';
	if(isset($x6))$xml .= '<'.$label[5].'>'. $x6.'</'.$label[5].'>';
	if(isset($x7))$xml .= '<'.$label[6].'>'. $x7.'</'.$label[6].'>';
   if(isset($x8))	$xml .= '<'.$label[7].'>'. $x8.'</'.$label[7].'>';
	if(isset($x9))$xml .= '<'.$label[8].'>'. $x8.'</'.$label[8].'>';
   if(isset($x10))	$xml .= '<'.$label[9].'>'. $x10.'</'.$label[9].'>';
   $fim = count($label) - 1;
   $xml .= '<modo>'. $label[$fim].'</modo>';
	$xml .= '</link>';

// Fechamento da raiz
$xml .= '</links>';

$fp = fopen($file, 'w+');
fwrite($fp, $xml);
fclose($fp);
        
}

function Envia_doc($host_ftp, $user_ftp, $pass_ftp, $file_origem,$file_destino){

$ftp_con = ftp_connect($host_ftp);
ftp_login($ftp_con,$user_ftp,$pass_ftp);
ftp_pasv($ftp_con, true);
ftp_put($ftp_con,$file_destino,$file_origem, FTP_ASCII);
ftp_close($ftp_con); 
        
}
function recebe_doc($host_ftp, $user_ftp, $pass_ftp, $file_origem,$file_destino){
     $ftp = ftp_connect($host_ftp);
     ftp_login($ftp,$user_ftp,$pass_ftp);
     ftp_pasv($ftp, true);
     ftp_get($ftp,$file_destino, $file_origem, FTP_BINARY);
     ftp_close($ftp);
}

function carregarXML($folder,$salvar){
   include "cons.php";
  // Listar XMLs da pasta
  if ($handle = opendir($folder)) {
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != "..") {
        // Carregar XML usando o simplexml
        $ler = $folder . '/' . $entry;
        if ($ler != NULL){
          $xml = simplexml_load_file($ler);
          unlink($ler);
          if ($salvar == 1) salvarXML($server,$user,$password,$db, $xml);
        }
       }
    }
    closedir($handle);
  }
}

// Envia um e-mail em texto simples usando a função mail() do PHP
// Retorna true se o PHP conseguiu repassar a mensagem ao mecanismo de envio
function enviar_email($destinatario, $assunto, $mensagem){
    include "cons.php";

    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $cabecalhos = "From: $nome_loja <$email_remetente>\r\n";
    $cabecalhos .= "Reply-To: $email_remetente\r\n";
    $cabecalhos .= "MIME-Version: 1.0\r\n";
    $cabecalhos .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Codifica o assunto para os acentos chegarem corretos
    $assunto = '=?UTF-8?B?' . base64_encode($assunto) . '?=';

    return @mail($destinatario, $assunto, $mensagem, $cabecalhos);
}

// Só deixa continuar se a conta logada for administradora
function exigir_admin(){
    if (empty($_SESSION['Admin'])) {
        header("Location: index.php");
        exit();
    }
}

// Recebe um item de $_FILES, valida e move a imagem para a pasta indicada (padrão: uploads)
// Retorna o caminho salvo (ex.: uploads/abc.jpg), '' se nenhum arquivo foi escolhido
// ou false em caso de erro (a explicação fica em $erro)
function salvar_foto($arquivo, &$erro, $pasta_destino = 'uploads'){
    $erro = '';

    if (!is_array($arquivo) || !isset($arquivo['error']) || is_array($arquivo['error'])) {
        $erro = 'Arquivo não recebido.';
        return false;
    }

    if ($arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE || $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        $erro = 'A foto é maior que o limite permitido pelo servidor.';
        return false;
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Falha no upload da foto. Código: ' . $arquivo['error'];
        return false;
    }

    $limite = 2 * 1024 * 1024;
    if ($arquivo['size'] <= 0 || $arquivo['size'] > $limite) {
        $erro = 'A foto deve ter até 2 MB.';
        return false;
    }

    $tipos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png'
    ];

    $info = finfo_open(FILEINFO_MIME_TYPE);
    if ($info === false) {
        $erro = 'A verificação do arquivo falhou.';
        return false;
    }

    $mime = finfo_file($info, $arquivo['tmp_name']);
    finfo_close($info);

    if ($mime === false || !isset($tipos[$mime])) {
        $erro = 'Formato inválido: use uma imagem JPG ou PNG.';
        return false;
    }

    $pasta = __DIR__ . '/' . $pasta_destino . '/';

    if (!is_dir($pasta)) {
        if (!mkdir($pasta, 0755, true)) {
            $erro = 'Não foi possível criar a pasta ' . $pasta_destino . '.';
            return false;
        }
    }

    // Nome gerado pelo servidor, para não sobrescrever nem confiar no nome original
    $nomeNovo = bin2hex(random_bytes(16));
    $nomeNovo .= '.' . $tipos[$mime];

    $origem = $arquivo['tmp_name'];
    $destino = $pasta . $nomeNovo;

    if (move_uploaded_file($origem, $destino)) {
        return $pasta_destino . '/' . $nomeNovo;
    }

    $erro = 'Erro ao mover a imagem para a pasta ' . $pasta_destino . '.';
    return false;
}

// Apaga a imagem de um filme, mas só se ela foi enviada pelo painel (pasta img/produtos)
// As imagens originais da pasta img são usadas também na página de gêneros
function apagar_imagem_produto($imagem){
    if (strpos($imagem, 'img/produtos/') === 0) {
        $arquivo = __DIR__ . '/img/produtos/' . basename($imagem);
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}

function salvarXML($server, $user, $password, $db, $xml){

    // Gerar SQL de inserção no banco de dados.
    if ($xml->link->modo == "incluir") $sql = "INSERT INTO vendas (num_cartao, nome_titular, validade, cod_seguranca, num_venda, valor, cod_cliente) VALUES('".$xml->link->num_cartao."', '".$xml->link->nome."', '".$xml->link->validade."',".$xml->link->cod_seg.",".$xml->link->num_venda.",'".$xml->link->valor."',".$xml->link->cod_cliente.")";
    if ($xml->link->modo == "lista") $sql = "select * vendas order by nome_titular";
    $resultado = banco($server, $user, $password, $db, $sql);
    return $resultado;
  }
  




?>
