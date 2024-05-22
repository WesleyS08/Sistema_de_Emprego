<?php
include "../../services/conexão_com_banco.php";

session_start();
// * =========================   Comentários ao longo fo código corrigidos ========================== *//

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
        <a>Política de Privacidade</a>
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

    <!--================================ Obter sugestão dos títulos ======================================= -->
    <script>
        $(document).ready(function () {
            var areaAtual = $('.selectArea').val();
            var idPessoa = <?php echo json_encode($idPessoa); ?>; // Armazena o ID da pessoa

            // Atualiza a área sempre que o usuário muda a seleção
            $('.selectArea').on('change', function () {
                areaAtual = $(this).val();
            });

            var termoAnterior = localStorage.getItem('termoPesquisa');
            if (termoAnterior) {
                $('.inputPesquisa').val(termoAnterior);
                buscarVagasPorTitulo(termoAnterior, areaAtual, idPessoa); // Passa a área e o ID da pessoa corretos
            }

            $('.inputPesquisa').on('input', function () {
                var searchTerm = $(this).val();
                localStorage.setItem('termoPesquisa', searchTerm);

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

            $(document).on('click', '.sugestao-item', function () {
                var textoSelecionado = $(this).text();
                $('.inputPesquisa').val(textoSelecionado);
                $('#sugestoes').hide();
                localStorage.setItem('termoPesquisa', textoSelecionado);
                buscarVagasPorTitulo(textoSelecionado, areaAtual, idPessoa); // Passa a área e o ID da pessoa corretos
            });

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
        });
    </script>


    <!--================================ Buscar Vagas por filtros ======================================= -->
    <script>
        $(document).ready(function () {
            var idPessoa = <?php echo json_encode($idPessoa); ?>; // Armazena o ID da pessoa
            var tema = <?php echo json_encode($tema); ?>; // Armazena o tema

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

            // Quando houver uma mudança em qualquer filtro, salvar no localStorage
            $('.selectArea, .checkBoxTipo, #apenasVagasAbertas, .inputPesquisa').on('change input', function () {
                salvarFiltros();
                aplicarFiltros();
            });

            function salvarFiltros() {
                // Obter valores dos filtros
                var area = $('.selectArea').val();
                var tipos = [];
                $('.checkBoxTipo:checked').each(function () {
                    tipos.push($(this).val());
                });
                var apenasVagasAbertas = $('#apenasVagasAbertas').is(':checked');
                var termoPesquisa = $('.inputPesquisa').val();
                // Salvar no localStorage
                localStorage.setItem('area', area);
                localStorage.setItem('tipos', JSON.stringify(tipos));
                localStorage.setItem('apenasVagasAbertas', apenasVagasAbertas);
                localStorage.setItem('termoPesquisa', termoPesquisa);
            }

            function aplicarFiltros() {
                // Obter valores dos filtros
                var area = localStorage.getItem('area');
                var tipos = JSON.parse(localStorage.getItem('tipos')) || [];
                var apenasVagasAbertas = JSON.parse(localStorage.getItem('apenasVagasAbertas'));
                var termoPesquisa = localStorage.getItem('termoPesquisa') || "";

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

            aplicarFiltros();
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Função para alternar o estado do botão e salvar no localStorage
            function toggleButtonState(buttonId) {
                const button = document.querySelector(`#${buttonId}`);
                let isActive = localStorage.getItem(`${buttonId}State`) === 'true';
                // Alternar o estado do botão
                isActive = !isActive;
                localStorage.setItem(`${buttonId}State`, isActive); // Salvar no localStorage
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
                        const isActive = localStorage.getItem(`${buttonId}State`) === 'true';
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
</body>

</html>