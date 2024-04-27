<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    // Se estiver autenticado com e-mail/senha e for do tipo empresa
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    // Se estiver autenticado com o Google e for do tipo empresa
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
// Consulta para obter o ID da pessoa logada
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

// Verifique se a preparação da declaração foi bem-sucedida
if ($stmt) {
    // Vincule o parâmetro ao placeholder na consulta
    $stmt->bind_param("s", $emailUsuario);

    // Execute a declaração
    $stmt->execute();

    // Obtenha o resultado da consulta
    $result = $stmt->get_result();

    // Verifique se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Obtenha o ID da pessoa
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];

        // Use o ID da pessoa como necessário no restante do seu código
    } else {
        // Se não houver resultados, lide com isso de acordo com sua lógica de aplicativo
    }

    // Feche a declaração
    $stmt->close();
}

// Quartar consulta para selecionar o tema que  a pessoa selecionou 
$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

// Verifique se a preparação foi bem-sucedida
if ($stmt) {
    // Execute a query com o parâmetro
    $stmt->bind_param('i', $idPessoa); // Vincula o parâmetro
    $stmt->execute();

    // Obter resultado usando o método correto
    $result = $stmt->get_result(); // Obtenha o resultado como mysqli_result
    if ($result) {
        $row = $result->fetch_assoc(); // Obter a linha como array associativo
        if ($row && isset($row['Tema'])) {
            $tema = $row['Tema'];
        } else {
            $tema = null; // No caso de não haver resultado
        }
    } else {
        $tema = null; // Se o resultado for nulo
    }
} else {
    die("Erro ao preparar a query.");
}


$sql_verificar_empresa = "SELECT * FROM Tb_Anuncios 
JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
WHERE Tb_Pessoas.Email = ?";

