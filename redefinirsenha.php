<?php
include("config.php");

$mensagem = "";
$achou=false;

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

$token='';
if (isset($_GET['token'])){
    $token = $_GET['token'];
    $sqlins="SELECT * FROM usuario WHERE token='".$token."'"; 
    $camposins = $mysqli->query($sqlins);
    while($objins = $camposins->fetch_object())
        $achou=true;
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VagaLivre - Redefinir Senha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1a1a2e 100%);
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
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 26px;
            font-weight: 800;
            color: #1A2B4D;
            margin-bottom: 5px;
        }

        .logo i { color: #1A2B4D; }
        .logo span { color: #2DAB61; }

        h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 20px;
            border: none;
            background-color: #f1f3f5;
            border-radius: 12px;
            font-size: 14px;
            color: #495057;
            outline: none;
        }

        .btn-entrar {
            width: 60%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 25px;
            background: linear-gradient(to right, #2c3e50, #000000);
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-entrar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        /* Botão para voltar se desistir de mudar a senha */
        .btn-voltar {
            display: block;
            margin-top: 20px;
            color: #777;
            text-decoration: none;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">
            <i class="fa-solid fa-car-side"></i> Vaga<span>Livre</span>
        </div>
        
        <h2>Redefinir senha</h2>

        <?php if($mensagem != ""): ?>
            <div class="error-msg"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form name="formRecuperar" id="formRecuperar">
            <?php if ($achou){ ?>
            <div class="form-group">
                <input type="password" id="nova_senha" placeholder="Nova senha" required>
            </div>

            <div class="form-group">
                <input type="password" id="confirmar_senha" placeholder="Confirmar nova senha" required>
            </div>
            <input type="button" onclick="salvar();" class="btn-entrar" value="Salvar nova senha"></input>
            <?php }
            else { ?>
            <div class="form-group">
                <input type="email" id="email" placeholder="Seu e-mail cadastrado" required>
            </div>
            <input type="button" onclick="enviar();" class="btn-entrar" value="Enviar link para redefinir"></input>
            <?php } ?>
        </form>

        <a href="login.php" id="btnEnviar" class="btn-voltar">Voltar para o login</a>
    </div>


</body>
</html>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
function salvar(){
    const nova_senha = document.getElementById('nova_senha').value;
    const confirmar_senha = document.getElementById('confirmar_senha').value;
    const token = '<?php echo $token ?>';
    const btn = document.getElementById('btnEnviar');
    

    // Bloqueia o botão e mostra o carregamento
    btn.disabled = true;
    Swal.fire({
        title: 'Salvando...',
        text: 'Por favor, aguarde um momento.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const dados = new FormData();
    dados.append('nova_senha', nova_senha);
    dados.append('confirmar_senha', confirmar_senha);
    dados.append('token', token);

    fetch('salvar_senha.php', {
        method: 'POST',
        body: dados
    })
    .then(response => response.text())
    .then(data => {
        btn.disabled = false;
        if (data=='1'){
            // Exibe o alerta de sucesso
            Swal.fire({
                title: 'Sucesso!',
                text: 'Senha alterada com sucesso!',
                icon: 'success',
                confirmButtonText: 'Continuar',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ops...',
                text: 'Senhas não coincidem.'
            });
        }
    })
    .catch(error => {
        btn.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Ops...',
            text: 'Senhas não coincidem.'
        });
    });
}



function enviar(){
    const email = document.getElementById('email').value;
    const btn = document.getElementById('btnEnviar');

    // Bloqueia o botão e mostra o carregamento
    btn.disabled = true;
    Swal.fire({
        title: 'Enviando...',
        text: 'Por favor, aguarde um momento.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const dados = new FormData();
    dados.append('email', email);

    fetch('enviar_token.php', {
        method: 'POST',
        body: dados
    })
    .then(response => response.text())
    .then(data => {
        btn.disabled = false;
        
        // Exibe o alerta de sucesso
        Swal.fire({
            icon: 'success',
            title: 'E-mail Enviado!',
            html: `O link de recuperação foi enviado para <b>${email}</b>.<br><br>
                   <p style="color: #d33;"><b>Importante:</b> Se não encontrar na caixa de entrada, verifique sua pasta de <b>SPAM</b> ou Lixo Eletrônico.</p>`,
            confirmButtonText: 'Entendi',
            confirmButtonColor: '#3085d6'
        });
    })
    .catch(error => {
        btn.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Ops...',
            text: 'Ocorreu um erro ao processar sua solicitação.'
        });
    });
}


</script>