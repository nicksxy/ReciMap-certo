<?php
session_start();
 
if (!isset($_SESSION["id_usuario"])) {
    header('Location: login.php?erro=4');
    exit;
}
 
$id = $_SESSION["id_usuario"];
$apelido = "";
 
include "conn.php";
 
// Busque o apelido
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />
  <title>Jogo da Reciclagem</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');
    body {
      font-family: 'Montserrat', sans-serif;
      background: radial-gradient(circle at top left, #50c878, #006400);
      margin: 0;
      padding: 20px;
      color: #f0f0f0;
      user-select: none;
      overflow-x: hidden;
    }
    h1 {
      text-align: center;
      font-size: 3rem;
      margin-bottom: 10px;
      text-shadow: 0 0 15px #aaffaa;
    }
    #game {
      max-width: 900px;
      margin: 0 auto;
      background: #004d00cc;
      border-radius: 20px;
      padding: 25px 30px;
      box-shadow: 0 0 30px #00ff44aa;
      border: 2px solid #00ff44;
    }
    #instructions {
      font-weight: 700;
      font-size: 1.2rem;
      text-align: center;
      margin-bottom: 20px;
      text-shadow: 0 0 5px #00ff44;
    }
    #name-input {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 20px;
    }
    #name-input input {
      padding: 10px;
      font-size: 16px;
      border-radius: 10px;
      border: none;
      margin-top: 10px;
      width: 60%;
      max-width: 300px;
      text-align: center;
    }
    #items {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
      margin-bottom: 40px;
      min-height: 120px;
    }
    .item {
  width: 110px;
  min-height: 130px;
  background: #0a4d0a;
  border-radius: 15px;
  box-shadow: 0 0 10px #00ff44;
  display: flex;
  flex-direction: column; /* 👈 imagem em cima, texto embaixo */
  align-items: center;
  justify-content: center;
  cursor: grab;
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  user-select: none;
  border: 2px solid transparent;
  padding: 8px;
  gap: 6px;
}

.item {
  width: 110px;
  min-height: 130px;
  background: #0a4d0a;
  border-radius: 15px;
  box-shadow: 0 0 10px #00ff44;
  display: flex;
  flex-direction: column; /* 👈 imagem em cima, texto embaixo */
  align-items: center;
  justify-content: center;
  cursor: grab;
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  user-select: none;
  border: 2px solid transparent;
  padding: 8px;
  gap: 6px;
}

