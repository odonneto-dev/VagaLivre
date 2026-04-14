<?php header("Content-Type: text/html; charset=UTF-8",true);?><?php
include("config.php");
$errado = false;
$info = false;
$criarSenha = false;
$login = "";
$senha = "";
ob_start();
@session_start();

if (!isset($_GET['cd_st']))
    if (isset($_SESSION["login"]) && isset($_SESSION["senha"]))
    {
        header("Location:./home.php");
        exit();
    }


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


ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

if (isset($_GET['ns']))
{
    $novasenha1=$_POST['senha1'];
    $novasenha2=$_POST['senha2'];
    $idu=$_GET['idu'];
    if ($novasenha1==$novasenha2)
    {
        $query = "update `usuario` SET
        `senha` = '".sha1(md5($novasenha1))."'
            
        WHERE (`id_usuario` = ".$idu.")";
        
        // Executa a query
        $atualiza2 = $mysqli->query($query);
    }
    $info = true;
}


if (!empty($_POST['email']))
{
    $sql="SELECT * FROM usuario WHERE email='".$login."' and senha='".$senha."'";

    $campos = $mysqli->query($sql);
    while($obj = $campos->fetch_object())
    {
        $achou = true;
        $cod = $obj->id_usuario;
        $idf = $obj->id_usuario;
        $nome = mb_strtoupper($obj->nome);
        $senha_atual = $obj->senha;

        if ($senha_atual=='ec3d95d4e9d53a99e06bf6914bdd019c36fe4bb3')
            $criarSenha = true;
        else
            $criarSenha = false;

        if ($senha != $senha_atual)
            $erro = true;
        else
            $senhaConfere = true;

        $contcursos++;
        $aux++;
    }

    if (($achou)&&(!$criarSenha))
    {
        if ($senhaConfere)
        {
            @session_destroy();
           
            $autorizado = true;
            $lifetime_in_seconds = 60; // 3 horas
            ini_set('session.gc_maxlifetime', 60); 
            // session_set_cookie_params($lifetime_in_seconds);
            // setcookie(session_name(), session_id(), time() + $lifetime_in_seconds);

            @session_start();
            $_SESSION['start_time'] = time();
            $_SESSION['expiry_time'] = time()+$lifetime_in_seconds;

            @$_SESSION['login'] = $login;
            @$_SESSION['senha'] = $senha;
            @$_SESSION['id'] = $idf;
            @$_SESSION['idcliente_login'] = $idcliente_login;
            header("Location:./home.php");

            $info = true;
        }
    }
    else
        $erro = true;
    
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>VagaLivre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;800&display=swap');

        * { box-sizing: border-box; }

        :root {
            --primary-dark: #2b5876;
            --primary-light: #4e4376;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --user-blue: #3498db;
            --white: #FFFFFF;
            --gray-bg: #f0f2f5;
            --map-road: #ffffff;
            --map-block: #e3e8ee;
            --map-bg: #ced6e0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0; padding: 0;
            height: 100%; width: 100%;
            position: fixed; overflow: hidden;
            background-color: var(--gray-bg);
            display: flex; flex-direction: column;

            touch-action: manipulation; /* Impede zoom por duplo toque */
            overscroll-behavior: none;  /* Impede o efeito elástico */
            -webkit-user-select: none;  /* Impede selecionar texto */
            user-select: none;
            

            font-family: 'Montserrat', sans-serif;
            margin: 0; padding: 0;
            height: 100%; width: 100%;
            position: fixed; overflow: hidden;
            background-color: var(--gray-bg);
            display: flex; flex-direction: column;



            font-family: 'Montserrat', sans-serif;
            touch-action: pan-x pan-y; /* Deixa rolar listas, mas bloqueia zoom */
            -webkit-text-size-adjust: 100%; /* Evita aumento de fonte automático */
            overscroll-behavior: none; /* Remove efeito elástico */

            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
            overflow: hidden; /* Evitamos assim o  scroll indesejado na animação */
        }


        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
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
            top: 0; right: -2px; width: 6px; height: 6px;
            background-color: var(--accent-green);
            border-radius: 50%; border: 2px solid var(--white);
        }
        .logo-text {
            font-size: 22px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px;
        }
        .logo-text span { color: var(--accent-green); font-weight: 600; }


        .overlay .logo-icon { color: var(--white); }
        .overlay .logo-icon::after { border-color: var(--primary-light); }
        .overlay .logo-text { color: var(--white); }


        .container {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 800px;
            max-width: 100%;
            min-height: 500px; /* Aqui fica a altura quando for desktop */
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }


        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }
        
        h1 { margin: 0; font-size: 24px; }
        p { font-size: 14px; font-weight: 100; line-height: 20px; letter-spacing: 0.5px; margin: 20px 0 30px; }
        span.sub-text { font-size: 12px; margin-bottom: 10px; }
        
        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            border-radius: 8px;
        }
        .plate-input {
            text-transform: uppercase;
            font-family: monospace;
            letter-spacing: 2px;
            font-weight: bold;
            border-left: 5px solid var(--accent-green);
        }

        button {
            border-radius: 20px;
            border: 1px solid var(--primary-light);
            background-color: var(--primary-light);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
            margin-top: 10px;
        }
        button:active { transform: scale(0.95); }
        button:focus { outline: none; }
        button.ghost { background-color: transparent; border-color: #FFFFFF; }


        .sign-in-container {
            left: 0; width: 50%; z-index: 2;
        }
        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }
        .sign-up-container {
            left: 0; width: 50%; opacity: 0; z-index: 1;
        }
        .container.right-panel-active .sign-up-container {
            transform: translateX(100%); opacity: 1; z-index: 5; animation: show 0.6s;
        }
        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }


        .overlay-container {
            position: absolute; top: 0; left: 50%; width: 50%; height: 100%;
            overflow: hidden; transition: transform 0.6s ease-in-out; z-index: 100;
        }
        .container.right-panel-active .overlay-container { transform: translateX(-100%); }
        
        .overlay {
            background: linear-gradient(to right, var(--primary-dark), var(--primary-light));
            background-repeat: no-repeat; background-size: cover; background-position: 0 0;
            color: #FFFFFF; position: relative; left: -100%; height: 100%; width: 200%;
            transform: translateX(0); transition: transform 0.6s ease-in-out;
        }
        .container.right-panel-active .overlay { transform: translateX(50%); }

        .overlay-panel {
            position: absolute; display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 40px; text-align: center; top: 0; height: 100%; width: 50%;
            transform: translateX(0); transition: transform 0.6s ease-in-out;
        }
        .overlay-left { transform: translateX(-20%); }
        .container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .container.right-panel-active .overlay-right { transform: translateX(20%); }


        /* Parte do mobile */
        @media (max-width: 768px) {
            
            .container {
                width: 90vw; /* Largura boa no mobile */
                height: 85vh; /* Altura quase que total */
                min-height: auto;
                max-width: 400px; /* Limite para tablets */
                border-radius: 15px;
            }

            form { padding: 0 30px; }
            h1 { font-size: 20px; }

            .sign-in-container, .sign-up-container {
                width: 100%;
                height: 70%;
                top: 0;
                left: 0;
            }


            .container.right-panel-active .sign-in-container { transform: translateY(100%); }
            .container.right-panel-active .sign-up-container { transform: translateY(0); }
            

            .overlay-container {
                width: 100%;
                height: 30%;
                top: 70%;
                left: 0;
                right: 0;
            }
            
            /* Quando ta ativo, o overlay corre e sobe para o TOPO */
            .container.right-panel-active .overlay-container {
                transform: translateY(-233%);
                transform: translateY(-233%); 
            }
            
            .container.right-panel-active .overlay-container {
                transform: translateY(-233%); 
            }

            .overlay {
                width: 100%;
                height: 200%;
                left: 0;
                top: -100%; 
                flex-direction: column;
                transform: translateY(0);
            }
            
            .container.right-panel-active .overlay {
                transform: translateY(50%);
            }

            /* Painéis de texto dentro do Overlay */
            .overlay-panel {
                width: 100%;
                height: 50%; /* Ajusta para cada painel ocupar metade da altura do overlay */
                padding: 0 20px;
            }

            .overlay-left { 
                top: 0; 
                transform: translateY(-20%); 
            }
            .overlay-right { 
                top: auto; 
                bottom: 0; 
                right: auto;
                transform: translateY(0); 
            }

            /* Animações de texto quando for mobile */
            .container.right-panel-active .overlay-left { transform: translateY(0); }
            .container.right-panel-active .overlay-right { transform: translateY(20%); }

            /* Ajuste de correção da posição dos forms na animação */
            .sign-up-container {
                top: auto;
                bottom: 0; /* Começa na parte de baixo quando for mobile */
                transform: translateY(0);
            }
            
            /* Estado inicial do login com form em cima e overlay em baixo */
            .sign-in-container { top: 0; height: 70%; }
            .sign-up-container { top: 30%; height: 70%; opacity: 0; z-index: 0;}
            
            /* Estado ativo de cadastro  com overlay em cima e form em baixo */
            .container.right-panel-active .sign-in-container {
                transform: translateY(100%);
                opacity: 0;
            }
            .container.right-panel-active .sign-up-container {
                transform: translateY(0); 
                top: 30%; 
                opacity: 1;
                z-index: 5;
                animation: none;
            }


            /* Quando for login o overlay está em top: 70% */
            /* Qjando for cadastro o overlay deve ir para top: 0% */
            
            .container.right-panel-active .overlay-container {
                transform: translateY(-233%); 
            }
        }

        /* Ajuste para telas muito pequenas */
        @media (max-height: 600px) and (max-width: 768px) {
            .container { height: 95vh; }
            h1 { font-size: 18px; margin-bottom: 5px;}
            p { margin: 10px 0; font-size: 12px; }
            .logo-container { margin-bottom: 5px; }
            input { padding: 8px 15px; margin: 4px 0; }
        }



        #map-container {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-color: var(--map-bg); z-index: 1; overflow: hidden;
            touch-action: none; /* Bloqueia TUDO no mapa inclusive pinça */
        }
        .custom-map-svg { width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; }
        .map-label { position: absolute; font-size: 8px; font-weight: 700; color: #8fa6b9; text-transform: uppercase; letter-spacing: 0.5px; pointer-events: none; text-align: center; }


        .pin {
            position: absolute; width: 28px; height: 28px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            cursor: pointer; transition: transform 0.2s; z-index: 10;
        }
        .pin i { transform: rotate(45deg); color: white; font-size: 12px; }
        .pin.free { background-color: var(--accent-green); border: 2px solid #fff; }
        .pin.occupied { background-color: var(--accent-red); border: 2px solid #fff; opacity: 0.8; }
        .pin.user {
            background-color: var(--user-blue); border: 2px solid #fff;
            width: 18px; height: 18px; border-radius: 50%; transform: none;
            box-shadow: 0 0 0 8px rgba(52, 152, 219, 0.2);
            animation: pulse 2s infinite; z-index: 5; pointer-events: none;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
            100% { box-shadow: 0 0 0 12px rgba(52, 152, 219, 0); }
        }


        .sidebar {
            position: absolute; bottom: 0; left: 0; width: 100%;
            background: white; z-index: 100;
            border-radius: 25px 25px 0 0;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.15);
            display: flex; flex-direction: column;
            transition: height 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            will-change: height;
        }
        .sidebar.collapsed { height: 130px; }
        .sidebar.half-expanded { height: 45%; }
        .sidebar.full-expanded { height: 70%; }

        .sheet-header { padding: 0 20px; flex-shrink: 0; background: white; border-radius: 25px 25px 0 0; }
        .drag-handle-container { width: 100%; display: flex; justify-content: center; padding: 12px 0; cursor: grab; touch-action: none; }
        .drag-handle { width: 40px; height: 5px; background-color: #ddd; border-radius: 5px; }

        .search-wrapper { transition: all 0.3s ease; max-height: 80px; opacity: 1; margin-bottom: 15px; overflow: hidden; }
        .sidebar.full-expanded .search-wrapper { max-height: 0; opacity: 0; margin-bottom: 0; pointer-events: none; }

        .search-container { position: relative; }
        .search-container input {
            width: 100%; padding: 14px 15px 14px 45px;
            border-radius: 12px; border: 1px solid #eee; background-color: #f9f9f9;
            font-family: 'Montserrat', sans-serif; font-size: 16px;
        }
        .search-container input:focus { outline: none; background-color: #fff; border-color: var(--primary-light); }
        .search-container i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }

        .panel-content { flex-grow: 1; overflow-y: auto; padding: 0 20px 40px 20px; opacity: 0; transition: opacity 0.2s ease; pointer-events: none; }
        .sidebar.half-expanded .panel-content, .sidebar.full-expanded .panel-content { opacity: 1; pointer-events: auto; }

        #list-view { display: block; }
        .quick-filters { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 5px; }
        .quick-filters::-webkit-scrollbar { display: none; }
        .filter-chip { padding: 8px 16px; background-color: #f0f2f5; border-radius: 20px; font-size: 12px; font-weight: 600; color: #555; white-space: nowrap; cursor: pointer; }
        .filter-chip.active { background-color: var(--primary-dark); color: white; }
        .suggestion-item { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #f5f5f5; cursor: pointer; }
        .s-icon { width: 36px; height: 36px; background-color: #edf2f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: var(--primary-dark); }

        #details-view { display: none; padding-top: 10px; }
        .spot-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .spot-tag { background: #e8f5e9; color: var(--accent-green); padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold; }


        .camera-feed-container {
            width: 100%; height: 200px; 
            background: #000; 
            border-radius: 12px;
            margin-bottom: 20px; 
            position: relative; 
            overflow: hidden; /* Garante que nada saia da borda redonda */
        }
        
        /* Modal do "Ao Vivo" fica sobreposto */
        .camera-overlay {
            position: absolute; top: 10px; left: 10px; z-index: 20;
            background: rgba(231, 76, 60, 0.9); color: white; 
            font-size: 10px; padding: 4px 8px; border-radius: 4px; font-weight: 700;
            display: flex; align-items: center; gap: 5px;
        }
        .camera-overlay i { font-size: 8px; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        /* Estilo das imagens do slide */
        .camera-slide {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            opacity: 0; /* Iniciando invisível */
            transition: opacity 0.8s ease-in-out; /* Transição suave */
        }
        
        /* Parete da classe que torna a imagem visível */
        .camera-slide.active { opacity: 1; }

        .btn-action { width: 100%; padding: 16px; border: none; background-color: var(--primary-light); color: white; font-weight: bold; border-radius: 12px; cursor: pointer; font-size: 14px; box-shadow: 0 4px 15px rgba(78, 67, 118, 0.3); }
        .btn-cancel { width: 100%; padding: 15px; background: transparent; border: none; color: #999; cursor: pointer; font-size: 13px; }
        .menu-btn { position: absolute; top: 20px; right: 20px; width: 45px; height: 45px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer; z-index: 200; }
    </style>
</head>
<body>

<div class="container" id="container">
    
    <div class="form-container sign-up-container">
        <form action="cadastrar.php" method="POST">
            <div class="logo-container">
                <i class="fas fa-car-side logo-icon"></i>
                <div class="logo-text">Vaga<span>Livre</span></div>
            </div>
            <h1>Criar Conta</h1>
            <input type="text" name="nome" placeholder="Nome" />
            <input type="text" name="whatsapp" placeholder="Telefone" />
            <input type="email" name="email" placeholder="Email" />
            <input type="password" name="senha" placeholder="Senha" />
            <input type="password" name="confsenha" placeholder="Confirmação de Senha" />
            <button>Cadastrar</button>
        </form>
    </div>

    <div class="form-container sign-in-container">
         <form action="login.php" method="POST">
            <div class="logo-container">
                <i class="fas fa-car-side logo-icon"></i>
                <div class="logo-text">Vaga<span>Livre</span></div>
            </div>
            <h1>Login</h1>
            <input type="email" name="email" placeholder="Email" />
            <input type="password" name="senha" placeholder="Senha" />
            <a href="./redefinirsenha.php" style="color: #666; font-size: 13px; text-decoration: none; margin: 15px 0; display: block;">
                Esqueceu a senha?
            </a>
            <button>Entrar</button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Já tem conta?</h1>
                <p>Faça login para ver suas vagas.</p>
                <button class="ghost" id="signIn">Entrar</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Novo aqui?</h1>
                <p>Cadastre-se e estacione fácil.</p>
                <button class="ghost" id="signUp">Cadastrar</button>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });

<?php if (@$_GET['cd_st']==1){ ?>
    Swal.fire({
            title: 'Sucesso!',
            text: 'Cadastro realizado com sucesso!',
            icon: 'success',
            confirmButtonText: 'Continuar',
            confirmButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'home.php';
            }
        });
<?php } ?>

<?php if (@$_GET['cd_st']==2){ ?>
    Swal.fire({
        title: "Email em uso",
        text: "Esse email já está em uso!",
        icon: "error"
    });
<?php } ?>

<?php if (@$_GET['cd_st']==3){ ?>
    Swal.fire({
        title: "Senha muito fraca",
        text: "A senha precisa ter pelo menos 1 simbolo, 1 letra maiuscula, 1 letra minúscula, 1 número e 8 caracteres !",
        icon: "error"
    });
<?php } ?>

<?php if (@$_GET['cd_st']==4){ ?>
    Swal.fire({
        title: "Senhas não conferem",
        text: "As senhas não coincidem, tente novamente!",
        icon: "error"
    });
<?php } ?>

</script>

</body>
</html>