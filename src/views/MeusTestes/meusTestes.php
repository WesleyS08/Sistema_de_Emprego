<?php
include "../../services/conexão_com_banco.php";

session_start();

// Definição de variáveis
$emailUsuario = '';
$cnpjLogado = '';
$autenticadoComoEmpresa = false; // Defina a variável para evitar o aviso de indefinição

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha
    $emailUsuario = $_SESSION['email_session'];
    // Buscar o CNPJ da empresa logada
    $sql_cnpj = "SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?)";
    $stmt_cnpj = $_con->prepare($sql_cnpj);
    if ($stmt_cnpj) {
        $stmt_cnpj->bind_param("s", $emailUsuario);
        $stmt_cnpj->execute();
        $result_cnpj = $stmt_cnpj->get_result();
        if ($result_cnpj->num_rows > 0) {
            $row_cnpj = $result_cnpj->fetch_assoc();
            $cnpjLogado = $row_cnpj['CNPJ'];
        }
        $stmt_cnpj->close();
    }
}

// Verificar se o usuário está autenticado como empresa
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    $autenticadoComoEmpresa = true;
}

$sql = "SELECT Id_Pessoas, Verificado FROM Tb_Pessoas WHERE Email = ?";
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
        // Obtenha o ID da pessoa e se ela está verificada
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];
        $verificado = $row['Verificado'];
    } else {
        // Trate o caso em que nenhum resultado é retornado
    }
    $stmt->close();
}
// Quartar consulta para selecionar o tema que a pessoa selecionou 
if (isset($idPessoa)) {
    $query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt = $_con->prepare($query);
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
        $stmt->close();
    } else {
        die("Erro ao preparar a query.");
    }
}

// Quinta Consulta para obter áreas do banco de dados 
$sql_areas = "SELECT DISTINCT Area FROM Tb_Questionarios ORDER BY Area ASC";
$stmt_areas = $_con->prepare($sql_areas);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();

$areas = ["Todas"]; // Adicionar a opção "Todas" ao início do array