.item p {
  font-size: 12px;
  font-weight: 700;
  text-align: center;
  color: #eaffea;
  margin: 0;
}


    .item.dragging {
      opacity: 0.7;
      cursor: grabbing;
      transform: scale(1.15);
      box-shadow: 0 0 20px #00ff88;
      border-color: #00ff88;
      z-index: 1000;
    }
    .item img {
      max-width: 70px;
      max-height: 70px;
      pointer-events: none;
      filter: drop-shadow(0 0 3px #00ff88);
    }
    .bins {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 25px;
    flex-wrap: wrap; /* Força uma linha só */
    overflow-x: auto; /* Se precisar, pode rolar horizontalmente */
    }
 
    .bin {
      width: 140px;
      height: 180px;
      border-radius: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      color: white;
      user-select: none;
      position: relative;
      box-shadow: 0 0 15px rgba(0,0,0,0.6);
      transition: background-color 0.4s, border-color 0.4s, box-shadow 0.4s;
      cursor: pointer;
      border: 3px solid transparent;
      text-align: center;
      padding: 10px;
    }
    .bin img {
      width: 80px;
      margin-bottom: 8px;
      filter: drop-shadow(0 0 5px rgba(255,255,255,0.7));
    }
    .bin.reciclavel { background: linear-gradient(145deg, #2196F3, #1769aa); border-color: #64b5f6; box-shadow: 0 0 15px #64b5f6; }
    .bin.organico { background: linear-gradient(145deg, #8bc34a, #558b2f); border-color: #aed581; box-shadow: 0 0 15px #aed581; }
    .bin.radioativo { background: linear-gradient(145deg, #fbc02d, #f9a825); border-color: #fdd835; box-shadow: 0 0 15px #fdd835; }
    .bin.hospitalar { background: linear-gradient(145deg, #e91e63, #880e4f); border-color: #f48fb1; box-shadow: 0 0 15px #f48fb1; }
    .bin.eletronico { background: linear-gradient(145deg, #9c27b0, #6a0080); border-color: #ba68c8; box-shadow: 0 0 15px #ba68c8; }
    .bin.correct { box-shadow: 0 0 25px 5px #76ff03 !important; transform: scale(1.1); }
    .bin.wrong { box-shadow: 0 0 25px 5px #ff1744 !important; transform: scale(1.1); }
    .bin::after {
      content: '⬇ Arraste aqui';
      position: absolute;
      bottom: 8px;
      font-size: 13px;
      color: rgba(255,255,255,0.8);
      font-weight: 700;
      text-shadow: 0 0 5px #000000a0;
    }
 
    #scoreboard {
      display: flex;
      justify-content: space-around;
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 0 8px #000;
    }
 
    #restart-btn, #start-btn {
      background-color: #2e7d32;
      color: white;
      border: none;
      padding: 14px 32px;
      border-radius: 18px;
      font-size: 20px;
      cursor: pointer;
      display: block;
      margin: 25px auto 0 auto;
      box-shadow: 0 0 10px #76ff03;
    }
    .btn-logout {
            display: block;
            cursor: pointer;
            width: 120px;
            margin: 20px auto;
            padding: 10px 0;
            background-color: #2e7d32;
            color: white;
            border: none;
            text-align: center;
            text-decoration: none;
            border-radius: 18px;
            font-weight: bold;
            box-shadow: 0 0 10px #76ff03;
            transition: background-color 0.3s;
    }
    .btn-logout:hover {
      background-color: #1b5e20;
      box-shadow: 0 0 15px #76ff03;
    }
 
 
 
    #restart-btn:hover, #start-btn:hover {
      background-color: #1b5e20;
      box-shadow: 0 0 15px #76ff03;
    }
 
    #message {
      margin-top: 25px;
      font-size: 22px;
      font-weight: 900;
      min-height: 40px;
      text-align: center;
      text-shadow: 0 0 10px #00ff44;
    }
 
    .bounce { animation: bounce 0.5s ease; }
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }
 
    #ranking {
      margin-top: 30px;
      padding: 15px;
      border-radius: 15px;
      background: #003300;
      box-shadow: 0 0 15px #00ff44;
    }
 
    #ranking h3 {
      text-align: center;
      margin-bottom: 10px;
      color: #aaffaa;
    }
 
    #ranking ol {
      padding-left: 20px;
    }
 
    #ranking li {
      font-size: 16px;
      margin: 4px 0;
    }
 
    #items, .bins, #scoreboard, #restart-btn, #message, #ranking {
      display: none;
    }
  </style>
</head>
<body>
 
<h1>Jogo da Reciclagem</h1>
<div id="welcome" style="text-align: center; font-size: 1.2rem; margin-bottom: 10px;">
  Bem-vindo, <strong><?= htmlspecialchars($apelido) ?></strong>!
</div>
 
<div id="game">
  <div id="instructions">Arraste os itens para a lixeira correta antes que o tempo acabe!</div>
 
 
  <button id="start-btn">Iniciar Jogo</button>
  <a href="index.php" class="btn-logout">Sair</a>
 
  <div id="scoreboard">
    <div>Acertos: <span id="score">0</span></div>
    <div>Erros: <span id="errors">0</span></div>
    <div>Tempo: <span id="timer">45</span>s</div>
  </div>
 
  </div>
 
  <div id="items"></div>
 
  <div class="bins">
    <div class="bin reciclavel" data-bin="reciclavel"><img src="./IMG/lixeira- reciclavel.webp" />Reciclável</div>
    <div class="bin organico" data-bin="organico"><img src="./IMG/lixeira - organico.webp" />Orgânico</div>
    <div class="bin radioativo" data-bin="radioativo"><img src="./IMG/lixeira - radioativo.webp" />Radioativo</div>
    <div class="bin hospitalar" data-bin="hospitalar"><img src="./IMG/lixeira - hospitalar.webp" />Hospitalar</div>
    <div class="bin eletronico" data-bin="eletronico"><img src="./IMG/lixeira-eletronico.webp" />Eletrônico</div>
  </div>
 
  <button id="restart-btn">Reiniciar Jogo</button>
  <div id="message"></div>
  <div id="ranking">
    <h3>🏆 Ranking dos Melhores</h3>
    <ol id="ranking-list"></ol>
  </div>
</div>
 
<!-- Sons -->
<audio id="sound-correct" src="https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg" preload="auto"></audio>
<audio id="sound-wrong" src="https://actions.google.com/sounds/v1/cartoon/cartoon_boing.ogg" preload="auto"></audio>
<audio id="sound-win" src="https://actions.google.com/sounds/v1/cartoon/clang.ogg" preload="auto"></audio>
 
<script>
  const itemsData = [
    { name: "Jornal", type: "reciclavel", img: "./IMG/jornal.png" },
    { name: "Garrafa Plástica", type: "reciclavel", img: "./IMG/garrafa[.png" },
    { name: "Banana (Casca)", type: "organico", img: "./IMG/casca de banana.png" },
    { name: "Papelão", type: "reciclavel", img: "./IMG/papelãp.png" },
    { name: "Contêiner Radioativo", type: "radioativo", img: "./IMG/latão radioativo.png" },
    { name: "Restos de comida", type: "organico", img: "./IMG/resto de comida.png" },
    { name: "Seringa Usada", type: "hospitalar", img: "./IMG/seringa.png" },
    { name: "Pote de Vidro", type: "reciclavel", img: "./IMG/pote de vidro.png" },
    { name: "Lâmpada Queimada", type: "eletronico", img: "./IMG/lampada.png" },
    { name: "Fralda descartável", type: "hospitalar", img: "./IMG/fralda.png" },
    { name: "Bateria", type: "eletronico", img: "./IMG/bateria de carro.png" },
    { name: "Saco plástico", type: "reciclavel", img: "./IMG/saco plastico.png" },
    { name: "Cascas de Maçã", type: "organico", img: "./IMG/maça mordida.png" },
    { name: "Embalagem de Alumínio", type: "reciclavel", img: "./IMG/embalagem de metal.png" },
    { name: "Resíduo Hospitalar", type: "hospitalar", img: "./IMG/residuos hospitalares 1.png" },
    { name: "Celular Quebrado", type: "eletronico", img: "./IMG/celular quebrado.png" },
    { name: "Folhas Secas", type: "organico", img: "./IMG/folhas secas.png" },
    { name: "Caixa de Leite", type: "reciclavel", img: "./IMG/caixa de leite.png" },
    { name: "Tinta Radioativa", type: "radioativo", img: "./IMG/tinta radioativa.png" },
    { name: "Bandagem Usada", type: "hospitalar", img: "./IMG/bandeide.png" },
    { name: "Monitor Queimado", type: "eletronico", img: "./IMG/monitor de pc.png" },
    { name: "Restos de Frutas", type: "organico", img: "./IMG/resto de frutas.png" },
    { name: "Lata de Alumínio", type: "reciclavel", img: "./IMG/latinha.png" },
    { name: "Ampola Médica", type: "hospitalar", img: "./IMG/ampola medica.png" },
    { name: "Rádio Quebrado", type: "eletronico", img: "./IMG/radio.png" },
    { name: "Carregador USB", type: "eletronico", img: "./IMG/carregador.png" },
    { name: "Cascas de Ovo", type: "organico", img: "./IMG/casca de ovo.png" },
    { name: "Pilha Velha", type: "eletronico", img: "./IMG/pilha.png" },
    { name: "Embalagem de Plástico", type: "reciclavel", img: "./IMG/plastico.png" },
    { name: "Máscara Descartável", type: "hospitalar", img: "./IMG/mascara.png" }
  ];
 
  const itemsContainer = document.getElementById('items');
  const bins = document.querySelectorAll('.bin');
  const scoreEl = document.getElementById('score');
  const errorsEl = document.getElementById('errors');
  const timerEl = document.getElementById('timer');
  const messageEl = document.getElementById('message');
  const restartBtn = document.getElementById('restart-btn');
  const startBtn = document.getElementById('start-btn');
  const scoreboard = document.getElementById('scoreboard');
  const rankingList = document.getElementById('ranking-list');
  const rankingSection = document.getElementById('ranking');
 
  let score = 0, errors = 0, timeLeft = 45, timerInterval = null, draggedItem = null;
  const playerName = "<?= addslashes($apelido) ?>";
 
  // 🔹 Agora teremos uma fila de itens (30), e 10 aparecem de cada vez
  let shuffledItems = [];
  let visibleItems = [];
 
  function shuffleItems() {
    shuffledItems = itemsData.sort(() => Math.random() - 0.5);
  }
 
  function createItemElement(item) {
  const div = document.createElement('div');
  div.classList.add('item');
  div.setAttribute('draggable', 'true');
  div.setAttribute('data-type', item.type);

  const img = document.createElement('img');
  img.src = item.img;
  img.alt = item.name;

  const name = document.createElement('p');
  name.textContent = item.name; // 👈 nome embaixo da imagem

  div.appendChild(img);
  div.appendChild(name);

  div.addEventListener('dragstart', dragStart);
  div.addEventListener('dragend', dragEnd);

  return div;
}

 
  function loadInitialItems() {
    itemsContainer.innerHTML = '';
    shuffleItems();
    visibleItems = shuffledItems.splice(0, 10); // 10 primeiros
    visibleItems.forEach(item => itemsContainer.appendChild(createItemElement(item)));
  }
 
  // 🔹 Substitui o item acertado por um novo (se houver)
  function replaceItem() {
    if (shuffledItems.length > 0) {
      const newItem = shuffledItems.shift();
      itemsContainer.appendChild(createItemElement(newItem));
    } else if (itemsContainer.children.length === 0) {
      // acabou tudo
      endGame();
    }
  }
 
  function dragStart(e) {
    draggedItem = this;
    this.classList.add('dragging');
    e.dataTransfer.setData('text/plain', this.dataset.type);
  }
 
  function dragEnd() {
    this.classList.remove('dragging');
  }
 
  bins.forEach(bin => {
    bin.addEventListener('dragover', e => e.preventDefault());
    bin.addEventListener('drop', e => {
      e.preventDefault();
      if (!draggedItem) return;
 
      const itemType = draggedItem.dataset.type;
      const binType = bin.dataset.bin;
 
      if (itemType === binType) {
        score++;
        playSound('correct');
        scoreEl.textContent = score;
        showMessage('✅ Muito bem! Item correto.', true);
        bin.classList.add('correct');
        setTimeout(() => bin.classList.remove('correct'), 800);
        draggedItem.remove();
        replaceItem();
      } else {
        errors++;
        playSound('wrong');
        errorsEl.textContent = errors;
        showMessage('❌ Oops! Item incorreto.', false);
        bin.classList.add('wrong');
        setTimeout(() => bin.classList.remove('wrong'), 800);
      }
      draggedItem = null;
    });
  });
 
  function showMessage(msg, success) {
    messageEl.textContent = msg;
    messageEl.style.color = success ? '#aaffaa' : '#ff5555';
    messageEl.classList.add('bounce');
    setTimeout(() => messageEl.classList.remove('bounce'), 600);
  }
 
  function playSound(type) {
    const sounds = {
      correct: 'sound-correct',
      wrong: 'sound-wrong',
      win: 'sound-win'
    };
    const audio = document.getElementById(sounds[type]);
    audio.currentTime = 0;
    audio.play();
  }
 
  function startTimer() {
    timeLeft = 45;
    timerEl.textContent = timeLeft;
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
      timeLeft--;
      timerEl.textContent = timeLeft;
      if (timeLeft <= 0) {
        clearInterval(timerInterval);
        endGame();
      }
    }, 1000);
  }
 
  function saveToRanking(score) {
    fetch('save_score.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ score: score })
    })
    .then(res => res.json())
    .then(() => showRanking())
    .catch(err => console.error('Erro ao salvar pontuação:', err));
  }
 
  function showRanking() {
    fetch('get_ranking.php')
      .then(res => res.json())
      .then(data => {
        rankingList.innerHTML = data.map(item => `<li>${item.apelido}: ${item.pontuacao} pts</li>`).join('');
        rankingSection.style.display = 'block';
      })
      .catch(err => console.error('Erro ao carregar ranking:', err));
  }
 
  function endGame() {
    clearInterval(timerInterval);
    itemsContainer.innerHTML = '';
    playSound('win');
    showMessage(`🏁 Fim de jogo, ${playerName}! Acertos: ${score} | Erros: ${errors}`, true);
    saveToRanking(score);
    restartBtn.style.display = 'block';
  }
 
  startBtn.addEventListener('click', () => {
    startBtn.style.display = 'none';
    score = 0;
    errors = 0;
    scoreEl.textContent = score;
    errorsEl.textContent = errors;
    itemsContainer.style.display = 'flex';
    document.querySelector('.bins').style.display = 'flex';
    scoreboard.style.display = 'flex';
    messageEl.textContent = '';
    rankingSection.style.display = 'none';
    restartBtn.style.display = 'none';
    loadInitialItems();
    startTimer();
  });
 
  restartBtn.addEventListener('click', () => location.reload());
 
  showRanking();
</script>

 
</body>
</html>
 