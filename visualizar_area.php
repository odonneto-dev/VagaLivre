<?php
include("config.php"); 
include("restrito.php"); 

@$id_ref=$_GET['id'];
$dados=[];
$sql2="select * from monitoramento m INNER JOIN camera c ON c.id_camera=m.id_camera INNER JOIN area a ON a.id_area=m.id_area WHERE m.id_monitoramento=".$id_ref;
$campos2 = $mysqli->query($sql2);
while($obj2 = $campos2->fetch_object())
    $avenida_nome=$obj2->nome_area.' - '.$obj2->localizacao;

$ultima_atualizacao = date("H:i:s");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VagaLivre - Monitoramento</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --white: #FFFFFF;
            --gray-bg: #f0f2f5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Montserrat', sans-serif; background: var(--gray-bg); overflow: hidden; height: 100vh; }

        /* HEADER PADRONIZADO */
        .main-header { 
            position: fixed; top: 0; width: 100%; z-index: 1000;
            padding: 15px 20px; background: var(--white); border-bottom: 1px solid #eee;
            display: flex; align-items: center;
        }
        
        .btn-back {
            color: var(--accent-green); /* SETINHA VERDE */
            text-decoration: none;
            font-size: 20px;
            margin-right: 15px;
        }

        .logo-container { display: flex; align-items: center; text-decoration: none; }
        
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
            position: fixed; top: 62px; width: 100%; z-index: 999;
            background: var(--primary-dark); color: white;
            padding: 8px; text-align: center; font-size: 11px; font-weight: 600;
        }
        .update-bar span { color: var(--accent-green); }

        /* MAPA */
        #map-container {
            width: 100%; height: 100vh; position: relative; background: var(--gray-bg);
            display: flex; align-items: center; justify-content: center;
        }
        .custom-map-svg { width: 150%; height: 150%; transform: rotate(-10deg); }

        /* PINS */
        .pin {
            position: absolute; width: 35px; height: 35px; border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg); display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); border: 2px solid white; cursor: pointer;
        }
        .pin i { transform: rotate(45deg); color: white; font-size: 14px; }
        .pin.free { background: var(--accent-green); }
        .pin.occupied { background: var(--accent-red); }
        .pin.user { 
            background: #3498db; width: 16px; height: 16px; border: 2px solid white; 
            border-radius: 50%; transform: none; cursor: default;
        }

        /* SIDEBAR / SHEET (DO INDEX) */
        .sidebar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: white; border-radius: 25px 25px 0 0;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.15); z-index: 1100;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: translateY(100%); /* Começa escondida */
        }
        .sidebar.visible { transform: translateY(0); }

        .sheet-header { padding: 20px; border-bottom: 1px solid #f0f0f0; text-align: center; }
        .drag-handle { width: 40px; height: 4px; background: #ddd; border-radius: 2px; margin: 0 auto 15px; }
        
        .panel-content { padding: 0 20px 30px; max-height: 70vh; overflow-y: auto; }
        
        .spot-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .spot-tag { background: #e8fcf0; color: #2ecc71; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 700; }

        /* CARROSSEL DE CÂMERAS */
        .camera-feed-container {
            width: 100%; height: 180px; background: #000; border-radius: 15px;
            margin-bottom: 20px; position: relative; overflow: hidden;
        }
        .camera-slide {
            position: absolute; width: 100%; height: 100%; object-fit: cover;
            opacity: 0; transition: opacity 0.5s;
        }
        .camera-slide.active { opacity: 1; }
        .camera-overlay {
            position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.5);
            color: white; padding: 4px 10px; border-radius: 10px; font-size: 10px; z-index: 2;
        }
        .camera-overlay i { color: #ff4757; margin-right: 5px; }

        .btn-action {
            width: 100%; padding: 15px; background: var(--primary-dark);
            color: white; border: none; border-radius: 15px; font-weight: 700; margin-bottom: 10px;
        }
        .btn-close {
            width: 100%; padding: 12px; background: #f0f2f5;
            color: #666; border: none; border-radius: 15px; font-weight: 600;
        }
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
            <?php echo $avenida_nome; ?>
        </div>
    </header>

    <div class="update-bar">
        <i class="fas fa-sync-alt fa-spin"></i> Atualização: <span><?php echo $ultima_atualizacao; ?></span>
    </div>

    <div id="map-container">
        <svg class="custom-map-svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice">
            <rect width="200" height="200" fill="#e3e8ee"/>
            <path d="M40 -10 L160 210" stroke="white" stroke-width="25"/> 
        </svg>

        <div class="pin user" style="top:60%; left:55%;"></div>

        <div class="pin free" style="top:25%; left:38%;" onclick="verDetalhes('Vaga #101', 'LIVRE')"><i class="fas fa-check"></i></div>
        <div class="pin occupied" style="top:40%; left:50%;" onclick="verDetalhes('Vaga #102', 'OCUPADA')"><i class="fas fa-times"></i></div>
        <div class="pin free" style="top:55%; left:62%;" onclick="verDetalhes('Vaga #103', 'LIVRE')"><i class="fas fa-check"></i></div>
    </div>

    <div class="sidebar" id="detailsSidebar">
    <div class="sheet-header">
        <div class="drag-handle"></div>
    </div>
    <div class="panel-content">
        <div class="spot-header">
            <div>
                <h2 id="spotTitle" style="font-size:20px; color:#000;">Vaga</h2>
                <span style="font-size:12px; color:#666;">Av. Vidal de Negreiros</span>
            </div>
            <div id="spotStatus" class="spot-tag">LIVRE</div>
        </div>

        <div class="camera-feed-container">
            <div class="camera-overlay"><i class="fas fa-circle"></i> AO VIVO</div>
            <img src="Img/image_0.png" class="camera-slide active" alt="Cam 1">
            <img src="Img/image_1.png" class="camera-slide" alt="Cam 2">
            <img src="Img/image_2.png" class="camera-slide" alt="Cam 3">
        </div>

        <div style="margin-bottom:20px; color:#555; font-size:13px;">
            <p style="margin-bottom:5px;"><i class="fas fa-info-circle"></i> Monitorada por sensor infravermelho.</p>
            <p><i class="fas fa-clock"></i> Tempo sugerido: 60 min.</p>
        </div>

        <button class="btn-close" onclick="fecharDetalhes()">Fechar</button>
    </div>
    </div>

    <script>
        let slideInterval;
        let currentSlide = 0;

        function verDetalhes(titulo, status) {
            document.getElementById('spotTitle').innerText = titulo;
            document.getElementById('spotStatus').innerText = status;
            
            // Ajusta cor da tag se estiver ocupada
            const tag = document.getElementById('spotStatus');
            tag.style.background = (status === 'LIVRE') ? '#e8fcf0' : '#ffebeb';
            tag.style.color = (status === 'LIVRE') ? '#2ecc71' : '#e74c3c';

            document.getElementById('detailsSidebar').classList.add('visible');
            startCarousel();
        }

        function fecharDetalhes() {
            document.getElementById('detailsSidebar').classList.remove('visible');
            clearInterval(slideInterval);
        }

        function startCarousel() {
            const slides = document.querySelectorAll('.camera-slide');
            currentSlide = 0;
            clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }, 2000);
        }

        // Fecha ao clicar no mapa
        document.getElementById('map-container').addEventListener('click', (e) => {
            if (!e.target.closest('.pin')) fecharDetalhes();
        });
    </script>
</body>
</html>