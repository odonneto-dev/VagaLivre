<?php
include("config.php"); 
include("restrito.php"); 

@$id_ref=$_GET['id'];
$sql2="select * from monitoramento m 
       INNER JOIN camera c ON c.id_camera=m.id_camera 
       INNER JOIN area a ON a.id_area=m.id_area 
       WHERE m.id_monitoramento=".$id_ref;

$campos2 = $mysqli->query($sql2);
$area_id_atual = 0; 

if($obj2 = $campos2->fetch_object()){
    $avenida_nome = $obj2->nome_area.' - '.$obj2->localizacao;
    $area_id_atual = $obj2->id_area; 
}
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
    <script src="https://unpkg.com/pinch-zoom-js@2.3.4/dist/pinch-zoom.umd.min.js"></script>

    <style>
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --white: #FFFFFF;
            --gray-bg: #f8fafc; /* Fundo mais suave */
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Montserrat', sans-serif; background: var(--gray-bg); overflow: hidden; height: 100vh; }

        .main-header { 
            position: fixed; top: 0; width: 100%; z-index: 1000;
            padding: 15px 20px; background: var(--white); border-bottom: 1px solid #eee;
            display: flex; align-items: center;
        }
        .btn-back { color: var(--accent-green); text-decoration: none; font-size: 20px; margin-right: 15px; }
        .logo-text { font-size: 20px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px; }
        .logo-text span { color: var(--accent-green); }

        .update-bar {
            position: fixed; top: 62px; width: 100%; z-index: 999;
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
        .ref-box { fill: #e2e8f0; rx: 6; ry: 6; }

        .sidebar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: white; border-radius: 25px 25px 0 0;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1); z-index: 1100;
            transition: transform 0.4s ease; transform: translateY(100%);
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
        <div class="logo-text">Vaga<span>Livre</span></div>
        <div style="margin-left: auto; font-size: 10px; font-weight: 700; color: #94a3b8;"><?php echo $avenida_nome; ?></div>
    </header>

    <div class="update-bar">Atualização: <span><?php echo $ultima_atualizacao; ?></span></div>

    <div id="pinch-wrapper">
        <div id="map-area">
            <svg class="custom-map-svg" viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice">
                <rect x="0" y="0" width="1000" height="1000" fill="#ebf0f5"/>
                
                <!-- Desenho das Ruas  -->
                <path d="M420 -100 L580 1100" stroke="#f8fafc" stroke-width="80" fill="none" /> <!-- R. Darcílio -->
                <path d="M-100 880 L1100 380" stroke="#f8fafc" stroke-width="100" fill="none" /> <!-- Av. Vidal -->

                <text x="400" y="625" class="street-name" transform="rotate(-24, 500, 625)">AVENIDA VIDAL DE NEGREIROS</text>
                <text x="512" y="300" class="street-name" transform="rotate(82, 472, 285)">RUA DARCÍLIO VANDERLEY</text>

                <!-- Referências Visuais  -->
                <!-- LADO ESQUERDO -->
                <rect x="110" y="470" width="160" height="40" class="ref-box" />
                <text x="130" y="495" class="map-label">🏢 Unniter</text>
                
                <rect x="290" y="910" width="100" height="40" class="ref-box" />
                <text x="310" y="935" class="map-label">🍴 Sakê</text>

                <!-- LADO DIREITO -->
                <rect x="710" y="260" width="190" height="45" class="ref-box" />
                <text x="730" y="288" class="map-label">📍 Jessyellen P.</text>

                <rect x="790" y="670" width="180" height="45" class="ref-box" />
                <text x="805" y="698" class="map-label">📐 Jayny Gomes Arq.</text>
            </svg>

            <!-- PINS ÁREA 1 -->
            <?php if ($area_id_atual == 1): ?>
                <div class="pin free" style="top:74%; left:25%;" onclick="verDetalhes('Vaga #01', 'LIVRE')"><i class="fas fa-check"></i></div>
                <div class="pin occupied" style="top:69%; left:38%;" onclick="verDetalhes('Vaga #02', 'OCUPADA')"><i class="fas fa-times"></i></div>
            
            <!-- PINS ÁREA 2 -->
            <?php else: ?>
                <div class="pin free" style="top:47%; left:53%;" onclick="verDetalhes('Vaga #08', 'LIVRE')"><i class="fas fa-check"></i></div>
                <div class="pin occupied" style="top:58%; left:68%;" onclick="verDetalhes('Vaga #09', 'OCUPADA')"><i class="fas fa-times"></i></div>
                <div class="pin free" style="top:52%; left:82%;" onclick="verDetalhes('Vaga #10', 'LIVRE')"><i class="fas fa-check"></i></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="sidebar" id="detailsSidebar">
        <div class="panel-content">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 id="spotTitle" style="font-size:22px; color:var(--primary-dark);">Vaga</h2>
                <div id="spotStatus" style="font-weight:800; padding:5px 15px; border-radius:20px;">LIVRE</div>
            </div>

            <div class="camera-feed-container">
                <img src="Img/image_0.png" class="camera-slide active" alt="Cam 1">
                <img src="Img/image_1.png" class="camera-slide" alt="Cam 2">
            </div>

            <button class="btn-close" onclick="fecharDetalhes()">Fechar</button>
        </div>
    </div>

    <script>
        const el = document.getElementById('map-area');
        new PinchZoom(el, { draggableUnzoomed: true, minZoom: 1, maxZoom: 4 });

        let slideInterval;
        let currentSlide = 0;

        function verDetalhes(titulo, status) {
            document.getElementById('spotTitle').innerText = titulo;
            const tag = document.getElementById('spotStatus');
            tag.innerText = status;
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
            }, 2500);
        }
    </script>
</body>
</html>