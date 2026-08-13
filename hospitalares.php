<?php
session_start();
 
if (!isset($_SESSION["id_usuario"])) {
    header('Location: login.php?erro=4');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>ReciMap-hospitalares</title>
        <link rel="icon" type="recimap777" href="./IMG/recimap777.png" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>

<style>
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
    <!-- Navigation-->
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
        <!-- Masthead-->
        <header class="masthead">
            <div class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center">
                <div class="d-flex justify-content-center">
                    <div class="text-center">
                        <h1 class="mx-auto my-0 text-uppercase">Hospitalares</h1>
                        <h3 class="text-white-50 mx-auto mt-2 mb-5">Errar no descarte custa caro. Faça certo, faça agora!</h3>
                    </div>
                </div>
            </div>
        </header>
        <!-- About-->
    <main>
        <!-- Projects-->
        <section class="projects-section bg-light" id="projects">
            <div class="container px-4 px-lg-5">
                <!-- Featured Project Row-->
                <div class="row gx-0 mb-4 mb-lg-5 align-items-center">
                    <div class="col-xl-8 col-lg-7"><img class="img-fluid mb-3 mb-lg-0" src="./img/hospitalar.jpg" alt="..." /></div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="featured-text text-center text-lg-left">
                            <h4>Lixos Hospitalares</h4>
                            <p class="text-black-100 mb-0">É o conjunto de rejeitos produzidos em estabelecimentos de saúde, como hospitais, clínicas veterinárias, pronto-socorros, unidades de pronto atendimento, consultórios odontológicos, centros de pesquisas e laboratórios farmacêuticos.

Substâncias radioativas e inflamáveis, materiais biológicos, peças anatômicas, seringas, luvas, agulhas e itens plásticos usados em procedimentos médicos são alguns exemplos desse tipo de resíduo.</p>
                        </div>
                    </div>
                </div>
                <!-- Project One Row-->
                <div class="row gx-0 mb-5 mb-lg-0 justify-content-center">
                    <div class="col-lg-6"><img class="img-fluid" src="./img/hospitalar7.jpg" alt="..." /></div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h4 class="text-white">Quais os riscos do lixo hospitalar?</h4>
                                    <p class="mb-0 text-white-50">Os resíduos de serviços de saúde podem incluir tanto lixo comum, como materiais de escritório, quanto rejeitos perigosos, como perfurocortantes e materiais biológicos, químicos e radioativos.

Quando descartados incorretamente, esses resíduos representam riscos à saúde humana e ao meio ambiente, podendo causar infecções, intoxicações e doenças graves.

Entre suas principais características estão a toxicidade, patogenicidade, carcinogenicidade, teratogenicidade e mutagenicidade.

Além disso, o descarte inadequado pode contaminar solo, água e ar, afetando ecossistemas e gerando um ciclo contínuo de contaminação.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row-->
                <div class="row gx-0 justify-content-center">
                    <div class="col-lg-6"><img class="img-fluid" src="./img/hospitalar5.jpg" alt="..." /></div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <h4 class="text-white">Como descartar lixo hospitalar?</h4>
                                    <p class="mb-0 text-white-50">É necessário observar os recipientes adequados para descartar itens infectantes, químicos e perfurocortantes, evitando acidentes de trabalho ou ocorrências junto aos pacientes e acompanhantes.

Eles ficarão nesses recipientes até que a equipe de limpeza os encaminhe para a segregação, acondicionamento e identificação, iniciando o protocolo de descarte.

No entanto, ainda há grande desconhecimento sobre o manejo dos rejeitos, que, muitas vezes, não são destinados corretamente.

Segundo levantamento da Abrelpe (Associação Brasileira de Empresas de Limpeza Pública e Resíduos Especiais), apenas 30% do lixo infectante vai para incineração – que é o destino final apropriado para esses materiais.

Enquanto outros quase 30% são enviados a aterros sanitários e mais de 15% vão para os lixões, sem qualquer cuidado para evitar que contaminem o solo e água.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Signup-->
        <section class="signup-section" id="signup">
            <div class="footer">
                
            </div>
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <div class="col-md-10 col-lg-8 mx-auto text-center">
                        <i class="far fa-paper-plane fa-2x mb-2 text-white"></i>
                        <h2 class="text-white mb-5">Nossos Contatos!</h2>
                        <!-- * * * * * * * * * * * * * * *-->
                        <!-- * * SB Forms Contact Form * *-->
                        <!-- * * * * * * * * * * * * * * *-->
                        <!-- This form is pre-integrated with SB Forms.-->
                        <!-- To make this form functional, sign up at-->
                        <!-- https://startbootstrap.com/solution/contact-forms-->
                        <!-- to get an API token!-->
                        <form class="form-signup" id="contactForm" data-sb-form-api-token="API_TOKEN">
                            <!-- Email address input-->
                            <div class="row input-group-newsletter">
                               
                            </div>
                            <div class="invalid-feedback mt-2" data-sb-feedback="emailAddress:required">An email is required.</div>
                            <div class="invalid-feedback mt-2" data-sb-feedback="emailAddress:email">Email is not valid.</div>
                            <!-- Submit success message-->
                            <!---->
                            <!-- This is what your users will see when the form-->
                            <!-- has successfully submitted-->
                            <div class="d-none" id="submitSuccessMessage">
                                <div class="text-center mb-3 mt-2 text-white">
                                    <div class="fw-bolder">Form submission successful!</div>
                                    To activate this form, sign up at
                                    <br />
                                    <a href="https://startbootstrap.com/solution/contact-forms">https://startbootstrap.com/solution/contact-forms</a>
                                </div>
                            </div>
                            <!-- Submit error message-->
                            <!---->
                            <!-- This is what your users will see when there is-->
                            <!-- an error submitting the form-->
                            <div class="d-none" id="submitErrorMessage"><div class="text-center text-danger mb-3 mt-2">Error sending message!</div></div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact-->
        <section class="contact-section bg-black">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-map-marked-alt text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Endereço</h4>
                                <hr class="my-4 mx-auto" />
                                <div class="small text-black-50">R. Saigiro Nakamura, 400 - Vila Industrial, São José dos Campos - SP, 12220-280</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-envelope text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Email</h4>
                                <hr class="my-4 mx-auto" />
                                 <div class="small text-black-50"><a href="https://www.sp.senac.br/senac-sao-jose-dos-campos">sjcampos@sp.senac.br</a>
            </div>
            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Telefone</h4>
                                <hr class="my-4 mx-auto" />
                                <div class="small text-black-50">(12) 2134-9000</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="social d-flex justify-content-center">
                    <a class="mx-2" href="#!"><i class="fab fa-twitter"></i></a>
                    <a class="mx-2" href="https://www.facebook.com/senacsjcampos/?locale=pt_BR"><i class="fab fa-facebook-f"></i></a>
                     <a class="mx-2" href="https://www.instagram.com/senacsaojosedoscampos/"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </section>
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
        <!-- Footer-->
        <footer class="footer bg-black small text-center text-white-50"><div class="container px-4 px-lg-5">Copyright &copy; Todos Direitos Reservados 2025</div></footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
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
