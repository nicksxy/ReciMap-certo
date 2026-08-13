<?php
// Carrega as mensagens do arquivo JSON
session_start();
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
  header('Location: login.php?msg=2');
  exit;
}


$file = 'mensagens.json';
if (!file_exists($file)) {
  $mensagens = [];
} else {
  $mensagens = json_decode(file_get_contents($file), true) ?? [];
}


include "conn.php";

$id = $_SESSION["id_usuario"];
$apelido = "";

$stmt = $conn->prepare("SELECT apelido FROM tab_usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($apelido);
$stmt->fetch();
$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel do Administrador</title>
  <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />
  <link href="./css/styles.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    /* ======== RESET ======== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url("./IMG/mundo.jpg");
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ======== HEADER ======== */
    header {
      background-color: #000;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      position: relative;
      z-index: 10;
    }

    header h1 {
      font-size: 1.5rem;
      color: #64a19d;
      flex: 1;
    }

    /* ======== NAV ======== */
    nav {
      flex: 2;
      text-align: center;
    }

    nav ul {
      list-style: none;
      display: flex;
      justify-content: center;
      gap: 1rem;
      padding-top: 1rem;
      padding-left: 0;
      transition: all 0.3s ease;
    }

    nav a {
      color: #eee;
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: background-color 0.2s, color 0.2s;
    }

    nav a:hover {
      color: #64a19d;
    }

    /* ======== BEM-VINDO ======== */
    .welcome,
    .nav-item.nav-link {
      flex: 1;
      text-align: right;
      color: #ccc;
      text-decoration: none;
      transition: color 0.2s;
    }

    .nav-item.nav-link:hover {
      color: #64a19d;
    }

    /* ======== BOTÃO DE MENU ======== */
    .navbar-toggler {
      display: none;
      background: none;
      border: 1px solid #64a19d;
      color: #64a19d;
      padding: 0.4rem 0.8rem;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      transition: all 0.3s;
    }

    .navbar-toggler:hover {
      background-color: #64a19d;
      color: #fff;
    }

    .navbar-toggler i {
      margin-left: 5px;
    }

    /* ======== CONTEÚDO ======== */
    main {
      flex: 1;
      width: 100%;
      max-width: 900px;
      margin: 2rem auto;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      padding: 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 1rem;
    }

    .mensagens {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .mensagem {
      background-color: #64a19d;
      color: #fff;
      border-radius: 8px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.7rem;
    }

    .mensagem h3 {
      margin-bottom: 0.3rem;
      font-size: 1.2rem;
    }

    .mensagem p {
      font-size: 1rem;
    }

    .info-adicional {
      background-color: rgba(255, 255, 255, 0.15);
      padding: 0.5rem;
      border-radius: 5px;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .botoes {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      justify-content: flex-end;
    }

    button {
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: background-color 0.2s;
      font-size: 0.9rem;
    }

    .aceitar {
      background-color: #2ecc71;
      color: white;
    }

    .aceitar:hover {
      background-color: #27ae60;
    }

    .negar {
      background-color: #e74c3c;
      color: white;
    }

    .negar:hover {
      background-color: #c0392b;
    }

    .status {
      font-weight: bold;
      font-size: 0.9rem;
      padding: 5px 10px;
      border-radius: 5px;
      text-align: center;
    }

    .aceito {
      background-color: #d5f5e3;
      color: #27ae60;
    }

    .negado {
      background-color: #f5b7b1;
      color: #c0392b;
    }

    /* ======== RESPONSIVO ======== */
    @media (max-width: 992px) {
      nav ul {
        padding-right: 0;
      }
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .navbar-toggler {
        display: block;
        margin-bottom: 0.8rem;
      }

      nav {
        width: 100%;
      }

      nav ul {
        flex-direction: column;
        width: 100%;
        background-color: #000;
        display: none;
        padding: 10px 0;
        gap: 0;
      }

      nav ul.show {
        display: flex;
        animation: slideDown 0.3s ease forwards;
      }

      @keyframes slideDown {
        from {
          opacity: 0;
          transform: translateY(-10px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      nav ul li {
        text-align: center;
        padding: 0.7rem 0;
      }

      .welcome,
      .nav-item.nav-link {
        width: 100%;
        text-align: center;
        margin-top: 0.6rem;
      }

      main {
        margin: 1rem;
        padding: 1rem;
      }

      h2 {
        font-size: 1.2rem;
      }

      .mensagem p {
        font-size: 0.9rem;
      }

      button {
        font-size: 0.85rem;
      }
    }

    @media (max-width: 480px) {
      header h1 {
        font-size: 1.2rem;
      }

      .navbar-toggler {
        font-size: 0.9rem;
        padding: 0.3rem 0.7rem;
      }

      .mensagem h3 {
        font-size: 1rem;
      }

      main {
        padding: 0.8rem;
      }
    }
  </style>
</head>

<body>
  <header>
    <button class="navbar-toggler" type="button" id="menuBtn">
      Menu <i class="fas fa-bars"></i>
    </button>

    <h1>Painel do Administrador</h1>

    <nav>
      <ul id="navbarResponsive">
        <li><a href="index.php">Home</a></li>
        <li><a href="cadastro_adm.php">Cadastrar Admin</a></li>
        <li><a href="historico_admin.php">Histórico</a></li>
      </ul>
    </nav>

    <a href="#" class="nav-item nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
      Bem-vindo, <strong><?= htmlspecialchars($apelido) ?></strong>!

    </a>


  </header>


  <main>
    <?php if (isset($_GET['msg'])): ?>
      <div style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; padding:10px; margin-bottom:15px; border-radius:5px; text-align:center;">
        <?= htmlspecialchars($_GET['msg']) ?>
      </div>
    <?php endif; ?>
    <h2>Mensagens Recebidas</h2>
    <div class="mensagens">
      <?php if (empty($mensagens)): ?>
        <p>Nenhuma mensagem encontrada.</p>
      <?php else: ?>
        <?php foreach ($mensagens as $msg): ?>
          <div class="mensagem">
            <div class="conteudo">
              <h3><?= htmlspecialchars($msg['nome']) ?></h3>
              <p><strong>Endereço:</strong> <?= htmlspecialchars($msg['endereco']) ?></p>
              <p><strong>Telefone:</strong> <?= htmlspecialchars($msg['telefone']) ?></p>
              <p><strong>Horário:</strong> <?= htmlspecialchars($msg['horario']) ?></p>
              <p><strong>Materiais:</strong> <?= htmlspecialchars($msg['material']) ?></p>
            </div>

            <div class="info-adicional">
              <p><strong>Solicitado por:</strong> <?= htmlspecialchars($msg['apelido']) ?> (<?= htmlspecialchars($msg['email']) ?>)</p>
              <p><strong>Latitude:</strong> <?= htmlspecialchars($msg['latitude']) ?> |
                <strong>Longitude:</strong> <?= htmlspecialchars($msg['longitude']) ?>
              </p>
            </div>

            <div class="botoes">
              <?php if ($msg['status'] === 'pendente'): ?>
                <form action="atualizar_ponto.php" method="POST">
                  <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                  <input type="hidden" name="acao" value="aceitar">
                  <input type="hidden" name="material" value="<?= $msg['material'] ?>">
                  <button class="aceitar">Aceitar</button>
                </form>

                <form action="atualizar_ponto.php" method="POST">
                  <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                  <input type="hidden" name="acao" value="negar">
                  <button class="negar">Negar</button>
                </form>
              <?php elseif ($msg['status'] === 'aceito'): ?>
                <span class="status aceito">✅ Aceito</span>
              <?php elseif ($msg['status'] === 'negado'): ?>
                <span class="status negado">❌ Negado</span>
              <?php endif; ?>
            </div>
          </div>

        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="logoutModalLabel">Deseja realmente sair?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            Ao sair, você será redirecionado para a tela de login.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <a href="logout.php" class="btn btn-success">Sim, sair</a>
          </div>
        </div>
  </main>

  <script>
    const menuBtn = document.getElementById('menuBtn');
    const navMenu = document.getElementById('navbarResponsive');

    menuBtn.addEventListener('click', () => {
      navMenu.classList.toggle('show');
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>