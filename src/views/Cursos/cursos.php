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

function BuscarCursosRecomendados($idPessoa)
{
    include "../../services/conexão_com_banco.php";

    // Consulta SQL para obter o CPF do candidato com base no ID da pessoa
    $sqlCPF = "SELECT c.CPF
                FROM Tb_Pessoas p
                JOIN Tb_Candidato c ON p.Id_Pessoas = c.Tb_Pessoas_Id
                WHERE p.Id_Pessoas = ?";

    // Preparar a declaração
    $stmtCPF = $_con->prepare($sqlCPF);

    // Verificar se a preparação da declaração foi bem-sucedida
    if ($stmtCPF === false) {
        die("Erro na preparação da declaração: " . $_con->error);
    }

    // Executar a declaração com o ID da pessoa como parâmetro
    $stmtCPF->bind_param("i", $idPessoa);
    $stmtCPF->execute();

    // Obter o resultado da consulta
    $resultadoCPF = $stmtCPF->get_result();

    // Verificar se a consulta retornou alguma linha
    if ($resultadoCPF->num_rows > 0) {
        // Obter o CPF do candidato
        $linhaCPF = $resultadoCPF->fetch_assoc();
        $cpfCandidato = $linhaCPF['CPF'];

        // Consulta SQL para obter os IDs dos cursos recomendados para o candidato
        $sqlCursos = "SELECT Tb_Cursos_Id FROM Tb_Recomendacoes WHERE Tb_Candidato_CPF = ?";

        // Preparar a declaração
        $stmtCursos = $_con->prepare($sqlCursos);

        // Verificar se a preparação da declaração foi bem-sucedida
        if ($stmtCursos === false) {
            die("Erro na preparação da declaração: " . $_con->error);
        }

        // Executar a declaração com o CPF do candidato como parâmetro
        $stmtCursos->bind_param("s", $cpfCandidato);
        $stmtCursos->execute();

        // Obter o resultado da consulta
        $resultadoCursos = $stmtCursos->get_result();

        // Inicializar um array para armazenar os IDs dos cursos recomendados
        $idsCursosRecomendados = array();

        // Verificar se a consulta retornou alguma linha
        if ($resultadoCursos->num_rows > 0) {
            // Iterar sobre os resultados e armazenar os IDs dos cursos recomendados
            while ($linhaCursos = $resultadoCursos->fetch_assoc()) {
                $idsCursosRecomendados[] = $linhaCursos['Tb_Cursos_Id'];
            }
        } else {
            // Se a tabela de recomendações estiver vazia, recomendar cursos aleatórios
            $sqlAleatorio = "SELECT Id_Cursos FROM Tb_Cursos ORDER BY RAND() LIMIT 5"; // Seleciona 5 cursos aleatórios
            $resultadoAleatorio = $_con->query($sqlAleatorio);

            // Verificar se a consulta retornou alguma linha
            if ($resultadoAleatorio->num_rows > 0) {
                // Iterar sobre os resultados e armazenar os IDs dos cursos recomendados
                while ($linhaAleatoria = $resultadoAleatorio->fetch_assoc()) {
                    $idsCursosRecomendados[] = $linhaAleatoria['Id_Cursos'];
                }
            }
        }

        // Consulta SQL para obter os detalhes dos cursos recomendados
        $sqlDetalhes = "SELECT * FROM Tb_Cursos WHERE Id_Cursos IN (" . implode(",", $idsCursosRecomendados) . ")";

        // Executar a consulta
        $resultadoDetalhes = $_con->query($sqlDetalhes);

        // Verificar se a consulta retornou alguma linha
        if ($resultadoDetalhes->num_rows > 0) {
            // Inicializar um array para armazenar os cursos recomendados
            $cursos = array();

            // Iterar sobre os resultados e armazenar os cursos recomendados
            while ($linhaDetalhes = $resultadoDetalhes->fetch_assoc()) {
                $cursos[] = $linhaDetalhes;
            }

            // Retornar os cursos recomendados
            return $cursos;
        } else {
            // Se nenhum curso recomendado for encontrado, retornar um array vazio
            return array();
        }
    } else {
        // Se não houver candidato encontrado, retornar um array vazio
        return array();
    }
}

