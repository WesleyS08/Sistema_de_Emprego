<?php

include "../../services/conexão_com_banco.php";

session_start();

// ?  Inicia a variável do email vazia
$emailUsuario = '';

// * Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario'])) {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario'])) {
    $emailUsuario = $_SESSION['google_session'];
} else {

}

// Primeira Consulta para obter o ID da pessoa caso esteja logado 
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

// Verifique se a preparação da declaração foi bem-sucedida
if ($stmt) {
    $stmt->bind_param("s", $emailUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifique se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Obtenha o ID da pessoa
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];
    }
    //! Feche a declaração
    $stmt->close();
}

// Segunda Consulta para selecionar o tema que salvo no banco de dados
$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

// Verifique se a preparação foi bem-sucedida
if ($stmt) {
    $stmt->bind_param('i', $idPessoa);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $row = $result->fetch_assoc();
        if ($row && isset($row['Tema'])) {
            $tema = $row['Tema'];
        } else {
            $tema = null;
        }
    } else {
        $tema = null;
    }
} else {
    // ! Arrumar questão de tratamento de Erros !! 
    die("Erro ao preparar a query.");
}

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
        <?php if ($idPessoa): ?>
            <a href="../../homeCandidato/homeCandidato.php"><img id="logo"
                    src="../../assets/images/logos_empresa/logo_sias.png"></a>
            <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
            <ul>
                <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
                <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
                <li><a href="../Cursos/cursos.php">Cursos</a></li>
                <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
            </ul>
        <?php endif; ?>
        <a href="../../../index.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <ul>
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
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
                    <input id="inputPesquisa" class="inputPesquisa" placeholder="Pesquisar" type="text">
                    <button id="searchButton" class="searchButton">
                        <lord-icon src="https://cdn.lordicon.com/kkvxgpti.json" trigger="hover" colors="primary:#f5f5f5"
                            style="width:36px;height:36px">
                        </lord-icon>
                    </button>
                </div>
            </div>
            <div id="divGridCursos" class="divGridCursos">
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
    preencherHTMLComCursos("Bradesco");
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
                    Este site indica cursos e certificações profissionalizantes a fim de enriquecer e aprimorar
                    o
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="carrosselCursosGratuitos.js"></script>
    <script src="carrosselCursosPagos.js"></script>
    <script src="carrosselCertificacoes.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="tituloDigitavel.js"></script>
    <script>
        var temaDoBancoDeDados = "<?php echo $tema; ?>";
    </script>
    <script src="../../../modoNoturno.js"></script>
    <script>
        document.getElementById('inputPesquisa').addEventListener('input', function () {
            const query = document.getElementById('inputPesquisa').value;
            // Only proceed if there's a query
            if (query.length > 0) {
                fetch('buscar_cursos.php?titulo=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        const divGridCursos = document.getElementById('divGridCursos');
                        divGridCursos.innerHTML = '';
                        if (data.length === 0) {
                            divGridCursos.innerHTML = '<p>Nenhum curso encontrado</p>';
                        } else {
                            data.forEach(curso => {
                                const cursoDiv = document.createElement('div');
                                cursoDiv.className = "cursoLink";
                 
                                cursoDiv.innerHTML = `
                                <a class="cursoLink" href="${curso.link}">
                                    <article class="curso">
                                        <div class="divLogoCurso">
                                            <img src="${curso.url_imagem}" alt="Imagem do curso ${curso.nome}" style="width: 100px; height: 100px;">
                                        </div>
                                        <section>
                                            <p id="empresaCurso">${curso.categoria}</p>
                                            <h3>${curso.nome.substring(0, 18)}...</h3>
                                            <div class="divFlexSpace">
                                                <p>${curso.nivel}</p>
                                                <p>Duração: ${curso.duracao}</p>
                                            </div>
                                        </section>
                                    </article>
                                </a>
                            `;
                                divGridCursos.appendChild(cursoDiv);
                            });
                        }
                    })
                    .catch(error => console.error('Erro:', error));
            } else {
                // Clear the results if the query is empty
                document.getElementById('divGridCursos').innerHTML = '';
            }
        });
    </script>
    <script>
        var idPessoa = <?php echo $idPessoa; ?>;
        $(".btnModo").click(function () {
            var novoTema = $("body").hasClass("noturno") ? "claro" : "noturno";
            $.ajax({
                url: "../../services/Temas/atualizar_tema.php",
                method: "POST",
                data: { tema: novoTema, idPessoa: idPessoa },
                success: function () {
                    console.log("Tema atualizado com sucesso");
                },
                error: function (error) {
                    console.error("Erro ao salvar o tema:", error);
                }
            });
            if (novoTema === "noturno") {
                $("body").addClass("noturno");
                Noturno();
            } else {
                $("body").removeClass("noturno");
                Claro(); 
            }
        });
    </script>
</body>
</html>