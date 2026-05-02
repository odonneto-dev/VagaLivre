<?php
session_start();
include("config.php");
include("restrito.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// Trava de segurança para Super Usuário
if ($login_usuario_id != 1) {
    header("Location: perfil.php");
    exit();
}

$mensagem = "";
$tipo_msg = "sucesso";

// --- LÓGICA DE EXCLUSÃO (DELETE) ---
if (isset($_GET['excluir'])) {
    $id_del = intval($_GET['excluir']);
    
    // Deleta das 3 tabelas relacionadas
    $mysqli->query("DELETE FROM monitoramento WHERE id_area = '$id_del'");
    $mysqli->query("DELETE FROM area WHERE id_area = '$id_del'");
    $mysqli->query("DELETE FROM consultas WHERE id_area = '$id_del'");
    
    $mensagem = "Ponto de monitoramento removido com sucesso!";
}

// --- LÓGICA DE CADASTRO / EDIÇÃO (CREATE / UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_ponto'])) {
    $nome_ponto = $mysqli->real_escape_string($_POST['nome_ponto']);
    $descricao = $mysqli->real_escape_string($_POST['descricao']);
    $endereco = $mysqli->real_escape_string($_POST['endereco']);
    $camera = $mysqli->real_escape_string($_POST['camera']);
    $id_edit = isset($_POST['id_area_edit']) ? intval($_POST['id_area_edit']) : 0;

    if ($id_edit > 0) {
        // UPDATE: Atualiza os dados existentes
        $mysqli->query("UPDATE area SET nome_area='$nome_ponto', descricao='$descricao', endereco='$endereco' WHERE id_area='$id_edit'");
        $mysqli->query("UPDATE monitoramento SET id_camera='$camera' WHERE id_area='$id_edit'");
        $mensagem = "Ponto atualizado com sucesso!";
    } else {
        // CREATE: Gera novo ID e insere
        $res = $mysqli->query("SELECT MAX(id_area) as total FROM area");
        $row = $res->fetch_assoc();
        $novo_id = $row['total'] + 1;

        $mysqli->query("INSERT INTO consultas (id_area, id_usuario) VALUES ('$novo_id', 1)");
        $mysqli->query("INSERT INTO area (id_area, descricao, endereco, nome_area) VALUES ('$novo_id', '$descricao', '$endereco', '$nome_ponto')");
        $mysqli->query("INSERT INTO monitoramento (id_area, id_camera) VALUES ('$novo_id', '$camera')");
        $mensagem = "Novo ponto cadastrado com sucesso!";
    }
}

