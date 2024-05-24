<?php

include "../../services/conexão_com_banco.php";
session_start(); // Inicia a sessão

$emailUsuario = ''; // Supondo que o email do usuário esteja armazenado na sessão
$idPessoa = '';
//Caminho inicial para caso seja visitante
$linkHome = "../../../index.php";
// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    // Se estiver autenticado com o Google
    $emailUsuario = $_SESSION['google_session'];
}
// Verifique se o email do usuário está definido
if (!empty($emailUsuario)) {
    // Consulta SQL para obter o ID da pessoa pelo email
    $sql_id_pessoa = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
    $stmt_id_pessoa = $_con->prepare($sql_id_pessoa);

    if ($stmt_id_pessoa) {
        $stmt_id_pessoa->bind_param("s", $emailUsuario);
        $stmt_id_pessoa->execute();
        $stmt_id_pessoa->store_result(); // Armazena o resultado para obter o número de linhas
        if ($stmt_id_pessoa->num_rows > 0) {
            $stmt_id_pessoa->bind_result($idPessoa); // Vincula o resultado a uma variável
            $stmt_id_pessoa->fetch(); // Busca o valor do resultado
            // Agora $idPessoa contém o ID da pessoa com o email fornecido
            if ($idPessoa > 0) {
                // Se o email estiver cadastrado, defina uma variável para mostrar o botão
                $mostrarBotao = true;
                //Exibe link para candidatos acessar suas contas
                $exibirLink = true;
                $exibirHome = true;

                //Link para acessar conta de candidato
                $linkPerfil = "../PerfilCandidato/perfilCandidato.php?id=$idPessoa";
                //Caso esteja verificado, Perfil direciona corretamente
                $linkHome = "../homeCandidato/homeCandidato.php";

            } else {
                // Se o email não estiver cadastrado, defina a variável para não mostrar o botão
                $mostrarBotao = false;
                //Permite acessar Perfil, porém será direcionado para login como caminho
                $exibirLink = true;

            }
            $stmt_id_pessoa->close(); // Fecha a declaração preparada

        } else {
            // Se houver um erro na preparação da consulta, trate-o aqui
            echo "Erro na preparação da consulta.";
        }
    }
}
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


// Quarta Consulta ao banco de dados para obter as areas 
$sql_areas = "
    SELECT DISTINCT Area 
    FROM Tb_questionarios 
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

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testes</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/todosStyle.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="<?php echo $linkHome; ?>"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>

            <?php
            /*
            //Botão fica escondido caso seja visitante
            if (isset($mostrarBotao) && $mostrarBotao): ?>
            <li><a href="../../../index.php">Deslogar</a></li>
            <?php endif; ?>
            */
            ?>

            <?php if (isset($exibirLink) && $exibirLink): ?>
                <li><a href="<?php echo $linkPerfil; ?>">Perfil</a></li>

            <?php else: ?>
                <li><a href="../Login/login.html">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">T</h1>
        <i class="pisca"></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input class="inputPesquisa" type="text" placeholder="Pesquisa por Título">
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
                            } ?>
                        </select>
                    </div>
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Nível:</label>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="basico" value="Básico" required>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="intermediario"
                            value="Intermediário" required>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="experiente" value="Experiente"
                            required>
                        <label for="basico" class="btnCheckBox" id="btnBasico">Básico</label>
                        <label for="intermediario" class="btnCheckBox" id="btnIntermediario">Intermediário</label>
                        <label for="experiente" class="btnCheckBox" id="btnExperiente">Experiente</label>
                    </div>
                </div>
            </div>
            <div class="divGridTestes">
                <?php
                $sql = "SELECT DISTINCT q.*, e.Nome_da_Empresa
        FROM Tb_Questionarios q
        LEFT JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
        LEFT JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ";
                $result = $_con->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $idQuestionario = $row['Id_Questionario'];
                        $nome = $row['Nome'];
                        $area = $row['Area'];
                        $nomeEmpresa = $row['Nome_da_Empresa'];

                        echo "<a class='testeCarrosselLink' href='../PreparaTeste/preparaTeste.php?id=$idQuestionario'>";
                        echo '<article class="testeCarrossel">';
                        echo '<div class="divAcessos">';
                        echo '<img src="../../assets/images/icones_diversos/people.svg"></img>';
                        echo '<small class="qntdAcessos">800</small>';
                        echo '</div>';
                        echo '<img src="../../../imagens/excel.svg"></img>';
                        echo '<div class="divDetalhesTeste divDetalhesTesteCustom">';
                        echo '<div>';
                        echo '<p class="nomeTeste">' . $nome . '</p>';
                        echo '<small class="autorTeste">' . $nomeEmpresa . '</small><br>';
                        echo '<small class="competenciasTeste">' . $area . '</small>';
                        echo '</div>';
                        echo '</div>';
                        echo '</article>';
                        echo '</a>';
                    }
                } else {
                    echo "<p> Nenhum questionário encontrado.</p>";
                }
                ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="tituloDigitavel.js"></script>
    <script src="checkButtons.js"></script>
    <script src="mostrarFiltros.js"></script>
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
                    url: 'processar_pesquisa.php',
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

            // Seleciona sugestão ao clicar nela
            $(document).on('click', '.sugestao-item', function () {
                var sugestao = $(this).text();
                $('.inputPesquisa').val(sugestao);
                $('.sugestoes').hide();
                salvarValores();
                executarPesquisa();
            });

            // Função para exibir sugestões
            function exibirSugestoes() {
                var termo = $('.inputPesquisa').val();
                var area = $('.selectArea').val();
                if (termo != '') {
                    $.ajax({
                        url: 'obter_sugestoes.php',
                        method: 'POST',
                        data: { termo: termo, area: area },
                        success: function (response) {
                            $('#sugestoes').html(response).show();
                            // Adiciona evento de teclado para seleção das sugestões
                            $('.sugestao-item').first().addClass('selecionada');
                            $(document).on('keydown', function (e) {
                                var sugestoes = $('.sugestao-item');
                                var index = sugestoes.index($('.selecionada'));
                                if (e.which === 38) { // Seta para cima
                                    sugestoes.removeClass('selecionada');
                                    sugestoes.eq(index === 0 ? sugestoes.length - 1 : index - 1).addClass('selecionada');
                                } else if (e.which === 40) { // Seta para baixo
                                    sugestoes.removeClass('selecionada');
                                    sugestoes.eq((index + 1) % sugestoes.length).addClass('selecionada');
                                } else if (e.which === 13 || e.which === 9) { // Enter, Tab ou Espaço
                                    var textoSelecionado = $('.selecionada').text();
                                    $('.inputPesquisa').val(textoSelecionado);
                                    $('#sugestoes').hide();
                                    localStorage.setItem('termoPesquisa', textoSelecionado);
                                    executarPesquisa(); // Chama a função para executar a pesquisa
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
<style>
    /* Adiciona espaçamento entre os questionários */
    .testeCarrosselCustom {
        margin-bottom: 20px;
    }

    /* Define o efeito de hover */
    .testeCarrosselCustom:hover {
        transform: scale(1.05);
        /* Aumenta em 5% ao passar o mouse */
        transition: transform 0.3s ease;
        /* Transição suave com duração de 0.3 segundos */
    }

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

</html>