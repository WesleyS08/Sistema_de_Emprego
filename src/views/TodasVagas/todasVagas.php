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
}

// Primeira Consulta para obter o ID da pessoa caso esteja logado 
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

// Verifique se a preparação da declaração foi bem-sucedida e atribuir a variável 
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
            $tema = null; // No caso de não haver resultado
        }
    } else {
        $tema = null; // Se o resultado for nulo
    }
} else {
    // ! Arrumar questão de tratamento de Erros !! 
    die("Erro ao preparar a query.");
}

// Atribuindo os valores das variáveis da sessão
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';

// Terceira Consulta para obter o status das vagas
$sql2 = "SELECT V.* 
FROM Tb_Vagas V
JOIN Tb_Empresa E ON V.Tb_Empresa_CNPJ = E.CNPJ
JOIN Tb_Pessoas P ON E.Tb_Pessoas_Id = P.Id_Pessoas
WHERE P.Email = ?";

// Prepare a declaração
$stmt = mysqli_prepare($_con, $sql2);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    $email = $emailUsuario;
    mysqli_stmt_execute($stmt);
    $resultVagas = mysqli_stmt_get_result($stmt);

    if ($resultVagas && mysqli_num_rows($resultVagas) > 0) {
        $row = mysqli_fetch_assoc($resultVagas);
        $Status = $row['Status'];
    } else {
        $Status = '';
    }
    //!  Feche a declaração
    mysqli_stmt_close($stmt);
} else {
    // ! Arrumar questão de tratamento de Erros !! 
    echo "Erro na preparação da declaração: " . mysqli_error($_con);
}

// * Atribuições caso algo da vaga não esteja definido no banco de dados 
$categoria = isset($row["Categoria"]) ? $row["Categoria"] : "Categoria não definida";
$titulo = isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido";
$descricao = isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida";
$status = isset($row["Status"]) ? $row["Status"] : "Status não definido";

// Quarta Consulta ao banco de dados para obter as areas 
$sql_areas = "
    SELECT DISTINCT Area 
    FROM Tb_Anuncios 
    ORDER BY Area ASC";

$stmt_areas = $_con->prepare($sql_areas);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();

$areas = ["Todas"]; // Adicionar a opção "Todas" ao início do array

if ($result_areas && $result_areas->num_rows > 0) {
    while ($row = $result_areas->fetch_assoc()) {
        $areas[] = $row['Area'];
    }
}

// Quinta Consulta para obter os anúncios
$sql_verificar_empresa = "SELECT * FROM Tb_Anuncios 
JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas";

$stmt = $_con->prepare($sql_verificar_empresa);
if ($stmt === false) {
    // Se o prepare falhou, exiba um erro (mas cuidado ao exibir detalhes em produção)
    echo "Erro ao preparar a consulta: " . $_con->error;
    exit;
}

//!  Executar a consulta
$stmt->execute();
// Obter os resultados
$result = $stmt->get_result();

