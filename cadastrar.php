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


function validaTelefone($telefone){
    if (preg_match('/^[0-9]{8,11}$/', $telefone))
        return true;
    else
        return false;
}

function validaEmail($email){
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
        return true;
    else
        return false;

}

function validarSenha($senha, $exigirMinuscula = false, $exigirMaiuscula = false, $exigirNumero = false, $exigirSimbolo = false, $minimoCaracteres = 8) {
    $regras = "";

    // Adiciona "Lookaheads" apenas se o parâmetro for true
    if ($exigirMinuscula) {
        $regras .= "(?=.*[a-z])";
    }
    if ($exigirMaiuscula) {
        $regras .= "(?=.*[A-Z])";
    }
    if ($exigirNumero) {
        $regras .= "(?=.*[0-9])";
    }
    if ($exigirSimbolo) {
        $regras .= "(?=.*[!@#$%^&*(),.?\":{}|<>])";
    }

    // Monta a regex: sem a minúscula fixa, apenas o tamanho mínimo e as regras opcionais
    $padrao = "/^" . $regras . ".{" . $minimoCaracteres . ",}$/";

    return preg_match($padrao, $senha);
}



if (!empty($_POST['email'])) {
    $achou = false;

    $sql = "SELECT * FROM usuario WHERE email='" . $mysqli->real_escape_string($_POST['email']) . "'";
    $campos = $mysqli->query($sql);
    
    if ($campos && $campos->num_rows > 0)
        $achou = true;

    
    if (!$achou) {
        $senha_pura = $_POST['senha'];
        $confsenha_pura = $_POST['confsenha'];
    
        // Validação da força da senha
        if (!validarSenha($senha_pura, false, false, false, false, 8))
            $status = 3; // Senha fraca
    
        // Validação de igualdade
        elseif ($senha_pura !== $confsenha_pura)
            $status = 4; // Senhas não conferem (crie esse status se não houver)
        
        else {
            // Se passou nas validações, prossegue com o cadastro
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = sha1(md5($senha_pura));
            $telefone = $_POST['telefone'];

            // Validação do email
            if (!validaEmail($email))
                $status = 5; // Email invalido

            // Validação do telefone
            if (!validaTelefone($telefone))
                $status = 6; // Telefone invalido


            if ($status==0)
            {
                $sql = "INSERT INTO usuario (nome, email, senha, telefone) VALUES ('$nome', '$email', '$senha', '$telefone')";
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
        }
    } else
        $status = 2; // Email já cadastrado

}


echo json_encode($status);

?>