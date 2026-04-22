<?php
session_start();
include("config.php");
include("restrito.php");

// Verifica se o usuário está logado (pode verificar se é admin depois)
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$mensagem = "";

// --- lógica de cadastro / update do super ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_ponto'])) {
    $nome_ponto = $mysqli->real_escape_string($_POST['nome_ponto']);
    $descricao = $mysqli->real_escape_string($_POST['descricao']);
    $endereco = $mysqli->real_escape_string($_POST['endereco']);
    $camera = $mysqli->real_escape_string($_POST['camera']);

    $mensagem = "Ponto de monitoramento atualizado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VagaLivre - Super Usuário</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --white: #FFFFFF;
            --text-dark: #2b5876;
            --gray-light: #f0f2f5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white);
            color: var(--text-dark);
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* header padrão */
        .main-header { padding: 15px 0; border-bottom: 1px solid #eee; background: var(--white); }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .logo-container { display: flex; align-items: center; text-decoration: none; }
        .logo-icon { font-size: 24px; color: var(--primary-dark); margin-right: 8px; position: relative; }
        .logo-icon::after { content: ''; position: absolute; top: 0; right: -2px; width: 6px; height: 6px; background-color: var(--accent-green); border-radius: 50%; border: 2px solid var(--white); }
        .logo-text { font-size: 22px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px; }
        .logo-text span { color: var(--accent-green); font-weight: 600; }

        /* conteúdo */
        .content { display: flex; flex-direction: column; align-items: center; padding: 40px 0; }
        .back-container { width: 100%; max-width: 580px; margin-bottom: 20px; }
        .back-link { text-decoration: none; color: var(--accent-green); font-weight: 600; display: flex; align-items: center; gap: 8px; }

        .card {
            background: white;
            width: 100%;
            max-width: 580px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-bottom: 5px solid var(--accent-green);
        }

        .card-header { text-align: center; margin-bottom: 30px; }
        .card-header i { font-size: 50px; color: var(--primary-dark); margin-bottom: 10px; }
        .card-header h1 { font-size: 22px; font-weight: 800; color: var(--primary-dark); text-transform: uppercase; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; font-weight: 700; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid transparent;
            background-color: var(--gray-light);
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--accent-green);
            background-color: #fff;
        }

        /* campo de arquivo */
        .input-file-container {
            position: relative;
            background-color: var(--gray-light);
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            border: 2px dashed #ccc;
        }

        .btn-salvar {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            border-radius: 30px;
            background: var(--accent-green);
            color: white;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-salvar:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3); }

        .sucesso-msg {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
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
                <a href="perfil.php" class="logo-container">
                    <i class="fas fa-user-circle" style="font-size: 28px; color: var(--accent-green);"></i>
                </a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="content">
            <div class="back-container">
                <a href="home.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Painel de Controle
                </a>
            </div>

            <section class="card">
                <div class="card-header">
                    <i class="fas fa-user-shield"></i>
                    <h1>Configuração do Ponto</h1>
                </div>

                <?php if($mensagem != ""): ?>
                    <div class="sucesso-msg"><?php echo $mensagem; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nome do Ponto de Monitoramento</label>
                        <input type="text" name="nome_ponto" placeholder="Ex: Estacionamento Vidal de Negreiros" required>
                    </div>

                    <div class="form-group">
                        <label>Descrição curta</label>
                        <input type="text" name="descricao" placeholder="Ex: Área central próxima ao comércio">
                    </div>

                    <div class="form-group">
                        <label>Endereço Completo</label>
                        <input type="text" name="endereco" placeholder="Rua, Número, Bairro" required>
                    </div>

                    <div class="form-group">
                        <label>Selecionar Dispositivo de Vídeo (Câmera)</label>
                        <select name="camera" required>
                            <option value="">Selecione uma câmera...</option>
                            <option value="cam_01">Câmera Principal - Entrada</option>
                            <option value="cam_02">Câmera Secundária - Fundos</option>
                            <option value="cam_usb">Webcam USB Local</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Foto de Identificação do Local</label>
                        <div class="input-file-container">
                            <input type="file" name="foto_ponto" accept="image/*">
                        </div>
                    </div>

                    <button type="submit" name="salvar_ponto" class="btn-salvar">
                        Salvar Configurações
                    </button>
                </form>
            </section>
        </div>
    </main>

</body>
</html>