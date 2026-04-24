<?php
include("config.php");

$mensagem = "";
$achou = false;

$token = '';
if (isset($_GET['token'])) {
    $token = $mysqli->real_escape_string($_GET['token']);
    $sqlins = "SELECT * FROM usuario WHERE token='" . $token . "'";
    $camposins = $mysqli->query($sqlins);
    if ($camposins && $camposins->num_rows > 0) {
        $achou = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VagaLivre - Redefinir Senha</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --white: #FFFFFF;
            --gray-bg: #f0f2f5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white); 
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background: var(--white);
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            border: 1px solid #eee;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .logo-icon {
            font-size: 24px;
            color: var(--primary-dark);
            margin-right: 8px;
            position: relative;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            top: 0; 
            right: -2px; 
            width: 6px; 
            height: 6px;
            background-color: var(--accent-green);
            border-radius: 50%; 
            border: 2px solid var(--white);
        }

        .logo-text {
            font-size: 22px; 
            font-weight: 800; 
            color: var(--primary-dark); 
            letter-spacing: -1px;
        }

        .logo-text span { 
            color: var(--accent-green); 
            font-weight: 600; 
        }

        h2 {
            font-size: 18px;
            color: #000000;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border-radius: 30px; 
            border: 1px solid #eee;
            background-color: #f9f9f9;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: var(--accent-green);
            background-color: #fff;
        }

        .btn-principal {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            border: none;
            border-radius: 30px;
            background-color: var(--primary-dark);
            color: white;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-principal:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-voltar-container {
            margin-top: 25px;
        }

        .btn-voltar {
            color: var(--accent-green);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo-container">
            <div class="logo-icon">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div class="logo-text">Vaga<span>Livre</span></div>
        </div>
        
        <h2>Redefinir senha</h2>

        <form id="formRecuperar">
            <?php if ($achou): ?>
                <div class="form-group">
                    <input type="password" id="nova_senha" placeholder="Nova senha" required>
                </div>
                <div class="form-group">
                    <input type="password" id="confirmar_senha" placeholder="Confirmar nova senha" required>
                </div>
                <button type="button" id="btnSalvar" onclick="salvar();" class="btn-principal">
                    Salvar Nova Senha
                </button>
            <?php else: ?>
                <div class="form-group">
                    <input type="email" id="email" placeholder="E-mail cadastrado" required>
                </div>
                <button type="button" id="btnEnviar" onclick="enviar();" class="btn-principal">
                    Enviar Link
                </button>
            <?php endif; ?>
        </form>

        <div class="btn-voltar-container">
            <a href="login.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar para login
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
    function salvar(){
        const nova_senha = document.getElementById('nova_senha').value;
        const confirmar_senha = document.getElementById('confirmar_senha').value;
        const token = '<?php echo $token ?>';
        const btn = document.getElementById('btnSalvar');

        if(!nova_senha || !confirmar_senha) {
            Swal.fire('Atenção', 'Preencha todos os campos.', 'warning');
            return;
        }

        btn.disabled = true;
        const dados = new FormData();
        dados.append('nova_senha', nova_senha);
        dados.append('confirmar_senha', confirmar_senha);
        dados.append('token', token);

        fetch('salvar_senha.php', { method: 'POST', body: dados })
        .then(r => r.text())
        .then(data => {
            btn.disabled = false;
            if (data.trim() == '1'){
                Swal.fire({ title: 'Sucesso!', text: 'Sua senha foi alterada.', icon: 'success', confirmButtonColor: '#2ecc71' })
                .then(() => { window.location.href = 'login.php'; });
            } else {
                Swal.fire('Erro', 'As senhas não coincidem ou link expirou.', 'error');
            }
        });
    }

    function enviar(){
        const email = document.getElementById('email').value;
        const btn = document.getElementById('btnEnviar');
        if(!email) return Swal.fire('Aviso', 'Digite seu e-mail cadastrado.', 'info');

        btn.disabled = true;
        const dados = new FormData();
        dados.append('email', email);

        fetch('enviar_token.php', { method: 'POST', body: dados })
        .then(() => {
            btn.disabled = false;
            Swal.fire({ icon: 'success', title: 'Enviado!', text: 'Se o e-mail existir, você receberá o link.', confirmButtonColor: '#2b5876' });
        });
    }
    </script>
</body>
</html>