$stmt = $_con->prepare($sql_verificar_empresa);
$stmt->bind_param("s", $emailUsuario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se a consulta teve sucesso
if ($result === false) {
    // Tratar o erro, se necessário
    echo "Erro na consulta: " . $_con->error;
    exit;
}

// Atribuindo os valores das variáveis de sessão
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';

// Segunda consulta para obter o status da vaga
$sql2 = "SELECT V.* 
FROM Tb_Vagas V
JOIN Tb_Empresa E ON V.Tb_Empresa_CNPJ = E.CNPJ
JOIN Tb_Pessoas P ON E.Tb_Pessoas_Id = P.Id_Pessoas
WHERE P.Email = ?";

// Prepare a declaração
$stmt = mysqli_prepare($_con, $sql2);

// Verifique se a preparação da declaração foi bem-sucedida
if ($stmt) {
    // Vincule o parâmetro ao placeholder na consulta
    mysqli_stmt_bind_param($stmt, "s", $email);

    // Substitua $email pelo valor real do email
    $email = $emailUsuario;

    // Execute a declaração
    mysqli_stmt_execute($stmt);

    // Obtenha o resultado da consulta
    $resultVagas = mysqli_stmt_get_result($stmt);

    if ($resultVagas && mysqli_num_rows($resultVagas) > 0) {
        $row = mysqli_fetch_assoc($resultVagas);
        $Status = $row['Status'];
    } else {
        // Defina um valor padrão para $Status se a consulta não retornar resultados
        $Status = '';
    }

    // Feche a declaração
    mysqli_stmt_close($stmt);
} else {
    // Se a preparação da declaração falhar, lide com o erro aqui
    echo "Erro na preparação da declaração: " . mysqli_error($_con);
}
$categoria = isset($row["Categoria"]) ? $row["Categoria"] : "Categoria não definida";
$titulo = isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido";
$descricao = isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida";
$status = isset($row["Status"]) ? $row["Status"] : "Status não definido";

$sql_areas = "
    SELECT DISTINCT Area 
    FROM Tb_Anuncios 
    ORDER BY Area ASC
";

// Preparar e executar a consulta para obter as áreas únicas
$stmt_areas = $_con->prepare($sql_areas);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();

$areas = ["Todas"]; // Adicionar a opção "Todas" ao início do array

if ($result_areas && $result_areas->num_rows > 0) {
    while ($row = $result_areas->fetch_assoc()) {
        $areas[] = $row['Area']; // Adicionar áreas ao array
    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas vagas</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/todosStyle.css">
    <style>
        .sugestoes {
            position: absolute; // Para garantir que as sugestões fiquem abaixo do campo de pesquisa
            border: 1px solid #ccc;
            background-color: #cecece;
            max-height: 150px; // Limitar a altura para evitar que seja muito alto
            overflow-y: auto; // Permitir rolagem se houver muitas sugestões
            z-index: 1000; // Para que fique por cima de outros elementos
        }

        .sugestao-item {
            padding: 8px;
            cursor: pointer; // Indicar que é clicável
        }

        .sugestao-item:hover {
            background-color: #fff; // Destacar ao passar o mouse
        }
    </style>
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="../HomeRecrutador/homeRecrutador.php"><img id="logo"
                src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../CriarVaga/criarVaga.php">Anunciar</a></li>
            <li><a href="../MinhasVagas/minhasVagas.php">Minhas vagas</a></li>
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li> <!--Arrumar esse link  -->
            <li><a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">M</h1>
        <i></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input class="inputPesquisa" placeholder="Pesquisa por Título">
                </div>

                <div id="sugestoes" class="sugestoes" style="display: none;">

                </div>


                <div id="mostraFiltros">
                    <h3>Filtros</h3>
                    <img id="iconeFiltro" src="../../assets/images/icones_diversos/showHidden.svg">
                </div>
                <div class="containerFiltros">
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Área:</label>
                        <select class="selectArea">
                            <?php
                            foreach ($areas as $area) {
                                echo "<option value='$area'>$area</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Tipo:</label>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="jovemAprendiz"
                            value="Jovem Aprendiz" required>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="estagio" value="Estágio" required>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="clt" value="CLT" required>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="pj" value="PJ" required>
                        <label for="jovemAprendiz" class="btnCheckBox" id="btnJovemAprendiz">Jovem Aprendiz</label>
                        <label for="estagio" class="btnCheckBox" id="btnEstagio">Estágio</label>
                        <label for="clt" class="btnCheckBox" id="btnClt">CLT</label>
                        <label for="pj" class="btnCheckBox" id="btnPj">PJ</label>
                    </div>
                    <div class="contentFiltro" id="flexContent">
                        <label class="nomeFiltro" for="apenasVagasAbertas">Apenas vagas abertas:</label>
                        <input type="checkbox" id="apenasVagasAbertas">
                    </div>

                </div>
            </div>

            <div class="divGridVagas">
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

                    // Exibir a vaga e o número de inscritos
                    echo '<a class="postLink" href="../MinhaVaga/minhaVaga.php?id=' . $row["Id_Anuncios"] . '">';
                    echo '<article class="post">';
                    echo '<div class="divAcessos">';
                    echo '<img src="../../../imagens/people.svg"></img>';
                    echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
                    echo '</div>';

                    echo '<header>';
                    switch ($row["Categoria"]) {
                        case "CLT":
                            echo '<img src="../../../imagens/clt.svg">';
                            echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                            break;
                        case "Estágio":
                        case "Jovem Aprendiz": // Caso tenham a mesma aparência visual
                            echo '<img src="../../../imagens/estagio.svg">';
                            echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                            break;
                        case "PJ":
                            echo '<img src="../../../imagens/pj.svg">';
                            echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                            break;
                        default:
                            echo '<label class="tipoVaga">Categoria não definida</label>';
                            break;
                    }
                    echo '</header>';
                    echo '<section>';
                    echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';
                    echo '<p class="empresaVaga">' . (isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida") . '</p>';
                    // Exibir o status da vaga e a data de criação
                    $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
                    $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
                    if ($row['Status'] == 'Aberto') {
                        echo '<p style="color: green;">' . $row['Status'] . '</p>';
                        echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                    } else {
                        echo '<p style="color: red;">' . $row['Status'] . '</p>';
                        echo '<p class="dataVaga">' . $datadeTermino . '</p>';
                    }

                    echo '</section>';
                    echo '</article>';
                    echo '</a>';
                }
                ?>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="checkButtons.js"></script>
    <script src="mostrarFiltros.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="tituloDigitavel.js"></script>
    <script>
        var temaDoBancoDeDados = "<?php echo $tema; ?>";
    </script>
    <script src="../../../modoNoturno.js"></script>
    <script>
        var idPessoa = <?php echo $idPessoa; ?>;

        $(".btnModo").click(function () {
            var novoTema = $("body").hasClass("noturno") ? "claro" : "noturno";


            // Salva o novo tema no banco de dados via AJAX
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
            // Atualiza a classe do body para mudar o tema
            if (novoTema === "noturno") {
                $("body").addClass("noturno");
                Noturno(); // Adicione esta linha para atualizar imediatamente o tema na interface
            } else {
                $("body").removeClass("noturno");
                Claro(); // Adicione esta linha para atualizar imediatamente o tema na interface
            }

        });
    </script>
    <!--================================ Buscar Vagas por filtros ======================================= -->
    <script>
        $(document).ready(function () {
            // Quando houver uma mudança em qualquer filtro
            $('.selectArea, .checkBoxTipo, #apenasVagasAbertas, .inputPesquisa').on('change input', function () {
                aplicarFiltros(); // Chama a função para aplicar filtros
            });

            function aplicarFiltros() {
                // Obter valores dos filtros
                var area = $('.selectArea').val(); // Valor do filtro de área
                var tipos = []; // Armazena valores das checkboxes selecionadas
                $('.checkBoxTipo:checked').each(function () {
                    tipos.push($(this).val()); // Adiciona valores ao array
                });

                var apenasVagasAbertas = $('#apenasVagasAbertas').is(':checked'); // Status do checkbox
                var termoPesquisa = $('.inputPesquisa').val(); // Termo de pesquisa por título

                // Fazer chamada AJAX para buscar vagas com base nos filtros
                $.ajax({
                    url: 'buscar_vagas_filtros.php', // Endereço do endpoint PHP
                    method: 'POST',
                    data: {
                        area: area,
                        tipos: tipos,
                        vagasAbertas: apenasVagasAbertas,
                        termo: termoPesquisa // Enviar o termo de pesquisa por título
                    },
                    success: function (response) {
                        // Atualiza o conteúdo com vagas filtradas
                        $('.divGridVagas').html(response);
                    },
                    error: function () {
                        console.error("Erro ao buscar vagas com filtros.");
                    }
                });
            }
        });

    </script>

    <!--================================ Obter sugestão dos titulos ======================================= -->
    <script>
        $(document).ready(function () {
            $('.inputPesquisa').on('input', function () {
                var searchTerm = $(this).val();

                if (searchTerm.length >= 2) {
                    $.ajax({
                        url: 'obter_sugestoes.php',
                        method: 'POST',
                        data: { termo: searchTerm },
                        success: function (response) {
                            $('#sugestoes').html(response).show();
                        },
                        error: function () {
                            console.error("Erro ao buscar sugestões.");
                        }
                    });
                } else {
                    $('#sugestoes').hide();
                }
            });

            // Adicione um evento de clique para as sugestões
            $(document).on('click', '.sugestao-item', function () {
                var textoSelecionado = $(this).text(); // Texto da sugestão clicada
                $('.inputPesquisa').val(textoSelecionado); // Preenche o campo de pesquisa
                $('#sugestoes').hide(); // Esconder a lista de sugestões
            });
        });

    </script>

    <script>
        $(document).ready(function () {
            // Evento para capturar quando o usuário clica no botão de pesquisa
            $('.searchButton').on('click', function () {
                // Obtém o termo de pesquisa digitado pelo usuário
                var termoPesquisa = $('.inputPesquisa').val();

                // Chama a função para buscar vagas por título, passando o termo de pesquisa como parâmetro
                buscarVagasPorTitulo(termoPesquisa);
            });
        });

        // Função para buscar vagas por título
        function buscarVagasPorTitulo(termoPesquisa) {
            // Faz a chamada AJAX para buscar vagas por título
            $.ajax({
                url: 'buscar_vaga_por_titulo.php', // Endereço do arquivo PHP para buscar vagas por título
                method: 'POST',
                data: {
                    termo: termoPesquisa // Passa o termo como parâmetro
                },
                success: function (response) {
                    // Atualiza o conteúdo com as vagas encontradas por título
                    $('.divGridVagas').html(response);
                },
                error: function () {
                    console.error("Erro ao buscar vagas por título.");
                }
            });
        }

    </script>

</body>

</html>