<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado como candidato
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato') {
    // Se estiver autenticado como candidato
    $autenticadoComoCandidato = true;
    $emailUsuario = '';

    // Definir o e-mail do usuário com base no tipo de sessão
    if (isset($_SESSION['email_session'])) {
        $emailUsuario = $_SESSION['email_session'];
    } elseif (isset($_SESSION['google_session'])) {
        $emailUsuario = $_SESSION['google_session'];
    }    
} else {
    // Se não estiver autenticado como candidato, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}

// Consulta SQL para verificar dados do candidato
$sql_verificar_candidato = "SELECT * FROM Tb_Candidato
    JOIN Tb_Pessoas ON Tb_Candidato.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
    WHERE Tb_Pessoas.Email = ?";

$stmt_verificar_candidato = $_con->prepare($sql_verificar_candidato);
$stmt_verificar_candidato->bind_param("s", $emailUsuario);
$stmt_verificar_candidato->execute();
$result_verificar_candidato = $stmt_verificar_candidato->get_result();

// Verificar se a consulta teve sucesso
if ($result_verificar_candidato === false) {
    // Tratar o erro, se necessário
    echo "Erro na consulta: " . $_con->error;
    exit;
}

// Obter dados do candidato
$candidato = $result_verificar_candidato->fetch_assoc();

// Verificar se o candidato foi encontrado
if (!$candidato) {
    echo "Candidato não encontrado";
    exit;
}

// Dados do candidato
$nomeUsuario = $candidato['Nome'];
$emailUsuario = $candidato['Email'];

// Limpar variáveis de sessão não utilizadas
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';


// Consulta SQL para verificar empresas e puxar as vagas
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

$stmt_verificar_candidato->close();
$_con->close();

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
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>
<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="homeCandidato.html"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> 
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button> 
        <ul>            
            <li><a href="../TodasVagas/todasVagas.html">Vagas</a></li>
            <li><a href="#">Testes</a></li>
            <li><a href="#">Cursos</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTitle">
        <div class="divCentraliza">
            <div>
                <div class="divTituloDigitavel">
                    <h1 id="tituloAutomatico">B</h1>
                    <i></i>
                </div>
                <p>Encontre vagas que combinam com você!<br>Clique e explore as oportunidades.</p>
                <button onclick="location.href=''">Ver todas as vagas</button>
            </div>
        </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Minhas vagas</h2>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftMinhasVagas"><</a>
            <a class="btnRightSlider" id="rightMinhasVagas">></a> 
                <div class="carrosselBox">
                    <?php
                    // Loop para exibir as vagas restantes no carrossel
                    while ($row = $result->fetch_assoc()) {
                        echo '<a class="postLink" href="../Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
                        echo '<article class="post">';
                        echo '<div class="divAcessos">';
                        echo '<img src="../../../imagens/people.svg"></img>';
                        echo '<small class="qntdAcessos">28</small>';
                        echo '</div>';
                        echo '<header>';
                        echo '<img src="../../../imagens/' . determinarImagemCategoria($row["Categoria"]) . '.svg">'; // Determina a imagem com base na categoria do trabalho
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
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Testes de Habilidades</h2>
            <p>Realize testes para ganhar pontos e se destacar em processos seletivos!</p>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftTestes"><</a>
            <a class="btnRightSlider" id="rightTestes">></a> 
            <div class="carrosselBox" id="carrosselTestes">
                <a class="testeCarrosselLink" href="../PreparaTeste/preparaTeste.html">
                    <article class="testeCarrossel">
                        <div class="divAcessos">
                            <img src="../../../imagens/people.svg"></img>
                            <small class="qntdAcessos">800</small>
                        </div>
                        <img src="../../../imagens/excel.svg"></img>
                        <div class="divDetalhesTeste">
                            <div>                                
                                <p class="nomeTeste">Excel Básico</p>
                                <small class="autorTeste">Por Jefferson Evangelista</small><br>
                                <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                            </div>
                        </div>
                    </article>
                </a>
                <a class="testeCarrosselLink">
                    <article class="testeCarrossel">
                        <div class="divAcessos">
                            <img src="../../../imagens/people.svg"></img>
                            <small class="qntdAcessos">800</small>
                        </div>
                        <img src="../../../imagens/figma.svg"></img>
                        <div class="divDetalhesTeste">
                            <div>                                
                                <p class="nomeTeste">Figma Intermediário</p>
                                <small class="autorTeste">Por Jefferson Evangelista</small><br>
                                <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                            </div>
                        </div>
                    </article>
                </a>
                <a class="testeCarrosselLink">
                    <article class="testeCarrossel">
                        <div class="divAcessos">
                            <img src="../../../imagens/people.svg"></img>
                            <small class="qntdAcessos">800</small>
                        </div>
                        <img src="../../../imagens/word.svg"></img>
                        <div class="divDetalhesTeste">
                            <div>                                
                                <p class="nomeTeste">Word Avançado</p>
                                <small class="autorTeste">Por Jefferson Evangelista</small><br>
                                <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                            </div>
                        </div>
                    </article>
                </a>
                <a class="testeCarrosselLink">
                    <article class="testeCarrossel">
                        <div class="divAcessos">
                            <img src="../../../imagens/people.svg"></img>
                            <small class="qntdAcessos">800</small>
                        </div>
                        <img src="../../../imagens/python.svg"></img>
                        <div class="divDetalhesTeste">
                            <div>                                
                                <p class="nomeTeste">Python Básico</p>
                                <small class="autorTeste">Por Jefferson Evangelista</small><br>
                                <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                            </div>
                        </div>
                    </article>
                </a>
            </div> 
            <div class="divBtnVerMais">
                <a href="src/views/todosTestes/todosTestes.html" class="btnVerMais"><button>Ver mais</button></a>
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
                        <img src="../../../imagens/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/php.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../../imagens/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/html.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/css.svg">
                    </div>
                    
        
                    <div class="slide">
                        <img src="../../../imagens/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/php.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../../imagens/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/html.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/css.svg">
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
<script src="carrosselUltimosAnuncios.js"></script>
<script src="carrosselMinhasVagas.js"></script>
<script src="carrosselTestes.js"></script>
</body>
</html>