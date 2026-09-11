
<?php
 
function teste_login($sessao) {
    if ($sessao != "ok") {
        header("Location: index.php?erro=1");
        exit;
    }
}
 
function banco($server, $user, $password, $db, $consulta) {
    $banco = new mysqli($server, $user, $password, $db);
    if ($banco->connect_error) {
        echo "Falha de conexão referência: (".$banco->connect_errno.") - ".$banco->connect_error;
        echo "<a href='incluir.php'><img src='img/fundo/voltar.png' width='40' height='40'/></a>";
        exit();
    }
    if (!$resultado = $banco->query($consulta)) {
        echo "Falha na consulta referência: (".$banco->errno.") - ".$banco->error;
        echo "<a href='incluir.php'><img src='img/fundo/voltar.png' width='40' height='40'/></a>";
        exit();
    }
    $banco->close();
    return $resultado;
}
 
// para usar o form escreva null nos campos e botões não usados
// $action = action do form
// $var = campos
// $b  = botões
function form($action, $var1, $var2, $var3, $var4, $var5, $var6, $var7, $b1, $b2, $b3) {
    echo "<style type='text/css'>label.incluir{display:inline-block;width:120px;}</style>";
    echo "<fieldset><form action='$action' method='post'>";
    foreach ([$var1, $var2, $var3, $var4, $var5, $var6, $var7] as $campo) {
        if (isset($campo)) {
            echo "<label for='$campo' class='incluir'>$campo:</label>";
            echo "<input type='text' name='$campo'/><br/>";
        }
    }
    foreach ([$b1, $b2, $b3] as $botao) {
        if (isset($botao)) echo "<input type='submit' value='$botao' name='$botao'/>";
    }
    echo "</form></fieldset>";
}
 
// $label = array com os nomes das tags XML (o último elemento é usado como <modo>)
// $x1..$x10 = valores correspondentes a label[0]..label[9]
function XML($label, $x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $x10, $file) {
    $valores = [$x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $x10];
    $xml = '<?xml version="1.0" encoding="utf-8"?><links><link>';
    foreach ($valores as $i => $v) {
        if (isset($v)) $xml .= '<'.$label[$i].'>'.$v.'</'.$label[$i].'>';
    }
    $xml .= '<modo>'.$label[count($label) - 1].'</modo></link></links>';
    file_put_contents($file, $xml);
}
 
function Envia_doc($host_ftp, $user_ftp, $pass_ftp, $file_origem, $file_destino) {
    $ftp_con = ftp_connect($host_ftp);
    ftp_login($ftp_con, $user_ftp, $pass_ftp);
    ftp_pasv($ftp_con, true);
    ftp_put($ftp_con, $file_destino, $file_origem, FTP_ASCII);
    ftp_close($ftp_con);
}
 
function recebe_doc($host_ftp, $user_ftp, $pass_ftp, $file_origem, $file_destino) {
    $ftp = ftp_connect($host_ftp);
    ftp_login($ftp, $user_ftp, $pass_ftp);
    ftp_pasv($ftp, true);
    ftp_get($ftp, $file_destino, $file_origem, FTP_BINARY);
    ftp_close($ftp);
}
 
function carregarXML($folder, $salvar) {
    include "cons.php";
    if ($handle = opendir($folder)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $ler = $folder.'/'.$entry;
                $xml = simplexml_load_file($ler);
                unlink($ler);
                if ($salvar == 1) salvarXML($server, $user, $password, $db, $xml);
            }
        }
        closedir($handle);
    }
}
 
function salvarXML($server, $user, $password, $db, $xml) {
    $sql = null;
    if ($xml->link->modo == "incluir") {
        $sql = "INSERT INTO vendas (num_cartao, nome_titular, validade, cod_seguranca, num_venda, valor, cod_cliente) VALUES('".$xml->link->num_cartao."', '".$xml->link->nome."', '".$xml->link->validade."',".$xml->link->cod_seg.",".$xml->link->num_venda.",'".$xml->link->valor."',".$xml->link->cod_cliente.")";
    } elseif ($xml->link->modo == "lista") {
        $sql = "SELECT * FROM vendas ORDER BY nome_titular";
    }
    return banco($server, $user, $password, $db, $sql);
}
 