if ($result_areas && $result_areas->num_rows > 0) {
    while ($row = $result_areas->fetch_assoc()) {
        $areas[] = $row['Area'];
    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus testes</title>
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
            background-color: #f0f0f0;
            /* Cor de fundo da sugestão selecionada */
            color: #333;
            /* Cor do texto da sugestão selecionada */
        }
    </style>

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
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li><!--Arrumar esse link  -->
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">M</h1>
        <i class="pisca"></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input class="inputPesquisa" type="text" placeholder="Pesquisar">
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
                        <label class="nomeFiltro">Nível:</label>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="basico" value="Básico" required>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="intermediario"
                            value="Intermediário" required>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="experiente" value="Avançado"
                            required>
                        <label for="basico" class="btnCheckBox" id="btnBasico">Básico</label>
                        <label for="intermediario" class="btnCheckBox" id="btnIntermediario">Intermediário</label>
                        <label for="experiente" class="btnCheckBox" id="btnExperiente">Experiente</label>
                    </div>

                </div>
            </div>
            <div class="divGridTestes">
                <?php
                if ($autenticadoComoEmpresa && !empty($cnpjLogado)) {

                    // Consulta SQL para selecionar os questionários associados à empresa logada
                    $sql_questionarios = "SELECT q.*, e.Nome_da_Empresa AS Nome_Empresa FROM Tb_Questionarios q INNER JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario INNER JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ WHERE eq.Id_Empresa = ?";
                    $stmt_questionarios = $_con->prepare($sql_questionarios);

                    if ($stmt_questionarios) {
                        $stmt_questionarios->bind_param("s", $cnpjLogado);
                        $stmt_questionarios->execute();
                        $result_questionarios = $stmt_questionarios->get_result();

                        // Verificar se há questionários associados
                        if ($result_questionarios->num_rows > 0) {

                            // Loop para exibir os questionários
                            while ($row_questionarios = $result_questionarios->fetch_assoc()) {

                                // Contar o número de respostas para o questionário atual
                                $idQuestionario = $row_questionarios['Id_Questionario']; // Definindo o ID do questionário
                                $sql_contagem_respostas = "SELECT COUNT(*) AS total_respostas FROM Tb_Resultados WHERE Tb_Questionarios_ID = ?";
                                $stmt_contagem_respostas = $_con->prepare($sql_contagem_respostas);

                                if ($stmt_contagem_respostas) {
                                    $stmt_contagem_respostas->bind_param("i", $idQuestionario);
                                    $stmt_contagem_respostas->execute();
                                    $result_contagem_respostas = $stmt_contagem_respostas->get_result();

                                    // Verifica se a consulta retornou algum resultado
                                    if ($result_contagem_respostas->num_rows > 0) {
                                        $row_contagem_respostas = $result_contagem_respostas->fetch_assoc();
                                        $total_respostas = $row_contagem_respostas['total_respostas'];
                                    } else {
                                        $total_respostas = 0;
                                    }
                                    // Exibir o HTML com os detalhes do questionário e o total de respostas
                                    echo '<a class="testeCarrosselLink" href="../PreparaTeste/preparaTeste.html">';
                                    echo '<article class="testeCarrossel">';
                                    echo '<div class="divAcessos">';
                                    echo '<img src="../../../imagens/people.svg"></img>';
                                    echo '<small class="qntdAcessos">' . $total_respostas . '</small>';
                                    echo '</div>';
                                    echo '<img class="imgTeste" src="' . $row_questionarios['ImagemQuestionario'] . '"></img>';
                                    echo '<div class="divDetalhesTeste">';
                                    echo '<div>';
                                    echo '<p class="nomeTeste">' . $row_questionarios['Nome'] . '</p>'; // Exibindo o título do questionário
                                    echo '<small class="autorTeste">' . $row_questionarios["Nome_Empresa"] . '</small><br>'; // Exibindo o nome da empresa como autor
                                    echo '<small class="competenciasTeste">' . $row_questionarios['Descricao'] . '</small>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</article>';
                                    echo '</a>';

                                    // Lembre-se de fechar o statement depois de usá-lo
                                    $stmt_contagem_respostas->close();
                                }
                            }
                        } else {
                            echo '<script>';
                            echo 'console.log("Nenhum questionário associado à empresa logada.");';
                            echo '</script>';
                        }

                        // Lembre-se de fechar o statement depois de usá-lo
                        $stmt_questionarios->close();
                    } else {
                        echo '<script>';
                        echo 'console.log("Erro ao preparar a consulta.");';
                        echo '</script>';
                    }
                } else {
                    echo '<script>';
                    echo 'console.log("Usuário não autenticado como empresa ou CNPJ não encontrado.");';
                    echo '</script>';
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="checkButtons.js"></script>
    <script src="mostrarFiltros.js"></script>
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
    <script>
        $(document).ready(function () {
            var idPessoa = <?php echo json_encode($idPessoa); ?>; // Armazena o ID da pessoa
            var tema = <?php echo json_encode($tema); ?>; // Armazena o tema (noturno ou claro)

            // Função para aplicar o modo noturno
            function aplicarModoNoturno() {
                if (tema === "noturno") {
                    $("body").addClass("noturno");
                    Noturno(); // Chama a função que configura o modo noturno
                } else {
                    $("body").removeClass("noturno");
                    Claro(); // Chama a função que configura o modo claro
                }
            }

            // Aplicar o modo noturno ao carregar a página
            aplicarModoNoturno();

            // Carrega os valores salvos no localStorage
            function carregarValores() {
                var termo = localStorage.getItem('termo');
                var area = localStorage.getItem('area');
                var criador = localStorage.getItem('criador');
                var niveis = localStorage.getItem('niveis');

                if (termo) {
                    $('.inputPesquisa').val(termo);
                }
                if (area) {
                    $('.selectArea').val(area);
                }
                if (criador) {
                    $('#criadorFiltro').val(criador);
                }
                if (niveis) {
                    niveis = JSON.parse(niveis);
                    $('.checkBoxTipo').prop('checked', false);
                    niveis.forEach(function (nivel) {
                        $('#' + nivel.toLowerCase()).prop('checked', true);
                    });
                }

                restaurarEstadosDosBotoes();
            }

            // Salva os valores no localStorage
            function salvarValores() {
                var termo = $('.inputPesquisa').val();
                var area = $('.selectArea').val();
                var criador = $('#criadorFiltro').val();
                var niveis = [];
                $('.checkBoxTipo:checked').each(function () {
                    niveis.push($(this).val());
                });

                localStorage.setItem('termo', termo);
                localStorage.setItem('area', area);
                localStorage.setItem('criador', criador);
                localStorage.setItem('niveis', JSON.stringify(niveis)); // Salvar os níveis como JSON
            }

            // Executa a pesquisa
            function executarPesquisa() {
                salvarValores(); // Salvar valores antes de executar a pesquisa
                var termo = $('.inputPesquisa').val();
                var area = $('.selectArea').val();
                var criador = $('#criadorFiltro').val();
                var niveis = JSON.parse(localStorage.getItem('niveis'));

                $.ajax({
                    url: 'buscar_questionario_filtros.php',
                    method: 'POST',
                    data: {
                        termo: termo, area: area, criador: criador, niveis: niveis, idPessoa: idPessoa, tema: tema
                    },
                    success: function (response) {
                        $('.divGridTestes').html(response).addClass('noturno');
                    },
                    error: function (error) {
                        console.error("Erro ao processar a pesquisa:", error);
                    }
                });
            }

            // Eventos para salvar valores e executar pesquisa quando inputs mudam
            $('.inputPesquisa, .selectArea, #criadorFiltro, .checkBoxTipo').on('input change', function () {
                salvarValores();
                executarPesquisa();
            });

            // Executa a pesquisa ao carregar a página
            executarPesquisa();

            // Exibe sugestões ao focar na inputPesquisa
            $('.inputPesquisa').focus(function () {
                exibirSugestoes();
            });

            // Oculta sugestões após 5 segundos de perder o foco
            $('.inputPesquisa').blur(function () {
                setTimeout(function () {
                    $('.sugestoes').hide();
                }, 5000);
            });

            // Função para exibir sugestões
           
            function exibirSugestoes() {
                var query = $('.inputPesquisa').val();
                var area = $('.selectArea').val();
                if (query != '') {
                    $.ajax({
                        url: 'obter_sugestoes.php',
                        method: 'POST',
                        data: { idPessoa: idPessoa, query: query, area: area },
                        success: function (response) {
                            $('#sugestoes').html(response).show();
                            // Adiciona evento de teclado para seleção das sugestões
                            $(document).off('keydown').on('keydown', function (e) {
                                var sugestoes = $('.sugestao-item');
                                var index = sugestoes.index($('.selecionada'));
                                if (e.which === 38) { // Seta para cima
                                    e.preventDefault(); // Previne o comportamento padrão
                                    sugestoes.removeClass('selecionada');
                                    sugestoes.eq(index === 0 ? sugestoes.length - 1 : index - 1).addClass('selecionada');
                                } else if (e.which === 40) { // Seta para baixo
                                    e.preventDefault(); // Previne o comportamento padrão
                                    sugestoes.removeClass('selecionada');
                                    sugestoes.eq((index + 1) % sugestoes.length).addClass('selecionada');
                                } else if (e.which === 13 || e.which === 9) { // Enter, Tab 
                                    var textoSelecionado = $('.selecionada').text();
                                    $('.inputPesquisa').val(textoSelecionado);
                                    $('#sugestoes').hide();
                                    localStorage.setItem('termoPesquisa', textoSelecionado);
                                    // Chame sua função para buscar vagas aqui
                                }
                            });
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
            }


            executarPesquisa();

            // Evento para chamar a função ao digitar na inputPesquisa
            $('.inputPesquisa').on('input', exibirSugestoes);

            // Chamada inicial para esconder as sugestões
            $('#sugestoes').hide();

            // Função para destacar a sugestão
            function highlightSuggestion(index) {
                console.log('highlightSuggestion called with index:', index);
                $('.sugestao-item').removeClass('selected');
                $('.sugestao-item').eq(index).addClass('selected');
            }

            // Função para alternar o estado do botão
            function toggleButtonState(buttonId) {
                const button = document.querySelector(`#${buttonId}`);
                let isActive = localStorage.getItem(`${buttonId}State`) === 'true';
                isActive = !isActive;
                localStorage.setItem(`${buttonId}State`, isActive);
                if (isActive) {
                    button.style.backgroundColor = "var(--laranja)";
                    button.style.border = "1px solid var(--laranja)";
                    button.style.color = "whitesmoke";
                } else {
                    button.style = "initial";
                }
            }
        });
    </script>
</body>

</html>