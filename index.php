<?php
include "src/services/conexão_com_banco.php";

session_start();

session_unset();
session_destroy();

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
    <script>
        function limparLocalStorageComExcecao() {
            const chavesParaLimpar = ['tipos', 'area', 'apenasVagasAbertas', 'termoPesquisa'];

            chavesParaLimpar.forEach(function (chave) {
                localStorage.removeItem(chave);
            });

            const areaPadrao = 'Todas'; // Valor padrão para "area"
            if (!localStorage.getItem('area')) {
                localStorage.setItem('area', areaPadrao);
            }

            console.log("LocalStorage após limpeza:", localStorage);
        }

        window.addEventListener('load', limparLocalStorageComExcecao);
    </script>
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
            <li><a href="src/views/TodasVagas/todasVagas.php">Vagas</a></li>
            <!--Colocar  link depois   -->
            <li><a href="#">Testes</a></li>
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
            <a class="btnLeftSlider">
                <img src="src/assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider">
                <img src="src/assets/images/icones_diversos/rightSlider.svg">
            </a>
            <div class="carrosselBox">
                <?php
                // Loop para exibir as vagas restantes no carrossel
                while ($row = $result->fetch_assoc()) {
                    // Consulta para contar o número de inscritos para esta vaga
                    $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
                    $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
                    $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]);
                    $stmt_inscricoes->execute();
                    $result_inscricoes = $stmt_inscricoes->get_result();

                    if ($result_inscricoes === false) {
                        echo "Erro na consulta de contagem de inscrições: " . $_con->error;
                        exit;
                    }

                    $row_inscricoes = $result_inscricoes->fetch_assoc();
                    $total_inscricoes = $row_inscricoes['total_inscricoes'];
                    $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";

                    // Obter o CNPJ da empresa associada
                    $sql_obter_cnpj = "SELECT Tb_Empresa_CNPJ FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";
                    $stmt_cnpj = $_con->prepare($sql_obter_cnpj);
                    $stmt_cnpj->bind_param("i", $row["Id_Anuncios"]);
                    $stmt_cnpj->execute();
                    $result_cnpj = $stmt_cnpj->get_result();

                    if ($result_cnpj === false || $result_cnpj->num_rows == 0) {
                        echo "Erro ao obter o CNPJ da empresa ou CNPJ não encontrado.";
                        exit;
                    }

                    $row_cnpj = $result_cnpj->fetch_assoc();
                    $cnpj = $row_cnpj['Tb_Empresa_CNPJ'];

                    // Obter o nome da empresa pelo CNPJ
                    $sql_obter_nome_empresa = "SELECT Nome_da_Empresa FROM Tb_Empresa WHERE CNPJ = ?";
                    $stmt_nome_empresa = $_con->prepare($sql_obter_nome_empresa);
                    $stmt_nome_empresa->bind_param("s", $cnpj);
                    $stmt_nome_empresa->execute();
                    $result_nome_empresa = $stmt_nome_empresa->get_result();

                    if ($result_nome_empresa === false || $result_nome_empresa->num_rows == 0) {
                        echo "Erro ao obter o nome da empresa ou empresa não encontrada.";
                        exit;
                    }

                    $row_nome_empresa = $result_nome_empresa->fetch_assoc();
                    $nome_empresa = isset($row_nome_empresa['Nome_da_Empresa']) ? $row_nome_empresa['Nome_da_Empresa'] : 'Confidencial';

                    echo '<a class="postLink" href="src/views/Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
                    echo '<article class="post">';
                    echo '<div class="divAcessos">';
                    echo '<img src="imagens/people.svg"></img>';
                    echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
                    echo '</div>';
                    echo '<header>';
                    echo '<img src="imagens/' . determinarImagemCategoria($row["Categoria"]) . '.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    echo '</header>';
                    echo '<section>';
                    echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';

                    // Exibir o nome da empresa
                    echo '<p class="empresaVaga">Empresa: ' . $nome_empresa . '</p>'; // Corrigida a atribuição
                
                    echo '</section>';

                    if ($row['Status'] == 'Aberto') {
                        echo '<h4 class="statusVaga" style="color:green">Aberto</h4>';
                        echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                    } else {
                        echo '<h4 class="statusVaga" style="color:red">' . $row['Status'] . '</h4>';
                        echo '<p class="dataVaga">' . $row['Data_de_Termino'] . '</p>'; // Certifique-se de que esta variável está definida
                    }

                    echo '</article>';
                    echo '</a>';
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    $sql = "SELECT q.Id_Questionario, q.Nome, q.Area, e.Nome_da_Empresa 
    FROM Tb_Questionarios q
    INNER JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
    INNER JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
    INNER JOIN Tb_Pessoas p ON e.Tb_Pessoas_Id = p.Id_Pessoas
    LIMIT 4"; // Limitar a 4 questionários para não ficar muitos questionários poluindo a tela    
    $result = $_con->query($sql);
    ?>
    <div class="divCommon">
        <div class="divTitulo">
            <h2>Testes de Habilidades</h2>
            <p>Avalie seu conhecimento e ganhe destaque nos processos seletivos!</p>
        </div>
        <div class="container">
            <div class="flexTestes">
                <?php
                $counter = 0; // Inicializa um contador para controlar os questionários por linha
                if ($result->num_rows > 0) {
                    // Loop através dos resultados da consulta
                    while ($row = $result->fetch_assoc()) {
                        $idQuestionario = $row['Id_Questionario'];
                        $nome = $row['Nome'];
                        $area = $row['Area'];
                        $nomeEmpresa = $row['Nome_da_Empresa'];

                        // Verifica se é hora de começar uma nova linha
                        if ($counter % 2 == 0) {
                            echo '<div class="gridTestes" style="margin-right: 12px;">';
                        }
                        ?>
                        <a class="testeLink" href="src/views/PreparaTeste/preparaTeste.php?id=<?php echo $idQuestionario ?>">
                            <article class="teste">
                                <div class="divAcessos">
                                    <img src="imagens/people.svg"></img>
                                    <small class="qntdAcessos">800</small>
                                </div>
                                <img src="imagens/excel.svg"></img>
                                <div class="divDetalhesTeste">
                                    <div>
                                        <?php
                                        $limite = 21;

                                        // Obtenha o nome e limite-o se necessário
                                        if (strlen($nome) > $limite) {
                                            $nome_limitado = mb_substr($nome, 0, $limite) . '...'; // Cortar o texto e adicionar reticências
                                        } else {
                                            $nome_limitado = $nome; // Se não ultrapassar o limite, use o nome inteiro
                                        }

                                        // Exibir o nome limitado
                                        echo '<p class="nomeTeste">' . $nome_limitado . '</p>';
                                        ?>
                                        <small class="autorTeste"><?php echo $nomeEmpresa; ?></small>
                                        <div class="divCompetencias">
                                            <small>Competências: </small>
                                            <small class="competenciasTeste"><?php echo $area; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </a>
                        <?php
                        // Verifica se é hora de fechar a linha
                        if ($counter % 2 == 1) {
                            echo '</div>'; // Fecha a div "gridTestes"
                        }
                        $counter++;
                    }
                    // Verifica se a última linha precisa ser fechada
                    if ($counter % 2 != 0) {
                        echo '</div>'; // Fecha a div "gridTestes"
                    }
                    ?>
            </div>
            <a href="src/views/Login/login.html"><button>Ver mais</button></a>
            <?php
            } else { // Se não houver resultados, exibe uma mensagem
                echo "<p style='text-align:center; margin:0 auto;'>Nenhum questionário encontrado.</p>";
            }
            ?>
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
                        <section class="opiniao">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
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
                        <section class="opiniao">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
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
                        <section class="opiniao">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
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
                        <section class="opiniao">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt aspernatur temporibu
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

</body>

</html>