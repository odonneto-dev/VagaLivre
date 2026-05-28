<?php
include("config.php");
include("restrito.php");
ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);

@$id_ref=$_GET['id'];
$dados=[];
$sql2="select * from monitoramento m INNER JOIN camera c ON c.id_camera=m.id_camera INNER JOIN area a ON a.id_area=m.id_area WHERE m.id_monitoramento=".$id_ref;
$campos2 = $mysqli->query($sql2);
while($obj2 = $campos2->fetch_object()){
    $avenida_nome=$obj2->nome_area.' - '.$obj2->localizacao;
    $rua=$obj2->localizacao;
}
$ultima_atualizacao = date("H:i:s");

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
            --user-blue: #3498db;
            --white: #FFFFFF;
            --map-road: #ffffff;
            --map-block: #e3e8ee;
            --map-bg: #ced6e0;
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --white: #FFFFFF;
            --gray-bg: #f8fafc; /* Fundo mais suave */
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
            max-width: 430px;
            position: absolute;
            left: 50%;
            margin-left: -195px;
        }


        /* Estilo Suave para Referências e Nomes */
        .map-label { fill: #64748b; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .street-name { fill: #94a3b8; font-size: 12px; font-weight: 800; letter-spacing: 0.5px; }
        .refbox { fill: #e2e8f0; rx: 6; ry: 6; } 

        .logo-container {
            display: contents;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            position: fixed;
            left: 53px;
            top: 16px;
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
        p { font-size: 14px; font-weight: 100; line-height: 20px; letter-spacing: 0.5px; margin: 15px 0 20px; }
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
        .sidebar.full-expanded { height: 40%; }

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

        /* HEADER PADRONIZADO */
        .main-header { 
            position: absolute; top: 0; width: 100%; z-index: 1000;
            padding: 15px 20px; background: var(--white); border-bottom: 1px solid #eee;
            display: flex; align-items: center;
            height: 60px;
        }
        
        .btn-back {
            color: var(--accent-green); /* SETINHA VERDE */
            text-decoration: none;
            font-size: 20px;
            margin-right: 15px;
        }

        .logo-container { display: contents; align-items: center; text-decoration: none; }
        
        .logo-icon {
            font-size: 22px; color: var(--primary-dark);
            margin-right: 6px; position: relative;
        }

        /* O PINGO VERDE OFICIAL */
        .logo-icon::after {
            content: ''; position: absolute; top: 0; right: -2px; 
            width: 6px; height: 6px; background-color: var(--accent-green);
            border-radius: 50%; border: 2px solid var(--white);
        }

        .logo-text { font-size: 20px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px; }
        .logo-text span { color: var(--accent-green); font-weight: 600; }

        /* BARRA DE ATUALIZAÇÃO */
        .update-bar {
            position: absolute; top: 62px; width: 100%; z-index: 999;
            background: var(--primary-dark); color: white;
            padding: 8px; text-align: center; font-size: 11px; font-weight: 600;
        }
        .update-bar span { color: var(--accent-green); }
        .sidebar.collapsed{
            height: 0px !important;
        }
        .pin.free.pcd {
            background-color: #2196F3; /* Um azul bonito para destacar */
            border-color: #0b7dda;
        }

        .main-header { 
            position: absolute; top: 0; width: 100%; z-index: 1000;
            padding: 15px 20px; background: var(--white); border-bottom: 1px solid #eee;
            display: flex; align-items: center;
        }
        .btn-back { color: var(--accent-green); text-decoration: none; font-size: 20px; margin-right: 15px; }
        .logo-text { font-size: 20px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px; }
        .logo-text span { color: var(--accent-green); }

        .update-bar {
            position: absolute; top: 62px; width: 100%; z-index: 999;
            background: var(--primary-dark); color: white;
            padding: 8px; text-align: center; font-size: 11px; font-weight: 600;
        }

        #pinch-wrapper { width: 100%; height: 100vh; background: #ebf0f5; display: flex; align-items: center; justify-content: center; }
        #map-area { width: 100%; height: 100%; position: relative; }

        .custom-map-svg { width: 100%; height: 100%; }

        .pin {
            position: absolute; width: 32px; height: 32px; border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg); display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 2px solid white; cursor: pointer; z-index: 100;
        }
        .pin i { transform: rotate(45deg); color: white; font-size: 12px; }
        .pin.free { background: var(--accent-green); }
        .pin.occupied { background: var(--accent-red); }

        /* Estilo Suave para Referências e Nomes */
        .map-label { fill: #64748b; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .street-name { fill: #94a3b8; font-size: 12px; font-weight: 800; letter-spacing: 0.5px; }
        .refbox { fill: #e2e8f0; rx: 6; ry: 6; }

        .sidebar {
            position: absolute; bottom: 0; left: 0; width: 100%;
            background: white; border-radius: 25px 25px 0 0;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1); z-index: 1100;
            trssansition: transform 0.4s ease; transadadssform: translateY(100%);
        }
        .sidebar.visible { transform: translateY(0); }
        .panel-content { padding: 20px; }
        
        .camera-feed-container {
            width: 100%; height: 180px; background: #000; border-radius: 15px;
            margin-top: 15px; position: relative; overflow: hidden;
        }
        .camera-slide { position: absolute; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 0.5s; }
        .camera-slide.active { opacity: 1; }
        .btn-close { width: 100%; padding: 12px; background: #f1f5f9; border: none; border-radius: 15px; font-weight: 700; margin-top: 15px; color: #475569; }

    </style>
</head>
<body>

    <header class="main-header">
        <a href="home.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <div class="logo-container">
            <div class="logo-icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="logo-text">Vaga<span>Livre</span></div>
        </div>
        <div style="margin-left: auto; font-weight: 700; font-size: 12px; color: #000;">
            <?php echo $rua; ?>
        </div>
    </header>

    <div class="update-bar">
        <i class="fas fa-sync-alt fa-spin"></i> Atualização: <span id="hora_atualizacao"></span>
    </div>


    <div class="menu-btn"><i class="fas fa-bars" style="color:var(--primary-dark)"></i></div>

    <div id="map-container">
        <svg class="custom-map-svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice">
            <rect width="200" height="200" fill="#e3e8ee"/>
            <path d="M80 -10 L120 210" stroke="white" stroke-width="14"/>
            <path d="M-10 80 L210 120" stroke="white" stroke-width="12"/>

            <!-- Referências Visuais  -->
            <!-- LADO ESQUERDO -->
            <div class="map-label" style="top: 33%;left: 48.5%;transform: rotate(81deg);border: 1px solid #64748b;padding: 10px;background-color: #64748b54;">🏢 Uninter</div>
            <div class="map-label" style="top: 35%;left: 21.5%;transform: rotate(260.4deg);border: 1px solid #64748b;padding: 10px;background-color: #64748b54;">🍴 Sakê</div>
            <div class="map-label" style="top: 69%;left: 56%;transform: rotate(80deg);border: 1px solid #64748b;padding: 10px;background-color: #64748b54;">🏢 Humanizzar</div>
            
        </svg>

        <div class="map-label" style="top:34%; left:27%; transform:rotate(80deg); font-size: 10px; color:#5a6d7e;"><?php echo $rua ?></div>
        <div class="map-label" style="top:47%; left:7%; transform:rotate(10deg);">Av. Vidal de Negreiros</div>
    </div>  

    <div class="sidebar collapsed" id="sidebar">
        <div class="sheet-header" id="drag-zone">
            <div class="drag-handle-container"><div class="drag-handle"></div></div>
            <div class="search-wrapper">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="">
                </div>
            </div>
        </div>

        <div class="panel-content">
            <div id="list-view">
                <div class="quick-filters">
                    <div class="filter-chip active">Tudo</div>
                    <div class="filter-chip">Livres</div>
                    <div class="filter-chip">Sombra</div>
                </div>
                <div class="suggestion-item">
                    <div class="s-icon"><i class="fas fa-store"></i></div>
                    <div><div style="font-weight:600; font-size:14px; color:#333">Comércio Central</div><div style="font-size:12px; color:#888;"><?php echo $rua ?></div></div>
                </div>
            </div>

            <div id="details-view">
                <div class="spot-header">
                    <div><h2 id="spot-title" style="margin:0; font-size:14px; color:#333;">Vaga</h2><span style="font-size:12px; color:#666;"><?php echo $rua ?></span></div>
                    <div class="spot-tag">LIVRE</div>
                </div>
                
                <div class="camera-feed-container" id="cameraCarousel" style="display:none">
                    <div class="camera-overlay"><i class="fas fa-circle"></i> AO VIVO</div>
                    <img src="./image_0.png" class="camera-slide active" alt="Cam 1">
                    <img src="./image_1.png" class="camera-slide" alt="Cam 2">
                    <img src="./image_2.png" class="camera-slide" alt="Cam 3">
                    
                    <div style="position:absolute; z-index:-1; width:100%; height:100%; background:#333; display:flex; align-items:center; justify-content:center; color:#666;">
                        <i class="fas fa-video-slash"></i>
                    </div>
                </div>
                
                <div style="margin-bottom:20px; color:#555; font-size:14px;">
                    <p><i class="fas fa-info-circle"></i> Vaga <span id="tipovaga"></span>, rotativo.</p>
                    <p><i class="fas fa-clock"></i> Tempo máx: <span id="tempovaga"></span></p>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>

        // Fazemos aqui a ideia de bloquear Zoom de Pinça
        document.addEventListener('touchmove', function (event) {
            if (event.scale !== 1) { 
                event.preventDefault(); 
            }
        }, { passive: false });

        // Fazemos aqui a parte de bloquear Zoom de Duplo Toque
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Fazemo aqui a parte de bloquear Zoom por Gestos
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        });

        const mapContainer = document.getElementById('map-container');
        const sidebar = document.getElementById('sidebar');
        const dragZone = document.getElementById('drag-zone');
        const searchInput = document.getElementById('searchInput');
        const listView = document.getElementById('list-view');
        const detailsView = document.getElementById('details-view');
        const spotTitle = document.getElementById('spot-title');
        const tempovaga = document.getElementById('tempovaga');
        const tipovaga = document.getElementById('tipovaga');

        // Controle da parte do slideshow
        let slideInterval;
        let currentSlide = 0;

        function startCameraCarousel() {
            const slides = document.querySelectorAll('.camera-slide');
            if (slides.length === 0) return;

            // Garante que começa do zero
            currentSlide = 0;
            updateSlides(slides);

            // Inicia o intervalo de 2 segundos
            slideInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % slides.length;
                updateSlides(slides);
            }, 2000);
        }

        function stopCameraCarousel() {
            clearInterval(slideInterval);
        }

        function updateSlides(slides) {
            slides.forEach((slide, index) => {
                if (index === currentSlide) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });
        }
        let vagas = [];
        function att(){
            // Inicia o AJAX
            $.ajax({
                url: './att_vagas.php',
                method: 'POST',
                success: function(response) {
                    vagas = response;
                    document.getElementById("hora_atualizacao").innerHTML = vagas[0].atualizadoEm;
                    generateRandomSpots();
                },
                error: function(error) {

                }
            });
        }
        

        function generateRandomSpots() {
            const existingPins = document.querySelectorAll('.pin:not(.user)');
            existingPins.forEach(pin => pin.remove());

            // Função auxiliar para mapear ícones e textos baseados no tipo da vaga livre
            function obterConfiguracaoVaga(tipo, isFree) {
                if (!isFree) return { icone: 'times', texto: '' }; // Se ocupada, sempre 'X'

                switch (tipo) {
                    case 'pcd':
                        return { icone: 'wheelchair', texto: ' (PCD)', tempo: 'Sem Limite', tipo:'PCD' };
                    case 'garagem':
                        return { icone: 'warehouse', texto: ' (Entrada de Garagem)', tempo: '2 min', tipo:'Garagem'}; // Ícone de garagem/galpão
                    case 'tempo_maximo':
                        return { icone: 'clock', texto: ' (Temporária)', tempo: '15 min', tipo:'Temporária' }; // Ícone de relógio
                    default:
                        return { icone: 'check', texto: 'normal', tempo: 'Sem Limite', tipo:'Normal' }; // Vaga comum livre
                }
            }

            // --- PRIMEIRO LOOP (Vagas 200 a 215) ---
            const numberOfSpots = 16;
            for (let i = 0; i < numberOfSpots; i++) {
                let pin = document.createElement('div');
                const spotId = i + 200;
                
                let vagaProcurada = vagas.find(vaga => vaga.id === spotId);
                let isFree = vagaProcurada ? vagaProcurada.status : 0;
                let tipoVaga = vagaProcurada ? vagaProcurada.tipo : 'comum';

                let progress = i / (numberOfSpots - 1);
                let baseLeft = 46 + (progress * 25);
                let baseTop = 21 + (progress * 80);

                if ((i > 4) && (i < 9)) {
                    baseLeft = 20 + (i * 8);
                    baseTop = 46 + (i * 0.8);
                }

                if ((i >= 9) && (i <= 16)) {
                    progress = (progress + 0.02) - 0.21;
                    baseLeft = 46 + (progress * 25);
                    baseTop = 21 + (progress * 80);
                }

                pin.id = `pin-vaga-${spotId}`; 
                
                // Adiciona o tipo como classe CSS (ex: pin free garagem)
                pin.className = `pin ${isFree ? 'free' : 'occupied'} ${tipoVaga}`;
                pin.style.top = baseTop + '%'; 
                pin.style.left = (baseLeft + (0 * 4 - 2)) + '%';
                
                // Obtém a configuração dinâmica de ícone e texto do modal
                const config = obterConfiguracaoVaga(tipoVaga, isFree);
                pin.innerHTML = `<i class="fas fa-${config.icone}"></i>`;

                if (isFree) {
                    pin.addEventListener('click', (e) => {
                        e.stopPropagation();
                        console.log(tipoVaga);
                        openSpotDetails(`Vaga #${spotId}${config.texto}`,config.tempo,config.tipo);
                    });
                }
                mapContainer.appendChild(pin);
            }

            // --- SEGUNDO LOOP (Vagas 216 a 218) ---
            for (let i = 0; i < 4; i++) {
                let pin = document.createElement('div');
                const spotId = i + 216;

                let vagaProcurada = vagas.find(vaga => vaga.id === spotId);
                let isFree = vagaProcurada ? vagaProcurada.status : 0;
                let tipoVaga = vagaProcurada ? vagaProcurada.tipo : 'comum';

                let progress = i / 4;
                let baseLeft = 46 + (progress * 5.2);
                let baseTop = 51 + (progress * 20);

                pin.id = `pin-vaga-${spotId}`;

                pin.className = `pin ${isFree ? 'free' : 'occupied'} ${tipoVaga}`;
                pin.style.top = baseTop + '%'; 
                pin.style.left = (baseLeft + (0 * 4 - 2)) + '%';
                
                const config = obterConfiguracaoVaga(tipoVaga, isFree);
                pin.innerHTML = `<i class="fas fa-${config.icone}"></i>`;

                if (isFree) {
                    pin.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openSpotDetails(`Vaga #${spotId}${config.texto}`,config.tempo,config.tipo);
                    });
                }
                mapContainer.appendChild(pin);
            }
        }





        // Gerencia os estados
        function setSidebarState(state) {
            sidebar.classList.remove('collapsed', 'half-expanded', 'full-expanded');
            if (state === 'collapsed')  {
                sidebar.classList.add('collapsed');
                //searchInput.blur();
                stopCameraCarousel(); // PARAR ANIMAÇÃO
                setTimeout(() => { if(sidebar.classList.contains('collapsed')) showListView(); }, 300);
            } else if (state === 'half') {
                 sidebar.classList.add('collapsed');
                //searchInput.blur();
                stopCameraCarousel(); // PARAR ANIMAÇÃO
                setTimeout(() => { if(sidebar.classList.contains('collapsed')) showListView(); }, 300);
            } else if (state === 'full') {
                sidebar.classList.add('full-expanded');
                // Dispara para animação começar na função openSpotDetails
            }
        }

        function showListView() {
            detailsView.style.display = 'none';
            listView.style.display = 'block';
        }

        function openSpotDetails(title,tempo,tipo) {
            spotTitle.innerText = title;
            tempovaga.innerText = tempo;
            tipovaga.innerText = tipo;

            
            listView.style.display = 'none';
            detailsView.style.display = 'block';
            setSidebarState('full');
            
            // INICIAR ANIMAÇÃO
            startCameraCarousel();
        }

        function backToList() {
            setSidebarState('half');
        }

        // Parte dos eventos
        //searchInput.addEventListener('focus', () => { setSidebarState('half'); });
        mapContainer.addEventListener('click', (e) => {
            if (!e.target.closest('.pin')) { setSidebarState('collapsed'); }
        });
        
        let startY = 0; let isDragging = false;
        dragZone.addEventListener('touchstart', (e) => { startY = e.touches[0].clientY; isDragging = true; });
        dragZone.addEventListener('touchend', (e) => {
            if (!isDragging) return; isDragging = false;
            const dist = startY - e.changedTouches[0].clientY;
            if (dist > 40) { if (sidebar.classList.contains('collapsed')) setSidebarState('half'); }
            else if (dist < -40) { 
                if (sidebar.classList.contains('full-expanded')) setSidebarState('half');
                else if (sidebar.classList.contains('half-expanded')) setSidebarState('collapsed');
            }
        });
        
        att();

        setInterval(() => {
            att();
        }, 2000);
    </script>
</body>
</html>