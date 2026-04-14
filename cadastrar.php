<?php header("Content-Type: text/html; charset=UTF-8",true);?><?php
include("config.php");
$errado = false;
$info = false;
$criarSenha = false;
$login = "";
$senha = "";
ob_start();
@session_start();
session_destroy();


if (!empty($_POST['email']))
    $login=stripslashes(trim($_POST['email']));
if (empty($_POST['senha']))
    $senha = "vazio";
else
    $senha=sha1(md5(trim($_POST['senha'])));

$aux = 0;
$admin = false;
$aluno = false;
$contcursos = 0;
$achou = false;
$senha_atual = "vazio";
$erro = false;
$senhaConfere = false;
$manutencao = false;
$periodo = false;
$status = 0;


ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);



function validarSenha_old($senha) {
    // Regras:
    // (?=.*[a-z]) : Pelo menos uma letra minúscula
    // (?=.*[A-Z]) : Pelo menos uma letra maiúscula
    // (?=.*[0-9]) : Pelo menos um número
    // (?=.*[!@#$%^&*(),.?":{}|<>]) : Pelo menos um caractere especial
    // .{8,}       : No mínimo 8 caracteres no total
    
    $padrao = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/';

    return preg_match($padrao, $senha);
}


function validarSenha($senha, $exigirMaiuscula = true, $exigirNumero = true, $exigirSimbolo = true, $minimoCaracteres = 8) {
    // A base da regex: garante o tamanho mínimo
    $regras = "";

    // Adiciona "Lookaheads" condicionais conforme os parâmetros
    if ($exigirMaiuscula) {
        $regras .= "(?=.*[A-Z])";
    }
    if ($exigirNumero) {
        $regras .= "(?=.*[0-9])";
    }
    if ($exigirSimbolo) {
        $regras .= "(?=.*[!@#$%^&*(),.?\":{}|<>])";
    }

    // Monta a regex final (sempre exigindo ao menos uma minúscula por padrão)
    $padrao = "/^(?=.*[a-z])" . $regras . ".{" . $minimoCaracteres . ",}$/";

    return preg_match($padrao, $senha);
}



if (!empty($_POST['email'])) {
    $achou = false;

    $sql = "SELECT * FROM usuario WHERE email='" . $mysqli->real_escape_string($_POST['email']) . "'";
    $campos = $mysqli->query($sql);
    
    if ($campos && $campos->num_rows > 0)
        $achou = true;

    $achou = false;

    if (!$achou) {
        $senha_pura = $_POST['senha'];
        $confsenha_pura = $_POST['confsenha'];
        
        // Validação da força da senha
        if (!validarSenha($senha_pura, false, false, false, 8))
            $status = 3; // Senha fraca
    
        // Validação de igualdade
        elseif ($senha_pura !== $confsenha_pura)
            $status = 4; // Senhas não conferem (crie esse status se não houver)
        
        else {
            // Se passou nas validações, prossegue com o cadastro
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = sha1(md5($senha_pura));
            $whatsapp = $_POST['whatsapp'];

            $sql = "INSERT INTO usuario (nome, email, senha, telefone) VALUES ('$nome', '$email', '$senha', '$whatsapp')";
            $result = $mysqli->query($sql);

            if ($result === TRUE) {
                $status = 1;
                $last_id = $mysqli->insert_id;

                // Início da Sessão
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                $lifetime_in_seconds = 60; // 3 horas (ajustado de 10s para algo útil)
                $_SESSION['start_time'] = time();
                $_SESSION['expiry_time'] = time() + $lifetime_in_seconds;
                $_SESSION['login'] = $email;
                $_SESSION['senha'] = $senha;
                $_SESSION['id'] = $last_id;
                $_SESSION['idcliente_login'] = $last_id;
                
                $autorizado = true;
            } else
                echo "Erro no banco: " . $mysqli->error;
            
        }
    } else
        $status = 2; // Email já cadastrado

}


header('Location: ./login.php?cd_st='.$status);

?>