function preencherHTMLComCursosRecomendados($idPessoa)
{

    $cursos = BuscarCursosRecomendados($idPessoa);
    // Iterando sobre os cursos e preenchendo o HTML
    foreach ($cursos as $curso) {
        echo '<a class="cursoLink" href="' . $curso['Link'] . '" target="_blank" onclick="registrarClique(' . $curso['Id_Cursos'] . ')" title="' . $curso['Nome_do_Curso'] . '">';
        echo '<article class="curso">';
        echo '<div class="divLogoCurso">';
        echo '<img src="' . $curso['URL_da_Imagem'] . '">';
        echo '</div>';
        echo '<section>';
        echo '<p id="empresaCurso">' . $curso['Categoria'] . '</p>';
        echo '<h3>' . substr($curso['Nome_do_Curso'], 0, 15) . '...</h3>';
        echo '<div class="divFlexSpace">';
        echo '<p>' . substr($curso['Nivel'], 0, 28) . '</p>';
        echo '<p>' . $curso['Duração'] . '</p>';
        echo '</div>';
        echo '</section>';
        echo '</article>';
        echo '</a>';
    }
}

// Função para preencher o HTML com os cursos
function preencherHTMLComCursos($categoria)
{
    // Buscando os cursos do banco de dados
    $cursos = buscarCursosDoBanco($categoria);

    // Iterando sobre os cursos e preenchendo o HTML
    foreach ($cursos as $curso) {
        echo '<a class="cursoLink" href="' . $curso['Link'] . '" target="_blank" onclick="registrarClique(' . $curso['Id_Cursos'] . ')" title="' . $curso['Nome_do_Curso'] . '">';
        echo '<article class="curso">';
        echo '<div class="divLogoCurso">';
        echo '<img src="' . $curso['URL_da_Imagem'] . '">';
        echo '</div>';
        echo '<section>';
        echo '<p id="empresaCurso">' . $curso['Categoria'] . '</p>';
        echo '<h3>' . substr($curso['Nome_do_Curso'], 0, 15) . '...</h3>';
        echo '<div class="divFlexSpace">';
        echo '<p>' . substr($curso['Nivel'], 0, 28) . '</p>';
        echo '<p>' . $curso['Duração'] . '</p>';
        echo '</div>';
        echo '</section>';
        echo '</article>';
        echo '</a>';
    }
}

// Consulta para obter a data da última atualização
$query = "SELECT Ultima_Atualizacao FROM Tb_Cursos ORDER BY Ultima_Atualizacao DESC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && $result['Ultima_Atualizacao'] !== null) {
    $ultimaAtualizacao = $result['Ultima_Atualizacao'];
    $dataAtual = new DateTime(); // Data atual
    $dataUltimaAtualizacao = new DateTime($ultimaAtualizacao); // Data da última atualização

    // Calcular a diferença de dias
    $diff = $dataAtual->diff($dataUltimaAtualizacao)->days;

    if ($diff >= 15) {
        // Calcular a data limite (16 dias atrás a partir da data atual)
        $dataAtual = new DateTime(); // Data atual
        $dataLimite = $dataAtual->modify('-16 days')->format('Y-m-d');

        // Consulta para excluir registros cuja última atualização foi há 16 dias ou mais
        $deleteQuery = "DELETE FROM Tb_Cursos WHERE Ultima_Atualizacao <= :dataLimite";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':dataLimite', $dataLimite);
        $deleteStmt->execute();

        // Verificar se algum registro foi excluído
        $registrosExcluidos = $deleteStmt->rowCount();
        if ($registrosExcluidos > 0) {
            //$message = "Registros com última atualização há 16 dias ou mais foram excluídos.";
            include '../../services/Obter_cursos.php';
        } else {
            //$message = "Nenhum registro com última atualização há 16 dias ou mais foi encontrado.";
        }
    } else {
        //$message = "A última atualização foi há $diff dias, que é menor que 15 dias.";
    }
} else {
    include '../../services/Obter_cursos.php';
}

