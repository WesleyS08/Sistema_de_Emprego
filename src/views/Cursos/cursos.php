<?php

include "../../services/conexão_com_banco.php";

// Função para buscar os cursos do banco de dados
function buscarCursosDoBanco($categoria)
{
    include "../../services/conexão_com_banco.php";
    // Consulta SQL para buscar os cursos da categoria específica
    $sql = "SELECT * FROM Tb_Cursos WHERE Categoria = ?";



    // Preparando a declaração
    $stmt = $_con->prepare($sql);

    // Verificando se a preparação da declaração falhou
    if ($stmt === false) {
        die("Erro na preparação da declaração: " . $_con->error);
    }

    // Ligando parâmetros
    $stmt->bind_param("s", $categoria);

    // Executando a declaração
    $stmt->execute();

    // Obtendo resultado
    $result = $stmt->get_result();

    // Array para armazenar os cursos
    $cursos = [];

    // Iterando sobre os resultados
    while ($row = $result->fetch_assoc()) {
        $cursos[] = $row;
    }

    // Fechando a declaração e a conexão
    $stmt->close();
    $_con->close();

    // Retornando os cursos
    return $cursos;
}

// Função para preencher o HTML com os cursos
function preencherHTMLComCursos($categoria)
{
    // Buscando os cursos do banco de dados
    $cursos = buscarCursosDoBanco($categoria);

    // Iterando sobre os cursos e preenchendo o HTML
    foreach ($cursos as $curso) {
        echo '<a class="cursoLink" href="' . $curso['Link'] . '">';
        echo '<article class="curso">';
        echo '<div class="divLogoCurso">';
        echo '<img src="' . $curso['URL_da_Imagem'] . '">';
        echo '</div>';
        echo '<section>';
        echo '<p id="empresaCurso">' . $curso['Categoria'] . '</p>';
        echo '<h3>' . substr($curso['Nome_do_Curso'], 0, 18) . '...</h3>';
        echo '<div class="divFlexSpace">';
        echo '<p>' . $curso['Nivel'] . '</p>';
        echo '<p>' . $curso['Duração'] . '</p>';
        echo '</div>';
        echo '</section>';
        echo '</article>';
        echo '</a>';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/todosStyle.css">
    <link rel="stylesheet" type="text/css" href="cursos.css">
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
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">C</h1>
        <i class="pisca"></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input class="inputPesquisa" placeholder="Pesquisar" type="text">
                    <button class="searchButton">
                        <lord-icon src="https://cdn.lordicon.com/kkvxgpti.json" trigger="hover" colors="primary:#f5f5f5"
                            style="width:36px;height:36px">
                        </lord-icon>
                    </button>
                </div>
            </div>
            <div class="divGridCursos">
                <!--Aqui vai os cursos da pesquisa-->
            </div>
        </div>
    </div>
  
    <?php
    // Preenchendo a seção de cursos Fatec
    echo '<div class="divCarrossel">';
    echo '<div class="divTitulo">';
    echo '<h2>Cursos Fatec</h2>';
    echo '</div>';
    echo '<div class="container">';
    echo '<a class="btnLeftSlider" id="leftCursosGratuitos">';
    echo '<img src="../../assets/images/icones_diversos/leftSlider.svg">';
    echo '</a>';
    echo '<a class="btnRightSlider" id="rightCursosGratuitos">';
    echo '<img src="../../assets/images/icones_diversos/rightSlider.svg">';
    echo '</a>';
    echo '<div class="carrosselBox" id="cursosGratuitos">';
    // Preenchendo com os cursos das Fatec
    preencherHTMLComCursos("Fatecs");
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Preenchendo a seção de cursos Etec
    echo '<div class="divCarrossel">';
    echo '<div class="divTitulo">';
    echo '<h2>Cursos Etec</h2>';
    echo '</div>';
    echo '<div class="container">';
    echo '<a class="btnLeftSlider" id="leftCursosPagos">';
    echo '<img src="../../assets/images/icones_diversos/leftSlider.svg">';
    echo '</a>';
    echo '<a class="btnRightSlider" id="rightCursosPagos">';
    echo '<img src="../../assets/images/icones_diversos/rightSlider.svg">';
    echo '</a>';
    echo '<div class="carrosselBox" id="cursosPagos">';
    // Preenchendo com os cursos das Etec
    preencherHTMLComCursos("Etecs");
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Preenchendo a seção de outros cursos
    echo '<div class="divCarrossel">';
    echo '<div class="divTitulo">';
    echo '<h2>Outros Cursos</h2>';
    echo '</div>';
    echo '<div class="container">';
    echo '<a class="btnLeftSlider" id="leftCertificacoes">';
    echo '<img src="../../assets/images/icones_diversos/leftSlider.svg">';
    echo '</a>';
    echo '<a class="btnRightSlider" id="rightCertificacoes">';
    echo '<img src="../../assets/images/icones_diversos/rightSlider.svg">';
    echo '</a>';
    echo '<div class="carrosselBox" id="certificacoes">';
    // Preenchendo com outros cursos
    preencherHTMLComCursos("Outros");
    echo '</div>';
    echo '</div>';
    echo '</div>';
    ?>
    <div class="divCommon">
        <div class="divAviso">
            <div class="divFlex">
                <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/usownftb.json" trigger="hover" stroke="bold"
                    state="hover-oscillate" colors="primary:#000000,secondary:#e88c30" style="width:40px;height:40px">
                </lord-icon>
                <h2>Aviso</h2>
                <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/usownftb.json" trigger="hover" stroke="bold"
                    state="hover-oscillate" colors="primary:#000000,secondary:#e88c30" style="width:40px;height:40px">
                </lord-icon>
            </div>
            <div class="divTextoAviso">
                <p>
                    Este site indica cursos e certificações profissionalizantes a fim de enriquecer e aprimorar o
                    conhecimento do usuário. Não recebemos pagamento ao divulgar as empresas acima.</p>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a href="../NossoContato/nossoContato.html">Nosso contato</a>
        <a href="../AvalieNos/avalieNos.html">Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="carrosselCursosGratuitos.js"></script>
    <script src="carrosselCursosPagos.js"></script>
    <script src="carrosselCertificacoes.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="tituloDigitavel.js"></script>
    <script src="../../../modoNoturno.js"></script>
</body>

</html>