<?php
include("conexao.php");
// Conexão com banco e verificação do e-mail omitidas por brevidade
$email_usuario = trim($_POST['email']);
#$email_usuario = "odonegeisa@gmail.com";

function getRandomStringShuffle($length = 64)
{
    $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $stringLength = strlen($stringSpace);
    $string = str_repeat($stringSpace, ceil($length / $stringLength));
    $shuffledString = str_shuffle($string);
    $randomString = substr($shuffledString, 1, $length);
    return $randomString;
}

$token = getRandomStringShuffle();

$result=false;
$ttl=0;


ob_start();
$mysqli = new mysqli("localhost",$username,$password,$database);

// Check connection
if ($mysqli -> connect_errno) {
  // echo "Erro na conexao com BD: " . $mysqli -> connect_error;
  echo "Erro na conexao com BD.";
//   exit();
}

$sql="UPDATE `usuario` SET `token`='".$token."' where email='".$email_usuario."' LIMIT 1";
$mysqli->query($sql);


$link = "https://imobiliariamonteiro.com/p5unifip/redefinirsenha.php?token=" . $token;
$mensagem = "Clique no link para redefinir sua senha: " . $link;
 
mail($email_usuario, "Recuperação de Senha no VagaLivre", $mensagem);


?>