// --- LÓGICA DE BUSCA PARA EDIÇÃO ---
$edit_data = null;
if (isset($_GET['editar'])) {
    $id_edit = intval($_GET['editar']);
    $res = $mysqli->query("SELECT a.*, m.id_camera FROM area a LEFT JOIN monitoramento m ON a.id_area = m.id_area WHERE a.id_area = '$id_edit'");
    $edit_data = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VagaLivre - Gerenciamento</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 para exclusão amigável -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-dark: #2b5876;
            --accent-green: #2ecc71;
            --accent-red: #e74c3c;
            --white: #FFFFFF;
            --text-dark: #2b5876;
            --gray-light: #f0f2f5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f9fa; color: var(--text-dark); }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 20px; }

        /* header padrão */
        .main-header { padding: 15px 0; border-bottom: 1px solid #eee; background: var(--white); position: sticky; top: 0; z-index: 100; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .logo-container { display: flex; align-items: center; text-decoration: none; }
        .logo-icon { font-size: 24px; color: var(--primary-dark); margin-right: 8px; position: relative; }
        .logo-icon::after { content: ''; position: absolute; top: 0; right: -2px; width: 6px; height: 6px; background-color: var(--accent-green); border-radius: 50%; border: 2px solid var(--white); }
        .logo-text { font-size: 22px; font-weight: 800; color: var(--primary-dark); letter-spacing: -1px; }
        .logo-text span { color: var(--accent-green); font-weight: 600; }

        .content { padding: 40px 0; display: grid; grid-template-columns: 1fr; gap: 30px; }

        .back-link { text-decoration: none; color: var(--accent-green); font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }

        /* Card Form */
        .card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-bottom: 5px solid var(--accent-green); }
        .card-header { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
        .card-header i { font-size: 30px; color: var(--primary-dark); }
        .card-header h1 { font-size: 18px; font-weight: 800; text-transform: uppercase; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 10px; font-weight: 700; color: #888; margin-bottom: 5px; text-transform: uppercase; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 2px solid transparent; background-color: var(--gray-light); border-radius: 10px; outline: none; transition: 0.3s; font-family: 'Montserrat'; }
        .form-group input:focus { border-color: var(--accent-green); background-color: #fff; }

        .btn-salvar { width: 100%; padding: 15px; background: var(--accent-green); color: white; font-weight: 700; border: none; border-radius: 30px; cursor: pointer; transition: 0.3s; text-transform: uppercase; }
        .btn-salvar:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3); }
        .btn-cancelar { display: block; text-align: center; margin-top: 10px; color: #888; text-decoration: none; font-size: 12px; }

        /* Tabela CRUD */
        .table-container { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 15px; font-size: 11px; text-transform: uppercase; color: #888; border-bottom: 2px solid var(--gray-light); }
        td { padding: 15px; font-size: 14px; border-bottom: 1px solid #eee; }
        
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; background: var(--gray-light); }
        
        .actions { display: flex; gap: 10px; }
        .btn-edit { color: var(--primary-dark); border: 1px solid var(--primary-dark); padding: 5px 10px; border-radius: 8px; text-decoration: none; transition: 0.2s; }
        .btn-edit:hover { background: var(--primary-dark); color: white; }
        .btn-del { color: var(--accent-red); border: 1px solid var(--accent-red); padding: 5px 10px; border-radius: 8px; text-decoration: none; transition: 0.2s; }
        .btn-del:hover { background: var(--accent-red); color: white; }

        .sucesso-msg { background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; text-align: center; }
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
                <a href="perfil.php"><i class="fas fa-user-circle" style="font-size: 28px; color: var(--accent-green);"></i></a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="content">
            <div>
                <a href="home.php" class="back-link"><i class="fas fa-arrow-left"></i> Voltar ao Início</a>
                
                <!-- FORMULÁRIO DE CADASTRO/EDIÇÃO -->
                <section class="card">
                    <div class="card-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <h1><?php echo $edit_data ? 'Editar Ponto' : 'Novo Ponto de Monitoramento'; ?></h1>
                    </div>

                    <?php if($mensagem != ""): ?>
                        <div class="sucesso-msg"><?php echo $mensagem; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <?php if($edit_data): ?>
                            <input type="hidden" name="id_area_edit" value="<?php echo $edit_data['id_area']; ?>">
                        <?php endif; ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nome do Ponto</label>
                                <input type="text" name="nome_ponto" value="<?php echo $edit_data ? $edit_data['nome_area'] : ''; ?>" placeholder="Ex: Av. Vidal de Negreiros" required>
                            </div>
                            <div class="form-group">
                                <label>Descrição</label>
                                <input type="text" name="descricao" value="<?php echo $edit_data ? $edit_data['descricao'] : ''; ?>" placeholder="Ex: Próximo à praça">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Endereço Completo</label>
                            <input type="text" name="endereco" value="<?php echo $edit_data ? $edit_data['endereco'] : ''; ?>" placeholder="Rua, Número, Bairro" required>
                        </div>

                        <div class="form-group">
                            <label>Câmera Vinculada</label>
                            <select name="camera" required>
                                <option value="1" <?php echo ($edit_data && $edit_data['id_camera'] == 1) ? 'selected' : ''; ?>>Câmera 01 - Setor Leste</option>
                                <option value="2" <?php echo ($edit_data && $edit_data['id_camera'] == 2) ? 'selected' : ''; ?>>Câmera 02 - Setor Oeste</option>
                            </select>
                        </div>

                        <button type="submit" name="salvar_ponto" class="btn-salvar">
                            <?php echo $edit_data ? 'Atualizar Ponto' : 'Cadastrar Ponto'; ?>
                        </button>
                        
                        <?php if($edit_data): ?>
                            <a href="superusuario.php" class="btn-cancelar">Cancelar Edição</a>
                        <?php endif; ?>
                    </form>
                </section>
            </div>

            <!-- TABELA DE LISTAGEM (O "R" DO CRUD) -->
            <section class="table-container">
                <div class="card-header">
                    <i class="fas fa-list"></i>
                    <h1>Pontos Ativos</h1>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome / Endereço</th>
                            <th>Câmera</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $mysqli->query("SELECT a.*, m.id_camera FROM area a LEFT JOIN monitoramento m ON a.id_area = m.id_area ORDER BY a.id_area DESC");
                        while($row = $res->fetch_assoc()):
                        ?>
                        <tr>
                            <td><span class="badge">#<?php echo $row['id_area']; ?></span></td>
                            <td>
                                <strong><?php echo $row['nome_area']; ?></strong><br>
                                <small style="color:#888"><?php echo $row['endereco']; ?></small>
                            </td>
                            <td><i class="fas fa-video"></i> CAM <?php echo $row['id_camera']; ?></td>
                            <td class="actions">
                                <a href="?editar=<?php echo $row['id_area']; ?>" class="btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" onclick="confirmarExclusao(<?php echo $row['id_area']; ?>)" class="btn-del" title="Excluir"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <script>
    function confirmarExclusao(id) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2ecc71',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'superusuario.php?excluir=' + id;
            }
        })
    }
    </script>
</body>
</html>