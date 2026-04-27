<?php
include("config.php"); 
include("restrito.php"); 

@$id_ref=$_GET['id'];
$dados=[];
$sql2="select * from monitoramento m INNER JOIN camera c ON c.id_camera=m.id_camera INNER JOIN area a ON a.id_area=m.id_area WHERE m.id_monitoramento=".$id_ref;
$campos2 = $mysqli->query($sql2);
while($obj2 = $campos2->fetch_object())
    $avenida_nome=$obj2->localizacao;

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
            --gray-bg: #e3e8ee;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Montserrat', sans-serif; background: var(--gray-bg); overflow: hidden; height: 100vh; }

        /* header */
        .main-header { 
            position: fixed; top: 0; width: 100%; z-index: 1000;
            padding: 15px 20px; background: var(--white); border-bottom: 1px solid #eee;
            display: flex; align-items: center; gap: 15px;
        }
        
        .btn-back-home {
            color: var(--primary-dark);
            text-decoration: none;
            font-size: 20px;
            padding-right: 10px;
            border-right: 1px solid #eee;
        }

        .logo-container { display: flex; align-items: center; text-decoration: none; }
        .logo-text { font-size: 18px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px; }
        .logo-text span { color: var(--accent-green); }

        /* barra atualização */
        .update-bar {
            position: fixed; top: 65px; width: 100%; z-index: 999;
            background: var(--primary-dark); color: white;
            padding: 10px; text-align: center; font-size: 12px; font-weight: 600;
        }
        .update-bar span { color: var(--accent-green); font-weight: 800; }

        /* mapa */
        #map-container {
            width: 100%; height: 100vh; position: relative; background: var(--gray-bg);
            display: flex; align-items: center; justify-content: center;
        }

        .custom-map-svg { width: 140%; height: 140%; transform: rotate(-15deg); }

        /* pin vaga */
        .pin {
            position: absolute; width: 38px; height: 38px; border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg); display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); border: 2px solid white;
        }
        .pin i { transform: rotate(45deg); color: white; font-size: 16px; }
        .pin.free { background: var(--accent-green); }
        .pin.occupied { background: var(--accent-red); }
        
        /* pin de localização */
        .pin.user { 
            background: #3498db; width: 18px; height: 18px; border: 3px solid white; 
            border-radius: 50%; transform: none;
        }

        /* legenda */
        .map-legend {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: white; padding: 15px 25px; border-radius: 50px;
            display: flex; gap: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: var(--primary-dark); }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
    </style>
</head>
<body>

    <header class="main-header">
        <a href="home.php" class="btn-back-home" title="Voltar para Home">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="logo-container">
            <i class="fas fa-car-side" style="color: var(--primary-dark); font-size: 18px; margin-right: 8px;"></i>
            <div class="logo-text">Vaga<span>Livre</span></div>
        </div>
        <div style="margin-left: auto; font-weight: 700; font-size: 13px; color: var(--primary-dark); opacity: 0.8;">
            <?php echo $avenida_nome; ?>
        </div>
    </header>

    <div class="update-bar">
        <i class="fas fa-sync-alt fa-spin"></i> Atualização em tempo real: <span><?php echo $ultima_atualizacao; ?></span>
    </div>

    <div id="map-container">
        <svg class="custom-map-svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice">
            <rect width="200" height="200" fill="#e3e8ee"/>
            <path d="M40 -10 L160 210" stroke="white" stroke-width="28"/> 
            <path d="M40 -10 L160 210" stroke="#bdc3c7" stroke-width="1" stroke-dasharray="6,6"/>
        </svg>

        <div class="pin user" style="top:65%; left:52%;"></div>

        <div class="pin free" style="top:25%; left:38%;"><i class="fas fa-check"></i></div>
        <div class="pin occupied" style="top:40%; left:50%;"><i class="fas fa-times"></i></div>
        <div class="pin free" style="top:55%; left:62%;"><i class="fas fa-check"></i></div>
        <div class="pin occupied" style="top:15%; left:30%;"><i class="fas fa-times"></i></div>
        <div class="pin free" style="top:75%; left:75%;"><i class="fas fa-check"></i></div>
    </div>

    <div class="map-legend">
        <div class="legend-item"><div class="dot" style="background: var(--accent-green);"></div> Livre</div>
        <div class="legend-item"><div class="dot" style="background: var(--accent-red);"></div> Ocupada</div>
    </div>

</body>
</html>