//echo json_encode(['message' => $message]);


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
        <?php
        //*  Se tiver um idPessoa mostra esse navbar 
        if ($idPessoa) {
            echo '<nav>';
            echo '    <input type="checkbox" id="check"> ';
            echo '    <label for="check" class="menuBtn">';
            echo '        <img src="../../../imagens/menu.svg">';
            echo '    </label> ';
            echo '<a href="../HomeCandidato/homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> ';
            echo '<button class="btnModo"><img src="../../../imagens/moon.svg"></button>';
            echo '<ul>';
            echo '    <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>';
            echo '    <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>';
            echo '    <li><a href="../Cursos/cursos.php">Cursos</a></li>';
            echo '    <li><a href="../PerfilCandidato/perfilCandidato.php?id=' . $idPessoa . '">Perfil</a></li>';
            echo '</ul>';
            echo '</nav>';
        } else {
            echo '<nav>';
            echo '    <input type="checkbox" id="check"> ';
            echo '    <label for="check" class="menuBtn">';
            echo '        <img src="../../../imagens/menu.svg">';
            echo '    </label> ';
            echo '<a href="../../../index.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> ';
            echo '<ul>';
            echo '    <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>';
            echo '    <li><a href="../Login/login.html">Testes</a></li>';
            echo '    <li><a href="../Cursos/cursos.php">Cursos</a></li>';
            echo '    <li><a href="../Login/login.html">Login</a></li>';
            echo '</ul>';
            echo '</nav>';
        }
        ?>
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
                </div>
                <div id="sugestoes" class="sugestoes" style="display: none;">
                    <ul id="sugestoesLista"></ul>
                </div>
            </div>
            <div id="divGridCursos" class="divGridCursos">
                <!--Aqui vai os cursos da pesquisa-->
            </div>
        </div>
    </div>

    <?php
    // Verifica se o ID da pessoa está definido
    if (isset($idPessoa)) {
        // Preenchendo a seção de cursos Recomendados
        echo '<div class="divCarrossel">';
        echo '<div class="divTitulo">';
        echo '<h2>Cursos Recomendados</h2>';
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
        preencherHTMLComCursosRecomendados($idPessoa);
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

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
    <a href="../PoliticadePrivacidade/PoliticadePrivacidade.html">Política de Privacidade</a>
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
        // Evento de entrada no campo de pesquisa para exibir sugestões e executar busca
        document.getElementById('inputPesquisa').addEventListener('input', function () {
            exibirSugestoes();
            executarBusca();
        });

        // Função para exibir sugestões
        function exibirSugestoes() {
            const query = document.getElementById('inputPesquisa').value;
            fetch(`obter_sugestoes.php?query=${encodeURIComponent(query)}`)



                .then(response => response.json())
                .then(data => {
                    const sugestoesLista = document.getElementById('sugestoesLista');
                    sugestoesLista.innerHTML = ''; // Limpar sugestões anteriores
                    data.forEach(sugestao => {
                        const sugestaoItem = document.createElement('li');
                        sugestaoItem.textContent = sugestao;
                        sugestaoItem.classList.add('sugestao-item');
                        sugestoesLista.appendChild(sugestaoItem);
                    });
                    document.getElementById('sugestoes').style.display = 'block'; // Exibir sugestões
                })
                .catch(error => console.error('Erro ao obter sugestões:', error));
        }

        // Evento de clique nas sugestões para selecionar
        document.getElementById('sugestoesLista').addEventListener('click', function (event) {
            if (event.target.classList.contains('sugestao-item')) {
                const sugestaoSelecionada = event.target.textContent;
                document.getElementById('inputPesquisa').value = sugestaoSelecionada;
                executarBusca(); // Executar busca ao selecionar sugestão
                ocultarSugestoes(); // Ocultar sugestões após selecionar
            }
        });

        // Evento de teclado para navegar e selecionar sugestões
        document.getElementById('inputPesquisa').addEventListener('keydown', function (event) {
            const sugestoes = document.querySelectorAll('.sugestao-item');
            const index = Array.from(sugestoes).findIndex(sugestao => sugestao.classList.contains('selecionada'));
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                event.preventDefault(); // Evitar comportamento padrão de scroll
                let newIndex = index;
                if (event.key === 'ArrowUp') {
                    newIndex = index > 0 ? index - 1 : sugestoes.length - 1;
                } else {
                    newIndex = index < sugestoes.length - 1 ? index + 1 : 0;
                }
                sugestoes.forEach(sugestao => sugestao.classList.remove('selecionada'));
                sugestoes[newIndex].classList.add('selecionada');
            } else if (event.key === 'Enter') {
                const sugestaoSelecionada = sugestoes[index].textContent;
                document.getElementById('inputPesquisa').value = sugestaoSelecionada;
                executarBusca(); // Executar busca ao selecionar sugestão
                ocultarSugestoes(); // Ocultar sugestões após selecionar
            }
        });

        // Função para ocultar as sugestões
        function ocultarSugestoes() {
            document.getElementById('sugestoes').style.display = 'none';
        }

        // Função para executar a busca em tempo real
        function executarBusca() {
            const query = document.getElementById('inputPesquisa').value;
            const novoTema = $("body").hasClass("noturno") ? "claro" : "noturno"; // Obter o valor do tema
            if (query.length > 0) {
                fetch(`buscar_cursos.php?titulo=${encodeURIComponent(query)}&tema=${novoTema}`) // Incluir o tema na URL da busca
                    .then(response => response.text())
                    .then(data => {
                        console.table({
                            Query: query,
                            tema: novoTema
                        });
                        document.getElementById('divGridCursos').innerHTML = data;
                    })
                    .catch(error => console.error('Erro:', error));
            } else {
                // Limpar os resultados se a consulta estiver vazia
                document.getElementById('divGridCursos').innerHTML = '';
            }
        }

        // Evento de clique no botão de pesquisa
        document.getElementById('searchButton').addEventListener('click', function () {
            executarBusca();
        });

        // Executar a busca quando a página carregar pela primeira vez
        executarBusca();
    </script>
    <script>
        // Função para atualizar o estilo da página e notificar o servidor
        function atualizarEstiloENotificarServidor(novoTema) {
            var idPessoa = <?php echo $idPessoa; ?>;
            $.ajax({
                url: "../../services/Temas/atualizar_tema.php",
                method: "POST",
                data: { tema: novoTema, idPessoa: idPessoa },
                success: function () {
                    console.log("Tema atualizado com sucesso");
                    atualizarEstiloPagina(novoTema); // Atualiza o estilo da página
                },
                error: function (error) {
                    console.error("Erro ao salvar o tema:", error);
                }
            });
        }

        // Função para atualizar o estilo da página
        function atualizarEstiloPagina(novoTema) {
            if (novoTema === "noturno") {
                $("body").addClass("noturno");
                Noturno(); // Ativa funcionalidades noturnas
            } else {
                $("body").removeClass("noturno");
                Claro(); // Ativa funcionalidades claras
            }
            executarBusca(); // Executa a busca após atualizar o estilo da página
        }

        // Evento de clique no botão de alternância de tema
        $(".btnModo").click(function () {
            var novoTema = $("body").hasClass("noturno") ? "claro" : "noturno";
            atualizarEstiloENotificarServidor(novoTema);
        });

        document.getElementById('inputPesquisa').addEventListener('input', function () {
            executarBusca();
        });

        // Função para executar a busca em tempo real
        function executarBusca() {
            const query = document.getElementById('inputPesquisa').value;
            const idPessoa = <?php echo $idPessoa; ?>;
            const novoTema = $("body").hasClass("noturno") ? "claro" : "noturno"; // Obter o valor do tema
            if (query.length > 0) {
                fetch(`buscar_cursos.php?titulo=${encodeURIComponent(query)}&idPessoa=${idPessoa}&tema=${novoTema}`) // Inclua o tema na URL da busca
                    .then(response => response.text())
                    .then(data => {
                        console.table({
                            Query: query,
                            ID_Pessoa: idPessoa,
                            tema: novoTema
                        });
                        document.getElementById('divGridCursos').innerHTML = data;
                    })
                    .catch(error => console.error('Erro:', error));
            } else {
                // Limpa os resultados se a consulta estiver vazia
                document.getElementById('divGridCursos').innerHTML = '';
            }
        }
        // Evento de clique no botão de pesquisa
        document.getElementById('searchButton').addEventListener('click', function () {
            executarBusca();
        });

        // Executar a busca quando a página carregar pela primeira vez
        executarBusca();
    </script>

    <script>
        document.getElementById('checkUpdate').addEventListener('click', function () {
            fetch('consulta.php')
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        });
    </script>
    <script>
        function registrarClique(cursoID) {
            // Fazendo uma requisição AJAX para registrar o clique
            fetch('registrar_clique.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ cursoID: cursoID })
            })
                .then(response => response.text()) // Alterado para .text() para depuração
                .then(data => {
                    console.log('Resposta do servidor:', data); // Log da resposta do servidor
                    let jsonData;
                    try {
                        jsonData = JSON.parse(data);
                    } catch (e) {
                        console.error('Erro ao fazer parse do JSON:', e);
                        return;
                    }
                    if (jsonData.success) {
                        console.log('Clique registrado com sucesso');
                    } else {
                        console.error('Erro ao registrar clique');
                    }
                })
                .catch((error) => {
                    console.error('Erro:', error);
                });
        }
    </script>
</body>

</html>