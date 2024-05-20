<?php

include "../../services/conexão_com_banco.php";
session_start(); // Inicia a sessão

$emailUsuario = ''; // Supondo que o email do usuário esteja armazenado na sessão
$idPessoa = '';

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
        } else {
            // Se nenhum resultado for encontrado, você pode tratar isso aqui
            echo "Nenhum resultado encontrado para o email fornecido.";
        }
        $stmt_id_pessoa->close(); // Fecha a declaração preparada
    } else {
        // Se houver um erro na preparação da consulta, trate-o aqui
        echo "Erro na preparação da consulta.";
    }
} else {
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
        <a href="#"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>

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
                        <label class="nomeFiltro">Criador do teste:</label>
                        <input class="selectArea" type="text" id="criadorFiltro" name="criadorFiltro"
                            placeholder="Cisco, Microsoft, etc">
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
                        echo '<img src="../../../imagens/people.svg"></img>';
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
                Claro(); /
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.inputPesquisa').keyup(function () {
                var query = $(this).val();
                var area = $('.selectArea').val(); // Captura o valor selecionado na área
                if (query != '') {
                    $.ajax({
                        url: 'obter_sugestoes.php',
                        method: 'POST',
                        data: { query: query, area: area }, // Envia também a área selecionada
                        success: function (data) {
                            $('#sugestoes').html(data);
                            $('.sugestoes').show();
                            // Exibe os dados recebidos no console do navegador
                            console.table(data);
                        }
                    });
                } else {
                    $('.sugestoes').hide();
                }
            });

            $(document).on('click', '.sugestao-item', function () {
                var sugestao = $(this).text();
                $('.inputPesquisa').val(sugestao); // Define o valor do campo de pesquisa com a sugestão clicada
                $('.sugestoes').hide(); // Esconde as sugestões após clicar
            });
        });
    </script>
  <script>
$(document).ready(function () {
    // Função para executar a pesquisa
    function executarPesquisa() {
        var termo = $('.inputPesquisa').val();
        var area = $('.selectArea').val();
        var criador = $('#criadorFiltro').val();
        var niveis = [];
        $('.checkBoxTipo:checked').each(function () {
            niveis.push($(this).val());
        });

        // Realizar a solicitação AJAX para processar a pesquisa
        $.ajax({
            url: 'processar_pesquisa.php',
            method: 'POST',
            data: { termo: termo, area: area, criador: criador, niveis: niveis },
            success: function (response) {
                $('.divGridTestes').html(response); 
            },
            error: function (error) {
                console.error("Erro ao processar a pesquisa:", error);
            }
        });
    }

    // Executar a pesquisa quando houver uma alteração em qualquer elemento relevante
    $('.inputPesquisa, .selectArea, #criadorFiltro, .checkBoxTipo').on('input change', function () {
        executarPesquisa();
    });

    // Executar a pesquisa inicialmente ao carregar a página
    executarPesquisa();
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
</style>

</html>