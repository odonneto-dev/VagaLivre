<?php
session_start();
include("config.php");
include("restrito.php");

$link_perfil="perfil.php";
if ($login_usuario_id==1)
    $link_perfil="superusuario.php";

$dados=[];
$sql2="select * from monitoramento m INNER JOIN camera c ON c.id_camera=m.id_camera INNER JOIN area a ON a.id_area=m.id_area;";
$campos2 = $mysqli->query($sql2);
while($obj2 = $campos2->fetch_object())
    array_push($dados,(array) $obj2);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VagaLivre - Home</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;800&display=swap" rel="stylesheet">

    <style>
        /* VARIÁVEIS  */
        :root {
            --primary-dark: #2b5876;
            --primary-light: #4e4376;
            --accent-green: #2ecc71;
            --white: #FFFFFF;
            --gray-bg: #f0f2f5;
            --text-dark: #2b5876;
        }

        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* HEADER IDENTICO À HOME */
        .main-header {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            background: var(--white);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 24px;
            color: var(--primary-dark);
            margin-right: 8px;
            position: relative;
        }

        /* O pingo verde no logo */
        .logo-icon::after {
            content: '';
            position: absolute;
            top: 0; right: -2px; width: 6px; height: 6px;
            background-color: var(--accent-green);
            border-radius: 50%; border: 2px solid var(--white);
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

        .user-profile i {
            font-size: 28px;
            color: var(--accent-green);
            cursor: pointer;
        }

        /* BUSCA */
        .search-section {
            margin: 40px auto;
            max-width: 550px;
            text-align: center;
        }

        .search-container {
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border-radius: 30px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            outline: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            transition: 0.3s;
        }

        .search-container input:focus {
            border-color: var(--accent-green);
            background-color: #fff;
        }

        .search-container i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-green);
        }

        /* GRID E CARDS*/
        .grid-areas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .area-card {
            text-decoration: none;
            display: block;
            border-radius: 20px; 
            overflow: hidden;
            position: relative;
            height: 280px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            border-bottom: 5px solid var(--accent-green);
            transition: transform 0.3s ease;
        }

        .area-card:hover {
            transform: translateY(-5px);
        }

        .card-image {
            width: 100%;
            height: 100%;
            background-image: url('./Img/av-vidal.jpg'); 
            background-size: cover;
            background-position: center;
        }

        /* Camada de labels sobre a imagem */
        .card-labels {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .location-tag {
            background: var(--white);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .action-icon {
            background: var(--white);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .action-icon i {
            color: var(--accent-green);
            font-size: 14px;
        }

        /* RESPONSIVIDADE */
        @media (max-width: 768px) {
            .grid-areas {
                grid-template-columns: 1fr;
            }
            .search-section {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <a href="home.php" class="logo-container">
                <i class="fas fa-car-side logo-icon"></i>
                <div class="logo-text">Vaga<span>Livre</span></div>
            </a>
            <div class="user-profile">
                <a href="<?php echo $link_perfil ?>" class="logo-container">
                    <i class="fas fa-user-circle"></i>
                </a>
            </div>
        </div>
    </header>

    <main class="container">
        
        <section class="search-section">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar área monitorada...">
            </div>
        <section class="grid-areas" id="gridAreas" style="margin-top: 40px;">

        <?php foreach ($dados as $key => $value){ ?>
        <a href="visualizar_area.php?id=<?php echo $value['id_monitoramento'] ?>" class="area-card" data-name="<?php echo $value['nome_area'].' ('.$value['localizacao'].')'  ?>">
            <div class="card-image"></div>
            <div class="card-labels">
                <div class="location-tag"><?php echo $value['nome_area'].' ('.$value['localizacao'].')' ?></div>
                <div class="action-icon">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                </div>
            </div>
        </a>
        <?php } ?>

        <div id="no-results" style="display: none; text-align: center; padding: 40px; width: 100%; grid-column: 1 / -1; color: #999;">
            <i class="fas fa-search-minus" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--accent-green);"></i>
            <p style="font-weight: 500;">Área não monitorada ou não encontrada.</p>
        </div>

    </section>

    </main>


    <script>
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.area-card');
        const noResults = document.getElementById('no-results');

        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            let found = false;

            cards.forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                if (name.includes(filter)) {
                    card.style.display = "block";
                    found = true;
                } else {
                    card.style.display = "none";
                }
            });

            // Mostra a mensagem se 'found' for falso
            noResults.style.display = found ? "none" : "block";
        });
    </script>
</body>
</html>