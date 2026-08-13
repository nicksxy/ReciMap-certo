<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
  // Redireciona para a página de login com uma mensagem de erro (código 4)
  header('Location: login.php?erro=4');
  exit; // Importante para garantir que o script pare aqui
}

// Se chegou até aqui, o usuário está logado
$id = $_SESSION["id_usuario"];

// A partir daqui, coloque o restante do código da página protegida...
?>




<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Notícias sobre Lixo, Sustentabilidade e Reciclagem</title>
<title>ReciMap</title>
  <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link href="./css/styles.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
 
<style>
    body {
        font-family: Arial, sans-serif;
        background: #eef3ee;
        padding: 20px;
        padding-top: 100px;
    }
 
    h1 { text-align: center; color: #2d7d46; }

    #news-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }
 
    .card {
        width: 300px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
 
    .card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        background: #ddd;
    }
 
    .card-content {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
 
    .card-content h3 {
        margin: 0 0 10px;
        font-size: 18px;
        color: #2d7d46;
    }
 
    .card-content p {
        font-size: 14px;
        color: #444;
        flex: 1;
    }
 
    .card-content a {
        margin-top: 10px;
        display: inline-block;
        text-decoration: none;
        background: #2d7d46;
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        text-align: center;
    }
 
    .loading {
        text-align: center;
        font-size: 20px;
        margin-top: 40px;
    }
    
    .tag {
        display: inline-block;
        background: #4CAF50;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-right: 5px;
        margin-top: 5px;
    }

    button {
        padding: 10px 20px;
        background: #2196F3;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
</style>
</head>
<body>



<style>
/* Navbar com cor sólida */
    /* Navbar com cor sólida */
    #mainNav {
        background-color: black;
        /* substitua pela cor que quiser */

        background-image: none;
        /* remove qualquer gradiente */
    }

    /* Links do menu */
    #mainNav .nav-link {
        color: white;
        /* cor do texto */
        transition: color 0.3s;
        /* efeito suave no hover */
    }

    /* Hover nos links */
    #mainNav .nav-link:hover {
        color: #64a19d;
        /* cor quando passa o mouse */
        text-decoration: none;
        /* remove sublinhado */
    }

    /* Botão admin (se existir) */
    #mainNav .btn-warning {
        background-color: #FFC107;
        /* cor sólida */
        border: none;
        transition: background-color 0.3s;
    }

    #mainNav .btn-warning:hover {
        background-color: #268d2bff;
        /* cor ao passar o mouse */
    }


/* Botão admin */
#mainNav .btn-warning {
    background-color: #FFC107;
    border: none;
    transition: background-color 0.3s;
}

#mainNav .btn-warning:hover {
    background-color: white;
    color: #000;
}

/* Dropdown estilizado */
.navbar .dropdown-menu {
    background-color: #000;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0;
    opacity: 0;
    transform: scaleY(0);
    transform-origin: top;
    transition: all 0.3s ease;
    display: block;
    visibility: hidden;
}

.navbar .dropdown-menu.show {
    opacity: 1;
    transform: scaleY(1);
    visibility: visible;
}

.navbar .dropdown-menu .dropdown-item {
    color: #fff;
    padding: 10px 20px;
    transition: background-color 0.3s, padding-left 0.3s;
}

.navbar .dropdown-menu .dropdown-item:hover {
    background-color: #64a19d;
    padding-left: 25px;
}

/* Ícone seta do dropdown */
#arrowIcon.rotate {
    transform: rotate(180deg);
}

/* Mobile ajuste */
@media (max-width: 991px) {
    #mainNav .navbar-nav {
        text-align: center;
    }
}


/* Container do filtro centralizado */
.filter-container {
    position: relative;
    display: flex;
    justify-content: center; /* centraliza horizontalmente */
    margin: 30px 0;
}

/* Select estilizado */
#themeFilter {
    background: #2d7d46;
    color: #fff;
    padding: 12px 40px 12px 20px; /* espaço para a seta */
    font-size: 16px;
    border-radius: 30px;
    border: none;
    outline: none;
    cursor: pointer;
    min-width: 300px;
    appearance: none; /* remove seta padrão */
    transition: background 0.3s, transform 0.2s;
}

/* Hover/foco */
#themeFilter:hover,
#themeFilter:focus {
   
    transform: scale(1.03);
}

/* Ícone da seta */
.dropdown-icon {
    position: absolute;
    right: calc(50% - 150px + 15px); /* centraliza relativo ao select */
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    pointer-events: none; /* não bloqueia o select */
    transition: transform 0.3s;
}

