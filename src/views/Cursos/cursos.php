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