<?php
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
function determinarImagemCategoria($categoria) {
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
    <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="imagens/menu.svg">
        </label>
        <label id="logo">SIAS</label>
        <ul>            
            <li><a href="#">Vagas</a></li>
            <li><a href="#">Pesquisar</a></li>
            <li><a href="#">Cursos</a></li>
            <li><a href="#">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTitle">
        <div class="divTituloDigitavel">
            <h1 id="tituloAutomatico">S</h1>
            <i></i>
        </div>
        <p>Construa sua carreira onde habilidades geram oportunidades.<br>Seu emprego dos sonhos está a um clique de distância!</p>
        <button onclick="location.href='src/views/Cadastro/cadastro.html'">Inscreva-se</button>
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
                    echo '<a class="postLink">';
                    echo '<article class="post">';
                    echo '<div class="divAcessos">';
                    echo '<img src="imagens/people.svg"></img>';
                    echo '<small class="qntdAcessos">28</small>';
                    echo '</div>';
                    echo '<header>';
                    echo '<img src="imagens/' . determinarImagemCategoria($row["Categoria"]) . '.svg">'; // Determina a imagem com base na categoria do trabalho
                    echo '<label class="tipoVaga" style="color:#191970">' . $row["Categoria"] . '</label>';
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
            <button>Ver mais</button>
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
                        <section class = "likes">
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
                        <section class = "likes">
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
                        <section class = "likes">
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
                        <section class = "likes">
                            <img src="imagens/like.svg">
                            <label class="qntdLikes">1000</label>
                        </section>
                    </article>
                </a>
                <button id="trocaImg" onclick="location.href='Login/login.html'">Faça parte!<br><img class="runningGuy" src="imagens/runningWhite.svg"></button>
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
<script src="tituloDigitavel.js"></script>
<script src="carrossel.js"></script>
<script src="trocaImagem.js"></script>
</body>
</html>