/* Rotação ao focar no select */
#themeFilter:focus + .dropdown-icon {
    transform: translateY(-50%) rotate(180deg);
}

/* Mobile responsivo */
@media (max-width: 576px) {
    #themeFilter {
        width: 90%;
    }
    .dropdown-icon {
        right: 5%; /* ajusta no mobile */
    }
}


</style>


<style>
/* Navbar com cor sólida */
    /* Navbar com cor sólida */
    #mainNav {
        background-color: black;
        /* substitua pela cor que quiser */

        background-image: none;
        /* remove qualquer gradiente */
    }

    /* Links do menu */
    #mainNav .nav-link {
        color: white;
        /* cor do texto */
        transition: color 0.3s;
        /* efeito suave no hover */
    }

    /* Hover nos links */
    #mainNav .nav-link:hover {
        color: #64a19d;
        /* cor quando passa o mouse */
        text-decoration: none;
        /* remove sublinhado */
    }

    /* Botão admin (se existir) */
    #mainNav .btn-warning {
        background-color: #FFC107;
        /* cor sólida */
        border: none;
        transition: background-color 0.3s;
    }

    #mainNav .btn-warning:hover {
        background-color: #268d2bff;
        /* cor ao passar o mouse */
    }


/* Botão admin */
#mainNav .btn-warning {
    background-color: #FFC107;
    border: none;
    transition: background-color 0.3s;
}

#mainNav .btn-warning:hover {
    background-color: white;
    color: #000;
}

/* Dropdown estilizado */
.navbar .dropdown-menu {
    background-color: #000;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0;
    opacity: 0;
    transform: scaleY(0);
    transform-origin: top;
    transition: all 0.3s ease;
    display: block;
    visibility: hidden;
}

.navbar .dropdown-menu.show {
    opacity: 1;
    transform: scaleY(1);
    visibility: visible;
}

.navbar .dropdown-menu .dropdown-item {
    color: #fff;
    padding: 10px 20px;
    transition: background-color 0.3s, padding-left 0.3s;
}

.navbar .dropdown-menu .dropdown-item:hover {
    background-color: #64a19d;
    padding-left: 25px;
}

/* Ícone seta do dropdown */
#arrowIcon.rotate {
    transform: rotate(180deg);
}

/* Mobile ajuste */
@media (max-width: 991px) {
    #mainNav .navbar-nav {
        text-align: center;
    }
}

</style>


<body id="page-top">
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-black fixed-top" id="mainNav">
  <div class="container px-4 px-lg-4">

    <!-- Botão do menu mobile -->
    <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
      aria-label="Toggle navigation">
      Menu <i class="fas fa-bars"></i>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="navbarResponsive">
      <!-- LADO ESQUERDO -->
      <ul class="navbar-nav h6 gap-2 align-items-center">
        <li><a href="index.php" class="nav-item nav-link">Home</a></li>
        <li><a href="game.php" class="nav-item nav-link">Game</a></li>
          <li><a href="noticias.php" class="nav-item nav-link">Notícias</a></li>

        <!-- Dropdown Tipos de Resíduos (desktop) -->
        <li class="nav-item dropdown position-relative d-none d-lg-block">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="residuosDropdown" role="button">
            <i class="fas fa-recycle me-2 text-success"></i>
            Tipos de Resíduos
            <i class="fas fa-chevron-down ms-2" id="arrowIcon" style="transition: transform 0.3s;"></i>
          </a>

          <ul class="dropdown-menu bg-dark shadow border-0" aria-labelledby="residuosDropdown" id="residuosMenu">
            <li><a class="dropdown-item text-white" href="organico.php">🌿 Orgânicos</a></li>
            <li><a class="dropdown-item text-white" href="reciclaveis.php">♻️ Recicláveis</a></li>
            <li><a class="dropdown-item text-white" href="radioativo.php">☢️ Radioativo</a></li>
            <li><a class="dropdown-item text-white" href="hospitalares.php">⚕️ Hospitalares</a></li>
            <li><a class="dropdown-item text-white" href="eletronicos.php">💻 Eletrônicos</a></li>
          </ul>
        </li>

        <!-- Lista direta (mobile) -->
        <div class="d-lg-none mobile-residuos">
          <hr class="menu-sep">
          <li><a class="nav-link text-white" href="organico.php">🌿 Orgânicos</a></li>
          <li><a class="nav-link text-white" href="reciclaveis.php">♻️ Recicláveis</a></li>
          <li><a class="nav-link text-white" href="radioativo.php">☢️ Radioativo</a></li>
          <li><a class="nav-link text-white" href="hospitalares.php">⚕️ Hospitalares</a></li>
          <li><a class="nav-link text-white" href="eletronicos.php">💻 Eletrônicos</a></li>
          <hr class="menu-sep">
        </div>
      </ul>

      <!-- LADO DIREITO -->
      <ul class="navbar-nav h6 gap-2 align-items-center">
        <?php if (isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'admin'): ?>
          <li><a href="administradores.php" class="btn btn-warning fw-bold">Painel do Administrador</a></li>
        <?php endif; ?>

        <li>
          <a href="#" class="nav-item nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
            Desconectar
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
 
