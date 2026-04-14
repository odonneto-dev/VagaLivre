<?php
include("config.php");

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $mysqli->real_escape_string($_POST['email']);
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem!";
    } else {
        // Verifica se o e-mail existe
        $sql_busca = "SELECT id_usuario FROM usuario WHERE email = '$email'";
        $res = $mysqli->query($sql_busca);

        if ($res->num_rows > 0) {
            // APLICANDO A CRIPTOGRAFIA PADRÃO DO SEU SISTEMA
            $senha_cripto = sha1(md5(trim($nova_senha)));
            
            $sqlUpdate = "UPDATE usuario SET senha = '$senha_cripto' WHERE email = '$email'";
            
            if ($mysqli->query($sqlUpdate)) {
                // REDIRECIONA PARA O LOGIN COM AVISO DE SUCESSO
                header("Location: login.php?sucesso=senha_alterada");
                exit();
            }
        } else {
            $mensagem = "E-mail não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VagaLivre - Redefinir Senha</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --white: #FFFFFF;
            --gray-bg: #f8f9fa;
            --text-dark: #2b5876;
        }

        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--gray-bg);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: white;
            width: 90%;
            max-width: 400px;
            padding: 40px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-bottom: 5px solid var(--accent-green);
        }

        /* LOGO PADRONIZADA COM PINGO VERDE */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .logo-icon {
            font-size: 26px;
            color: var(--primary-dark);
            margin-right: 8px;
            position: relative;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            top: 0; right: -2px; width: 6px; height: 6px;
            background-color: var(--accent-green);
            border-radius: 50%; border: 2px solid var(--white);
        }

        .logo-text {
            font-size: 24px; 
            font-weight: 800; 
            color: var(--primary-dark); 
            letter-spacing: -1px;
        }

        .logo-text span { 
            color: var(--accent-green); 
            font-weight: 600; 
        }

        h2 {
            font-size: 16px;
            color: #888;
            margin-bottom: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: none;
            background-color: #f0f2f5;
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            background-color: #fff;
            box-shadow: 0 0 0 2px var(--accent-green);
        }

        /* BOTÃO SEM DEGRADÊ - VERDE SÓLIDO */
        .btn-redefinir {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            border: none;
            border-radius: 30px;
            background-color: var(--accent-green);
            color: white;
            font-weight: 700;
            font-size: 14px;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-redefinir:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
            opacity: 0.9;
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-voltar {
            display: inline-block;
            margin-top: 25px;
            color: var(--accent-green);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-voltar:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo-container">
            <i class="fas fa-car-side logo-icon"></i>
            <div class="logo-text">Vaga<span>Livre</span></div>
        </div>
        
        <h2>Redefinir senha</h2>

        <?php if($mensagem != ""): ?>
            <div class="error-msg"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="E-mail cadastrado" required>
            </div>

            <div class="form-group">
                <input type="password" name="nova_senha" placeholder="Nova senha" required>
            </div>

            <div class="form-group">
                <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" required>
            </div>

            <button type="submit" class="btn-redefinir">Redefinir Agora</button>
        </form>

        <a href="login.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar para o login
        </a>
    </div>

</body>
</html>