// Verificar se a consulta teve sucesso
if ($result === false) {
    // ! Arrumar questão de tratamento de Erros !! 
    echo "Erro ao executar a consulta: " . $_con->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vagas</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/todosStyle.css">
    <style>
        .sugestoes {
            position: absolute;
            border: 1px solid #ccc;
            background-color: #cecece;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sugestao-item {
            padding: 8px;
            cursor: pointer;
        }

        .sugestao-item:hover {
            background-color: #fff;
        }

        .selecionada {
            background-color: #ffffff;
            /* Cor de fundo da sugestão selecionada */
            color: #333;
            /* Cor do texto da sugestão selecionada */
        }
    </style>
</head>

<body>
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
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">V</h1>
        <i class="pisca"></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input id="inputPesquisa" class="inputPesquisa" type="text" placeholder="Pesquisa por Título">
                </div>
                <div id="sugestoes" class="sugestoes" style="display: none;">
                    <ul id="sugestoesLista"></ul>
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
                            } ?>
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
                    $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]);
                    $stmt_inscricoes->execute();
                    $result_inscricoes = $stmt_inscricoes->get_result();

                    // Verificar se a consulta teve sucesso
                    if ($result_inscricoes === false) {
                        // ! Arrumar questão de tratamento de Erros !! 
                        echo "Erro na consulta de contagem de inscrições: " . $_con->error;
                        exit;
                    }

                    // Obter o resultado da contagem de inscrições
                    $row_inscricoes = $result_inscricoes->fetch_assoc();
                    $total_inscricoes = $row_inscricoes['total_inscricoes'];

                    echo '<a class="postLink" href="../Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
                    echo '<article class="post">';
                    echo '<div class="divAcessos">';
                    echo '<img src="../../assets/images/icones_diversos/people.svg"></img>';
                    echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
                    echo '</div>';
                    echo '<header>';
                    switch ($row["Categoria"]) {
                        case "CLT":
                            echo '<img src="../../../imagens/clt.svg">';
                            echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                            break;
                        case "Estágio":
                        case "Jovem Aprendiz":
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
                    echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? (strlen($row["Titulo"]) > 14 ? substr($row["Titulo"], 0, 20) . '...' : $row["Titulo"]) : "Título não definido") . '</h3>';
                    // Se não houver empresa, definir um valor padrão
                    if (empty($nome_empresa)) {
                        $nome_empresa = 'Confidencial';
                    }
                    echo '<p class="empresaVaga"> Empresa:' . $nome_empresa . '</p>';
                    // Exibir o status da vaga e a data de criação
                    $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
                    $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
                    if ($row['Status'] == 'Aberto') {
                        echo '<h4 class="statusVaga" style="color:green">Aberto</h4>';
                        echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                    } else {
                        echo '<h4 class="statusVaga" style="color:red">' . $row['Status'] . '</h4>';
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
        <a href="../PoliticadePrivacidade/PoliticadePrivacidade.html">Política de Privacidade</a>
        <a href="../NossoContato/nossoContato.html">Nosso contato</a>
        <a href="../AvalieNos/avalieNos.html">Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="checkButtons.js"></script>
    <script src="mostrarFiltros.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="tituloDigitavel.js"></script>


    <script>
        $(document).ready(function () {
            $('#inputPesquisa').blur(function () {
                var pesquisa = $(this).val();
                var idPessoa = <?php echo json_encode($idPessoa); ?>;

                // Verifica se o idPessoa está definido
                if (!idPessoa) {
                    console.warn("ID da pessoa não definido. A função não será executada.");
                    return;  // Sai da função se o idPessoa não estiver definido
                }

                $.ajax({
                    type: 'POST',
                    url: 'salvar_pesquisa.php',
                    data: {
                        pesquisa: pesquisa,
                        idPessoa: idPessoa
                    },
                    success: function (response) {
                        try {
                            var cursos = JSON.parse(response);
                            console.log(cursos);
                            // Aqui você pode adicionar código para exibir os cursos recomendados na interface do usuário
                        } catch (e) {
                            console.error("Erro ao processar a resposta: " + e);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erro ao enviar a solicitação AJAX: " + error);
                    }
                });
            });
        });
    </script>

    <!--================================ Parte do tema noturno ======================================= -->

    <!-- Atribui o tema salvo no banco de dados a essa variável e passa ela pro modo noturno  -->
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

                    // Atualizar a imagem dentro da divAcessos
                    var novoIcone = novoTema === "noturno" ? "peopleWhite.svg" : "people.svg";
                    $(".divAcessos img").attr("src", "../../assets/images/icones_diversos/" + novoIcone);
                },
                error: function (error) {
                    console.error("Erro ao salvar o tema:", error);
                }
            });
            // Atualiza a classe do body para mudar o tema
            if (novoTema === "noturno") {
                $("body").addClass("noturno");
                Noturno();
            } else {
                $("body").removeClass("noturno");
                Claro();
            }
        });

    </script>



    <!--================================ Buscar Vagas por filtros ======================================= -->
    <script>
        $(document).ready(function () {
            var idPessoa = <?php echo json_encode($idPessoa); ?>; // Armazena o ID da pessoa
            var tema = <?php echo json_encode($tema); ?>; // Armazena o tema
            var pageIdentifier = window.location.pathname; // Identificador único da página

            // Função para aplicar o modo noturno
            function aplicarModoNoturno() {
                if (tema === "noturno") {
                    $("body").addClass("noturno");
                    Noturno(); // Se necessário, chame aqui a função que configura o modo noturno
                } else {
                    $("body").removeClass("noturno");
                    Claro(); // Se necessário, chame aqui a função que configura o modo claro
                }
            }

            // Aplicar o modo noturno ao carregar a página
            aplicarModoNoturno();

            // Função para aplicar e salvar filtros
            function aplicarFiltros() {
                // Obter valores dos filtros do localStorage usando a chave única
                var area = localStorage.getItem(pageIdentifier + '_area') || 'Todas'; // Definir "Todas" como valor padrão
                var tipos = JSON.parse(localStorage.getItem(pageIdentifier + '_tipos')) || [];
                var apenasVagasAbertas = JSON.parse(localStorage.getItem(pageIdentifier + '_apenasVagasAbertas'));
                var termoPesquisa = localStorage.getItem(pageIdentifier + '_termoPesquisa') || "";

                // Aplicar os valores do localStorage aos elementos da página
                $('.selectArea').val(area);
                $('.checkBoxTipo').each(function () {
                    $(this).prop('checked', tipos.includes($(this).val()));
                });
                $('#apenasVagasAbertas').prop('checked', apenasVagasAbertas);
                $('.inputPesquisa').val(termoPesquisa);

                // Filtros a serem enviados na requisição AJAX
                var filtros = {
                    area: area,
                    tipos: tipos,
                    vagasAbertas: apenasVagasAbertas,
                    termo: termoPesquisa,
                    idPessoa: idPessoa, // Certifique-se de que 'idPessoa' está definido em algum lugar no seu script
                    tema: tema
                };

                // Log dos filtros no console para depuração
                console.table(filtros);

                // Chamada AJAX para buscar vagas com base nos filtros
                $.ajax({
                    url: 'buscar_vagas_filtros.php',
                    method: 'POST',
                    data: filtros,
                    success: function (response) {
                        // Inserir a resposta no DOM
                        $('.divGridVagas').html(response);

                        // Adicionar a classe 'noturno' se o tema for noturno
                        if (tema === 'noturno') {
                            $('.divGridVagas').addClass('noturno');
                        }
                    },
                    error: function () {
                        console.error("Erro ao buscar vagas com filtros.");
                    }
                });
            }

            // Salvar filtros ao mudar qualquer filtro
            function salvarFiltros() {
                // Obter valores dos filtros
                var area = $('.selectArea').val();
                var tipos = [];
                $('.checkBoxTipo:checked').each(function () {
                    tipos.push($(this).val());
                });
                var apenasVagasAbertas = $('#apenasVagasAbertas').is(':checked');
                var termoPesquisa = $('.inputPesquisa').val();

                // Salvar no localStorage com a chave única
                localStorage.setItem(pageIdentifier + '_area', area);
                localStorage.setItem(pageIdentifier + '_tipos', JSON.stringify(tipos));
                localStorage.setItem(pageIdentifier + '_apenasVagasAbertas', apenasVagasAbertas);
                localStorage.setItem(pageIdentifier + '_termoPesquisa', termoPesquisa);
            }

            // Aplicar filtros ao carregar a página
            aplicarFiltros();

            // Eventos para salvar filtros quando eles são alterados
            $('.selectArea, .checkBoxTipo, #apenasVagasAbertas, .inputPesquisa').on('change input', function () {
                salvarFiltros();
                aplicarFiltros();
            });

            // Função para buscar vagas por título com área e idPessoa
            function buscarVagasPorTitulo(termoPesquisa, area, idPessoa) {
                $.ajax({
                    url: 'buscar_vaga_por_titulo.php',
                    method: 'POST',
                    data: {
                        termo: termoPesquisa,
                        area: area,
                        idPessoa: idPessoa // Inclui o ID da pessoa
                    },
                    success: function (response) {
                        $('.divGridVagas').html(response).addClass('noturno');
                    },
                    error: function () {
                        console.error("Erro ao buscar vagas por título.");
                    }
                });
            }

            // Gestão da pesquisa e sugestões
            var areaAtual = $('.selectArea').val();
            var termoAnterior = localStorage.getItem(pageIdentifier + '_termoPesquisa');
            if (termoAnterior) {
                $('.inputPesquisa').val(termoAnterior);
                buscarVagasPorTitulo(termoAnterior, areaAtual, idPessoa); // Passa a área e o ID da pessoa corretos
            }

            $('.inputPesquisa').on('input', function () {
                var searchTerm = $(this).val();
                localStorage.setItem(pageIdentifier + '_termoPesquisa', searchTerm);

                if (searchTerm.length >= 2) {
                    $.ajax({
                        url: 'obter_sugestoes.php',
                        method: 'POST',
                        data: {
                            termo: searchTerm,
                            area: areaAtual,
                            idPessoa: idPessoa // Inclui o ID da pessoa
                        },
                        success: function (response) {
                            var sugestoes = JSON.parse(response);
                            var sugestoesLista = $('#sugestoesLista');
                            sugestoesLista.empty();

                            sugestoes.forEach(function (sugestao) {
                                sugestoesLista.append('<li class="sugestao-item">' + sugestao + '</li>');
                            });

                            // Exibir o contêiner de sugestões após adicionar as sugestões
                            $('#sugestoes').css('display', 'block');
                        },
                        error: function () {
                            console.error("Erro ao buscar sugestões.");
                        }
                    });
                } else {
                    $('.inputPesquisa').on('blur', function () {
                        setTimeout(function () {
                            $('#sugestoes').hide();
                        }, 5000); // Oculta as sugestões após 5 segundos de perder o foco
                    });
                }
            });

            $(document).on('click', '.sugestao-item', function () {
                var textoSelecionado = $(this).text();
                $('.inputPesquisa').val(textoSelecionado);
                $('#sugestoes').hide();
                localStorage.setItem(pageIdentifier + '_termoPesquisa', textoSelecionado);
                buscarVagasPorTitulo(textoSelecionado, areaAtual, idPessoa); // Passa a área e o ID da pessoa corretos
            });

            $(document).on('keydown', function (e) {
                var sugestoes = $('.sugestao-item');
                var index = sugestoes.index($('.selecionada'));

                if (e.which === 38) { // Seta para cima
                    e.preventDefault(); // Evita que a página faça scroll
                    index = (index === -1) ? sugestoes.length - 1 : (index === 0) ? sugestoes.length - 1 : index - 1;
                    sugestoes.removeClass('selecionada');
                    sugestoes.eq(index).addClass('selecionada');
                } else if (e.which === 40) { // Seta para baixo
                    e.preventDefault(); // Evita que a página faça scroll
                    index = (index === sugestoes.length - 1) ? -1 : index + 1;
                    sugestoes.removeClass('selecionada');
                    sugestoes.eq(index).addClass('selecionada');
                } else if (e.which === 13 || e.which === 9) { // Enter, Tab 
                    if (index === -1) { // Se nenhuma sugestão estiver selecionada
                        var primeiraSugestao = sugestoes.first().text();
                        $('.inputPesquisa').val(primeiraSugestao);
                    } else {
                        var textoSelecionado = $('.selecionada').text();
                        $('.inputPesquisa').val(textoSelecionado);
                    }
                    $('#sugestoes').hide();
                    localStorage.setItem(pageIdentifier + '_termoPesquisa', $('.inputPesquisa').val());
                    buscarVagasPorTitulo($('.inputPesquisa').val(), areaAtual, idPessoa);
                }
            });

            // Função para alternar o estado do botão e salvar no localStorage
            function toggleButtonState(buttonId) {
                const button = document.querySelector(`#${buttonId}`);
                let isActive = localStorage.getItem(pageIdentifier + '_' + buttonId + 'State') === 'true';
                // Alternar o estado do botão
                isActive = !isActive;
                localStorage.setItem(pageIdentifier + '_' + buttonId + 'State', isActive); // Salvar no localStorage
                if (isActive) {
                    button.style.backgroundColor = "var(--laranja)";
                    button.style.border = "1px solid var(--laranja)";
                    button.style.color = "whitesmoke";
                } else {
                    button.style = "initial";
                }
            }

            // Função para restaurar os estados dos botões ao carregar a página
            function restaurarEstadosDosBotoes() {
                const buttonIds = ["btnJovemAprendiz", "btnEstagio", "btnClt", "btnPj"];
                buttonIds.forEach(buttonId => {
                    const button = document.querySelector(`#${buttonId}`);
                    if (button) {
                        const isActive = localStorage.getItem(pageIdentifier + '_' + buttonId + 'State') === 'true';
                        if (isActive) {
                            button.style.backgroundColor = "var(--laranja)";
                            button.style.border = "1px solid var(--laranja)";
                            button.style.color = "whitesmoke";
                        } else {
                            button.style = "initial";
                        }
                    }
                });
            }
            restaurarEstadosDosBotoes();
            // Configurar eventos de clique para alternar estados e salvar no localStorage
            document.querySelector("#btnJovemAprendiz").addEventListener("click", () => toggleButtonState("btnJovemAprendiz"));
            document.querySelector("#btnEstagio").addEventListener("click", () => toggleButtonState("btnEstagio"));
            document.querySelector("#btnClt").addEventListener("click", () => toggleButtonState("btnClt"));
            document.querySelector("#btnPj").addEventListener("click", () => toggleButtonState("btnPj"));
        });
    </script>
    <script>
        $(document).ready(function () {
            var pageIdentifier = window.location.pathname;
            var storageKey = pageIdentifier + '_lastVisit';
            var now = new Date().getTime();
            var expirationTime = 1 * 60 * 60 * 1000;

            // Verificar se a última visita foi há mais de 24 horas
            var lastVisit = localStorage.getItem(storageKey);
            if (lastVisit && now - lastVisit > expirationTime) {
                // Limpar o localStorage se passou o tempo de expiração
                localStorage.removeItem(pageIdentifier + '_area');
                localStorage.removeItem(pageIdentifier + '_tipos');
                localStorage.removeItem(pageIdentifier + '_apenasVagasAbertas');
                localStorage.removeItem(pageIdentifier + '_termoPesquisa');

                // Remover estados de botões de filtro
                const buttonIds = ["btnJovemAprendiz", "btnEstagio", "btnClt", "btnPj"];
                buttonIds.forEach(buttonId => {
                    localStorage.removeItem(pageIdentifier + '_' + buttonId + 'State');
                });
            }

            // Atualizar a última visita
            localStorage.setItem(storageKey, now);
        });
    </script>

</body>

</html>