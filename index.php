<?php
session_start();

// Verificar se a variável de sessão 'email_session' ou 'google_session' está definida
if (isset($_SESSION['email_session']) || isset($_SESSION['google_session'])) {
    // Destruir a sessão atual
    session_destroy();
    // Encerrar o script após destruir a sessão
    exit;
}


include "src/services/conexão_com_banco.php";

$sql_verificar_empresa = "SELECT * FROM Tb_Anuncios 
JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ";

$stmt = $_con->prepare($sql_verificar_empresa);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se a consulta teve sucesso
if ($result === false) {
    // Tratar o erro, se necessário
    echo "Erro na consulta: " . $_con->error;
    exit;
}
// Função para determinar a imagem com base na categoria do trabalho
function determinarImagemCategoria($categoria)
{
    switch ($categoria) {
        case 'Estágio':
            return 'estagio';
        case 'CLT':
            return 'clt';
        case 'PJ':
            return 'pj';
        case 'Jovem Aprendiz':
            return 'estagio';
        // Adicione mais casos conforme necessário
        default:
            return 'default'; // Retorna uma imagem padrão caso não haja correspondência
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="src/assets/styles/homeStyles.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="imagens/menu.svg">
        </label>
        <a href="index.html"><img id="logo" src="src/assets/images/logos_empresa/logo_sias.png"></a>
        <ul>
            <li><a href="src/views/TodasVagas/todasVagas.html">Vagas</a></li>
            <li><a href="src/views/PreparaTeste/preparaTeste.html">Testes</a></li>
            <li><a href="#">Cursos</a></li>
            <li><a href="src/views/Login/login.html">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTitle">
        <div class="divCentraliza" id="divCentralizaComIcones">
            <div class="divIconeFlutuante">
                <lord-icon src="https://cdn.lordicon.com/vdjwmfqs.json" trigger="in" delay="500" state="in-assignment"
                    colors="primary:#f5f5f5" style="width:170px;height:170px">
                </lord-icon>
            </div>
            <div>
                <div class="divTituloDigitavel">
                    <h1 id="tituloAutomatico">S</h1>
                    <i></i>
                </div>
                <p>Construa sua carreira onde habilidades geram oportunidades.<br>Seu emprego dos sonhos está a um
                    clique de distância!</p>
                <button onclick="location.href='src/views/Cadastro/cadastro.html'">Inscreva-se</button>
            </div>
            <div class="divIconeFlutuante">
                <lord-icon src="https://cdn.lordicon.com/ppyvfomi.json" trigger="in" delay="500" state="in-work"
                    colors="primary:#f5f5f5" style="width:170px;height:170px">
                </lord-icon>
            </div>
        </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Últimos anúncios</h2>
        </div>
        <div class="container">
            <a class="btnLeftSlider"></a>
            <a class="btnRightSlider">></a>
            <div class="carrosselBox">
                <?php
                // Loop para exibir as vagas restantes no carrossel
                while ($row = $result->fetch_assoc()) {
                    // Consulta para contar o número de inscritos para esta vaga
                    $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
                    $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
                    $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]); // "i" indica que o parâmetro é um inteiro
                    $stmt_inscricoes->execute();
                    $result_inscricoes = $stmt_inscricoes->get_result();

                    // Verificar se a consulta teve sucesso
                    if ($result_inscricoes === false) {
                        // Tratar o erro, se necessário
                        echo "Erro na consulta de contagem de inscrições: " . $_con->error;
                        exit;
                    }

                    // Obter o resultado da contagem de inscrições
                    $row_inscricoes = $result_inscricoes->fetch_assoc();
                    $total_inscricoes = $row_inscricoes['total_inscricoes'];

                    echo '<a class="postLink" href="src/views/Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
                    echo '<article class="post">';
                    echo '<div class="divAcessos">';
                    echo '<img src="imagens/people.svg"></img>';
                    echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>'; // Exibindo o número total de inscrições
                    echo '</div>';
                    echo '<header>';
                    echo '<img src="imagens/' . determinarImagemCategoria($row["Categoria"]) . '.svg">'; // Determina a imagem com base na categoria do trabalho
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    echo '</header>';
                    echo '<section>';
                    echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';
                    echo '<p class="empresaVaga">' . (isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida") . '</p>';
                    echo '</section>';
                    echo '<p class="statusVaga" style="color: green;">Aberta</p>';
                    $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
                    echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                    echo '</article>';
                    echo '</a>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="divCommon">
        <div class="divTitulo">
            <h2>Testes de Habilidades</h2>
            <p>Avalie seu conhecimento e ganhe destaque nos processos seletivos!</p>
        </div>
        <div class="container">
            <div class="flexTestes">
                <div class="gridTestes" style="margin-right: 12px;">
                    <a class="testeLink">
                        <article class="teste">
                            <div class="divAcessos">
                                <img src="imagens/people.svg"></img>
                                <small class="qntdAcessos">800</small>
                            </div>
                            <img src="imagens/excel.svg"></img>
                            <div class="divDetalhesTeste">
                                <div>
                                    <p class="nomeTeste">Excel Avançado</p>
                                    <small class="autorTeste">Por Jefferson Evangelista</small>
                                    <div class="divCompetencias">
                                        <small>Competências: </small>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </a>
                    <a class="testeLink">
                        <article class="teste">
                            <div class="divAcessos">
                                <img src="imagens/people.svg"></img>
                                <small class="qntdAcessos">800</small>
                            </div>
                            <img src="imagens/figma.svg"></img>
                            <div class="divDetalhesTeste">
                                <div>
                                    <p class="nomeTeste">Figma Básico</p>
                                    <small class="autorTeste">Por Jefferson Evangelista</small>
                                    <div class="divCompetencias">
                                        <small>Competências: </small>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </a>
                </div>
                <div class="gridTestes" style="margin-left: 12px;">
                    <a class="testeLink">
                        <article class="teste">
                            <div class="divAcessos">
                                <img src="imagens/people.svg"></img>
                                <small class="qntdAcessos">800</small>
                            </div>
                            <img src="imagens/word.svg"></img>
                            <div class="divDetalhesTeste">
                                <div>
                                    <p class="nomeTeste">Word Básico</p>
                                    <small class="autorTeste">Por Jefferson Evangelista</small>
                                    <div class="divCompetencias">
                                        <small>Competências: </small>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </a>
                    <a class="testeLink">
                        <article class="teste">
                            <div class="divAcessos">
                                <img src="imagens/people.svg"></img>
                                <small class="qntdAcessos">800</small>
                            </div>
                            <img src="imagens/python.svg"></img>
                            <div class="divDetalhesTeste">
                                <div>
                                    <p class="nomeTeste">Python Intermediário</p>
                                    <small class="autorTeste">Por Jefferson Evangelista</small>
                                    <div class="divCompetencias">
                                        <small>Competências: </small>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </a>
                </div>
            </div>
            <a href="src/views/Login/login.html"><button>Ver mais</button></a>
        </div>
    </div>
    <div class="divCommon">
        <div class="divTitulo">
            <h2>Quem se increve recomenda!</h2>
        </div>
        <div class="container">
            <div class="divComentarios">
                <a class="comentarioLink">
                    <article class="comentario">
                        <header>
                            <div class="divImg"></div>
                            <label class="nomePessoa">Clarice Josefina da Silva Machado</label>
                        </header>
                        <section class="sectionEstrelas">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star.svg">
                            <small class="dataEnvio">13/03/2024</small>
                        </section>
                        <section>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
                        </section>
                        <section class="likes">
                            <img src="imagens/like.svg">
                            <label class="qntdLikes">1000</label>
                        </section>
                    </article>
                </a>
                <a class="comentarioLink">
                    <article class="comentario">
                        <header>
                            <div class="divImg"></div>
                            <label class="nomePessoa">Martinho Lutero da Silva dsdas das</label>
                        </header>
                        <section class="sectionEstrelas">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star.svg">
                            <small class="dataEnvio">13/03/2024</small>
                        </section>
                        <section>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
                        </section>
                        <section class="likes">
                            <img src="imagens/like.svg">
                            <label class="qntdLikes">1000</label>
                        </section>
                    </article>
                </a>
                <a class="comentarioLink">
                    <article class="comentario">
                        <header>
                            <div class="divImg"></div>
                            <label class="nomePessoa">Pedro Borges</label>
                        </header>
                        <section class="sectionEstrelas">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star.svg">
                            <small class="dataEnvio">13/03/2024</small>
                        </section>
                        <section>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
                        </section>
                        <section class="likes">
                            <img src="imagens/like.svg">
                            <label class="qntdLikes">1000</label>
                        </section>
                    </article>
                </a>
                <a class="comentarioLink">
                    <article class="comentario">
                        <header>
                            <div class="divImg"></div>
                            <label class="nomePessoa">Pedro Borges</label>
                        </header>
                        <section class="sectionEstrelas">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star-fill.svg">
                            <img class="estrela" src="imagens/star.svg">
                            <small class="dataEnvio">13/03/2024</small>
                        </section>
                        <section>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
                        </section>
                        <section class="likes">
                            <img src="imagens/like.svg">
                            <label class="qntdLikes">1000</label>
                        </section>
                    </article>
                </a>
                <button id="btnRunningGuy" onclick="location.href='src/views/Login/login.html'">Faça parte!<br>
                    <lord-icon src="https://cdn.lordicon.com/gwvmctbb.json" trigger="hover"
                        colors="primary:#ffffff,secondary:#ffffff" style="width:90px;height:90px">
                    </lord-icon>
                </button>
            </div>
        </div>
    </div>
    <div class="divCommon">
        <div class="divTitulo">
            <h2>Nossos Parceiros</h2>
        </div>
        <div class="container">
            <div class="carrosselInfinito">
                <div class="trilhaCarrossel">
                    <div class="slide">
                        <img src="imagens/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/php.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="imagens/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="imagens/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/html.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/css.svg">
                    </div>


                    <div class="slide">
                        <img src="imagens/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/php.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="imagens/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="imagens/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/html.svg">
                    </div>
                    <div class="slide">
                        <img src="imagens/css.svg">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>

    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="tituloDigitavel.js"></script>
    <script src="carrossel.js"></script>
    <script src="trocaImagem.js"></script>
</body>

</html>