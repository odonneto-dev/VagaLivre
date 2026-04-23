<?php
include("conexao.php");
// Conexão com banco e verificação do e-mail omitidas por brevidade
$nova_senha = trim($_POST['nova_senha']);
$confirmar_senha = trim($_POST['confirmar_senha']);
$token = trim($_POST['token']);

ob_start();
$mysqli = new mysqli("localhost",$username,$password,$database);

// Check connection
if ($mysqli -> connect_errno) {
  // echo "Erro na conexao com BD: " . $mysqli -> connect_error;
  echo "Erro na conexao com BD.";
//   exit();
}


if ($nova_senha!=$confirmar_senha)die('0');

$senhaok=sha1(md5($nova_senha));

$sql="UPDATE `usuario` SET senha='".$senhaok."',token='' WHERE `token`='".$token."' LIMIT 1";
$mysqli->query($sql);

echo '1';
?>