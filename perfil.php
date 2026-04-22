<?php
session_start();
include("config.php");
include("restrito.php");

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$id = $login_usuario_id;

// --- SALVAR ALTERAÇÕES ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar'])) {
    $nome = $mysqli->real_escape_string($_POST['nome']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $telefone = $mysqli->real_escape_string($_POST['telefone']);

    $sqlUpdate = "UPDATE usuario SET nome = '$nome', email = '$email', telefone = '$telefone' WHERE id_usuario = $id";
    $mysqli->query($sqlUpdate);
    header("Location: perfil.php?atualizado=1");
    exit();
}

// --- EXCLUIR PERFIL ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['excluir'])) {
    $sqlDelete = "DELETE FROM usuario WHERE id_usuario = $id";
    if ($mysqli->query($sqlDelete)) {
        header("Location: logout.php");
        exit();
    }
}

// --- BUSCAR DADOS ---
$sql = "SELECT * FROM usuario WHERE id_usuario = $id";
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VagaLivre - Dados Pessoais</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;800&display=swap" rel="stylesheet">

    <style>
        /* váriaveis */
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --white: #FFFFFF;
            --text-dark: #2b5876;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

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

        /* header padrão */
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
            text-decoration: none; /* Remove sublinhado do link */
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
        }

        /* conteúdo do perfil */
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 0;
        }

        .back-container {
            width: 100%;
            max-width: 580px;
            margin-bottom: 20px;
        }

        .back-link {
            text-decoration: none;
            color: var(--accent-green);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

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
        .card-header i { font-size: 55px; color: var(--primary-dark); margin-bottom: 10px; }
        .card-header h1 { font-size: 24px; font-weight: 800; color: var(--primary-dark); }

        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #888;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: none;
            background-color: #f0f2f5;
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
        }

        .readonly-input { opacity: 0.6; cursor: not-allowed; }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            gap: 15px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }

        .btn-excluir {
            background: transparent;
            border: 1.5px solid #FF6B6B;
            color: #FF6B6B;
        }

        .btn-salvar {
            background: var(--accent-green);
            color: white;
            flex-grow: 1;
        }

        .btn:hover { opacity: 0.8; transform: translateY(-1px); }

        @media (max-width: 620px) {
            .card { width: 95%; padding: 30px 20px; }
            .actions { flex-direction: column-reverse; }
            .btn { width: 100%; }
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
                    <i class="fas fa-user-circle"></i>
                </a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="content">
            <div class="back-container">
                <a href="home.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Voltar para Início
                </a>
            </div>

            <section class="card">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i>
                    <h1>Dados Pessoais</h1>
                </div>

                <form method="POST" action="perfil.php">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" value="<?php echo htmlspecialchars($user['telefone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" value="********" readonly class="readonly-input">
                    </div>

                    <div class="actions">
                        <button type="submit" name="excluir" class="btn btn-excluir" onclick="return confirm('Excluir conta permanentemente?')">
                            Excluir perfil
                        </button>
                        <button type="submit" name="salvar" class="btn btn-salvar">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

</body>
</html>