<h1>🌱 Notícias sobre Lixo, Sustentabilidade e Reciclagem</h1>
<div class="filter-container">
  <select id="themeFilter" onchange="aplicarFiltro()">
      <option value="todos">🔎 Todos os temas</option>
      <option value="reciclagem">♻️ Reciclagem</option>
      <option value="lixo resíduos">🗑️ Lixo e Resíduos</option>
      <option value="sustentabilidade">🌱 Sustentabilidade</option>
      <option value="meio ambiente">🌍 Meio Ambiente</option>
      <option value="descarte consciente">🚮 Descarte Consciente</option>
  </select>
  <i class="fas fa-chevron-down dropdown-icon"></i>
</div>



<div id="news-container"></div>
<div id="loading" class="loading">Carregando notícias...</div>
<!-- 
<button onclick="testarAPIs()">Testar Outras Fontes</button> -->
 
<script>
let temaSelecionado = "todos";
let pageNumbar = 1;
const articlesLoadeds = new Set();

function aplicarFiltro() {
    temaSelecionado = document.getElementById("themeFilter").value;
    pageNumbar = 1;
    articlesLoadeds.clear();
    document.getElementById("news-container").innerHTML = "";
    getNewsFromNewsAPI();
}

// Modifique a função getNewsFromNewsAPI() para usar o filtro
async function getNewsFromNewsAPI() {
    if (window.loadingNews) return;
    window.loadingNews = true;

    const container = document.getElementById("news-container");
    const loading = document.getElementById("loading");
    loading.style.display = "block";
    loading.innerText = "Carregando notícias...";

    try {
        const temasConsulta = temaSelecionado === "todos" 
            ? temas 
            : [temaSelecionado];

        const promises = temasConsulta.map(async (tema) => {
            const url = `https://newsapi.org/v2/everything?` +
                        `q=${encodeURIComponent(tema)}&` +
                        `language=pt&` +
                        `pageSize=10&` +
                        `page=${pageNumber}&` +
                        `sortBy=relevancy&` +
                        `apiKey=${NEWSAPI_KEY}`;
            const resp = await fetch(url);
            const data = await resp.json();
            return data.articles || [];
        });

        const results = await Promise.all(promises);
        const allArticles = results.flat();

        const filteredArticles = allArticles.filter(article => {
            if (!article.url || articlesLoaded.has(article.url)) return false;
            const texto = (article.title + ' ' + article.description).toLowerCase();
            if (!keywords.some(k => texto.includes(k))) return false;
            articlesLoaded.add(article.url);
            return true;
        });

        if (filteredArticles.length === 0 && pageNumber === 1) {
            container.innerHTML = "<p>Nenhuma notícia encontrada sobre lixo, reciclagem ou sustentabilidade.</p>";
        }

        filteredArticles.forEach(article => {
            const tags = identificarTags(article);
            const card = document.createElement("div");
            card.className = "card";
            card.innerHTML = `
                <img src="${article.urlToImage || 'https://via.placeholder.com/300x150/4CAF50/FFFFFF?text=Sustentabilidade'}" alt="">
                <div class="card-content">
                    <h3>${article.title || 'Sem título'}</h3>
                    <p>${article.description || article.content?.substring(0, 150) || 'Sem descrição'}...</p>
                    <div class="tags">${tags.map(tag => `<span class="tag">${tag}</span>`).join('')}</div>
                    <p><small><strong>Fonte:</strong> ${article.source?.name || 'Desconhecida'}</small></p>
                    <a href="${article.url}" target="_blank">Ler notícia completa</a>
                </div>
            `;
            container.appendChild(card);
        });

        loading.style.display = "none";
        window.loadingNews = false;
        pageNumber++;
    } catch (error) {
        loading.innerText = "Erro ao buscar notícias da NewsAPI.";
        console.error(error);
        window.loadingNews = false;
    }
}

// CHAVES DAS APIs
const NEWSAPI_KEY = "4f42120960b64b70823a94ae72e20a87";

// PALAVRAS-CHAVE PARA FILTRAR
const keywords = [
    "reciclagem", "lixo", "resíduo", "resíduos", 
    "sustentabilidade", "sustentável", 
    "meio ambiente", "ambiental", 
    "coleta seletiva", "descarte consciente"
];

// TEMAS PARA CONSULTA
const temas = [
    "reciclagem",
    "lixo resíduos",
    "sustentabilidade",
    "meio ambiente",
    "coleta seletiva",
    "descarte consciente"
];

// PAGINAÇÃO PARA SCROLL INFINITO
let pageNumber = 1;
let loadingNews = false;
const articlesLoaded = new Set(); // para evitar duplicatas

async function getNewsFromNewsAPI() {
    if (loadingNews) return;
    loadingNews = true;

    const container = document.getElementById("news-container");
    const loading = document.getElementById("loading");
    loading.style.display = "block";
    loading.innerText = "Carregando notícias...";

    try {
        const temasConsulta = temaSelecionado === "todos" 
    ? temas 
    : [temaSelecionado];

const promises = temasConsulta.map(async (tema) => {

            const url = `https://newsapi.org/v2/everything?` +
                        `q=${encodeURIComponent(tema)}&` +
                        `language=pt&` +
                        `pageSize=10&` +
                        `page=${pageNumber}&` +
                        `sortBy=relevancy&` +
                        `apiKey=${NEWSAPI_KEY}`;
            const resp = await fetch(url);
            const data = await resp.json();
            return data.articles || [];
        });

        const results = await Promise.all(promises);
        const allArticles = results.flat();

        // REMOVER DUPLICATAS E FILTRAR POR PALAVRAS-CHAVE
        const filteredArticles = allArticles.filter(article => {
            if (!article.url || articlesLoaded.has(article.url)) return false;
            const texto = (article.title + ' ' + article.description).toLowerCase();
            if (!keywords.some(k => texto.includes(k))) return false;
            articlesLoaded.add(article.url);
            return true;
        });

        if (filteredArticles.length === 0 && pageNumber === 1) {
            container.innerHTML = "<p>Nenhuma notícia encontrada sobre lixo, reciclagem ou sustentabilidade.</p>";
        }

        // MOSTRAR CARDS
        filteredArticles.forEach(article => {
            const tags = identificarTags(article);
            const card = document.createElement("div");
            card.className = "card";
            card.innerHTML = `
                <img src="${article.urlToImage || 'https://via.placeholder.com/300x150/4CAF50/FFFFFF?text=Sustentabilidade'}" alt="">
                <div class="card-content">
                    <h3>${article.title || 'Sem título'}</h3>
                    <p>${article.description || article.content?.substring(0, 150) || 'Sem descrição'}...</p>
                    <div class="tags">${tags.map(tag => `<span class="tag">${tag}</span>`).join('')}</div>
                    <p><small><strong>Fonte:</strong> ${article.source?.name || 'Desconhecida'}</small></p>
                    <a href="${article.url}" target="_blank">Ler notícia completa</a>
                </div>
            `;
            container.appendChild(card);
        });

        loading.style.display = "none";
        loadingNews = false;
        pageNumber++; // Próxima página para scroll infinito

    } catch (error) {
        loading.innerText = "Erro ao buscar notícias da NewsAPI.";
        console.error(error);
        loadingNews = false;
    }
}

// SCROLL INFINITO
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
        getNewsFromNewsAPI();
    }
});

// FUNÇÃO DE TAGS
function identificarTags(article) {
    const texto = (article.title + ' ' + article.description).toLowerCase();
    const tags = [];
    if (texto.includes('reciclagem') || texto.includes('reciclar')) tags.push('♻️ Reciclagem');
    if (texto.includes('lixo') || texto.includes('resíduo')) tags.push('🗑️ Lixo');
    if (texto.includes('sustentabilidade') || texto.includes('sustentável')) tags.push('🌱 Sustentabilidade');
    if (texto.includes('meio ambiente') || texto.includes('ambiental')) tags.push('🌍 Meio Ambiente');
    return tags;
}

// INICIALIZA
getNewsFromNewsAPI();




</script>
   <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
  <!-- * *                               SB Forms JS                               * *-->
  <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
  <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('residuosDropdown');
    const menu = document.getElementById('residuosMenu');
    const arrow = document.getElementById('arrowIcon');

    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        menu.classList.toggle('show');
        arrow.classList.toggle('rotate');
    });

    // Fecha ao clicar fora
    document.addEventListener('click', function(e) {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('show');
            arrow.classList.remove('rotate');
        }
    });
    });
    </script>
  <script src='https://www.noupe.com/embed/019c1f3c8294716498b8dd58d2621fb0c78b.js'></script>
 

</body>
</html>
