<?php
session_start();

// Inclua o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";

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

// Atribuindo os valores das variáveis de sessão
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';


// Primeira consulta para obter o ID da pessoa logada
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
    } else {
    }
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

$sql_areas = "
    SELECT DISTINCT Area 
    FROM Tb_Anuncios 
    ORDER BY Area ASC
";

// Preparar e executar a consulta para obter as áreas únicas
$stmt_areas = $_con->prepare($sql_areas);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();



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
    <title>Anunciar</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/criacao.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
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
    <div class="divCommon">
        <div class="divTituloComBtn">
            <a class="btnVoltar" href="../HomeRecrutador/homeRecrutador.php"><img
                    src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Criação de Vaga</h2>
        </div>
        <form id="formvaga" method="POST" action="../../../src/services/cadastros/vaga.php" autocomplete="off">
            <div class="containerForm">
                <div class="containerSuperior">
                    <div class="divInputs">
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="titulo" name="titulo" type="text" required>
                                    <div class="labelLine">Título</div>
                                </div>
                                <small name="aviso" id="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="area" name="area" type="text" list="areaList"
                                        required>
                                    <div class="labelLine">Área</div>
                                    <datalist id="areaList">
                                        <?php
                                        foreach ($areas as $area) {
                                            echo "<option value='$area'>$area</option>";
                                        }
                                        ?>
                                    </datalist>
                                </div>
                                <small name="aviso" id="aviso-Area"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="cep" name="cep" type="text" required>
                                    <div class="labelLine">CEP</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="bairro" name="bairro" type="text" required>
                                    <div class="labelLine">Bairro</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="estado" id="estado" type="text" required>
                                    <div class="labelLine">Estado</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="cidade" name="cidade" type="text" required>
                                    <div class="labelLine">Cidade</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="endereco" id="endereco" type="text"
                                        placeholder="Rua Fulano de Tal, 123" required>
                                    <div class="labelLine">Endereço</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="numero" name="numero" type="text"
                                        placeholder="Número da empresa" required>
                                    <div class="labelLine">Número</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" name="horario" id="horario" type="text"
                                    placeholder="seg a sáb, das 9:00 às 16:00" required>
                                <div class="labelLine">Carga horária</div>

                            </div>
                            <small name="aviso" id="avisoHorario"></small>
                        </div>
                    </div>
                    <div class="divTextArea">
                        <div class="containerTextArea">
                            <div class="contentInputTextArea">
                                <textarea class="textAreaAnimada" name="descricao" id="descricao" type="text"
                                    required></textarea>
                                <div class="textArealabelLine">Descrição da Vaga</div>
                            </div>
                            <small name="aviso" id="aviso-descricao"></small>
                        </div>
                        <div class="divFlex" id="divFlexTextArea">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="requisitos" id="requisitos" type="text"
                                        required></textarea>
                                    <div class="textArealabelLine">Requisitos</div>
                                </div>
                                <small name="aviso" id="aviso-requisitos"></small>
                            </div>
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="beneficios" id="beneficios"
                                        required></textarea>
                                    <div class="textArealabelLine">Benefícios</div>
                                </div>
                                <small name="aviso" id="aviso-beneficios"></small>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="containerInferior">
                    <div class="divFlexRadios">
                        <div>
                            <div class="divRadioContent">
                                <h3>Tipo de profissional:</h3>
                                <input type="radio" name="tipo" id="jovemAprendiz" value="Jovem Aprendiz"
                                    class="radioBtn" required>
                                <input type="radio" name="tipo" id="estagio" value="Estágio" class="radioBtn" required>
                                <input type="radio" name="tipo" id="clt" value="CLT" class="radioBtn" required>
                                <input type="radio" name="tipo" id="pj" value="PJ" class="radioBtn" required>
                                <label for="jovemAprendiz" class="btnRadio" id="btnJovemAprendiz">Jovem Aprendiz</label>
                                <label for="estagio" class="btnRadio" id="btnEstagio">Estágio</label>
                                <label for="clt" class="btnRadio" id="btnClt">CLT</label>
                                <label for="pj" class="btnRadio" id="btnPj">PJ</label>
                            </div>
                            <div class="divRadioContent">
                                <h3>Nível de aprendizado:</h3>
                                <input type="radio" name="nivel" id="medio" value="Ensino Médio" class="radioBtn"
                                    required>
                                <input type="radio" name="nivel" id="tecnico" value="Técnico" class="radioBtn" required>
                                <input type="radio" name="nivel" id="superior" value="Superior" class="radioBtn"
                                    required>
                                <label for="medio" class="btnRadio" id="btnMedio">Ensino Médio</label>
                                <label for="tecnico" class="btnRadio" id="btnTecnico">Ensino Técnico</label>
                                <label for="superior" class="btnRadio" id="btnSuperior">Ensino Superior</label>
                            </div>
                        </div>
                        <div>
                            <div class="divRadioContent">
                                <h3>Modalidade:</h3>
                                <input type="radio" name="modalidade" id="remoto" value="Remoto" class="radioBtn"
                                    required>
                                <input type="radio" name="modalidade" id="presencial" value="Presencial"
                                    class="radioBtn" required>
                                <label for="remoto" class="btnRadio" id="btnRemoto">Remoto</label>
                                <label for="presencial" class="btnRadio" id="btnPresencial">Presencial</label>
                            </div>
                            <div class="divRadioContent">
                                <h3>Jornada:</h3>
                                <input type="radio" name="jornada" id="meioPeriodo" value="Meio período"
                                    class="radioBtn" required>
                                <input type="radio" name="jornada" id="integral" value="Tempo integral" class="radioBtn"
                                    required>
                                <label for="meioPeriodo" class="btnRadio" id="btnMeioPeriodo">Meio período</label>
                                <label for="integral" class="btnRadio" id="btnIntegral">Tempo integral</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="emailSession" value="<?php echo $emailUsuario; ?>">
                    <div class="divSalvar">
                        <input type="submit" value="Salvar" class="btnSalvar">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            // Novo regex para validar dias da semana e horários dentro de 24 horas
            const regexDiasHorarios = /^((seg|ter|qua|qui|sex|sáb|dom)( a (seg|ter|qua|qui|sex|sáb|dom))? (das \d{1,2}:\d{2} (às|as) \d{1,2}:\d{2})|(\d{1,2}:\d{2} \- \d{1,2}:\d{2})|(\d{1,2}:\d{2}))$/i


            function validarHorario() {
                const horario = $("#horario").val();
                const aviso = $("#avisoHorario");

                if (horario === "") {
                    aviso.text(""); // Limpar o aviso se o campo estiver vazio
                    return;
                }

                if (!regexDiasHorarios.test(horario)) {
                    console.log("Formato inválido Use esse exemplo: 'seg a sáb, das x:xx às xx:xx' ou 'sáb, xx:xx - xx:xx'.");
                    aviso.text("Formato inválido Use esse exemplo: 'seg a sáb, das x:xx às xx:xx' ou 'sáb, xx:xx - xx:xx'.");
                } else {
                    console.log("Formato válido.");
                    aviso.text("");
                }
            }

            $("#horario").on("blur", function () {
                validarHorario(); // Validar quando o campo perde o foco
            });

            $("#horario").on("input", function () {
                const aviso = $("#avisoHorario");
                const horario = $(this).val();

                if (horario === "") {
                    aviso.text(""); // Limpar o aviso se o campo estiver vazio
                }
            });

            $("#formvaga").on("submit", function (e) {
                validarHorario(); // Validar quando o formulário é enviado
                if ($("#avisoHorario").text() !== "") {
                    e.preventDefault(); // Impedir o envio se houver um aviso de erro
                }
            });
        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Função para definir o comportamento do placeholder
            function configurarPlaceholder(elementId, focusText) {
                var textArea = document.getElementById(elementId);

                textArea.addEventListener("focus", function () {
                    textArea.setAttribute("placeholder", focusText);
                });

                textArea.addEventListener("blur", function () {
                    textArea.removeAttribute("placeholder");
                });
            }

            // Configurar placeholders para diferentes campos
            configurarPlaceholder("beneficios", "Separe os benefícios por vírgula: Benefícios 1, Benefícios 2 ...");
            configurarPlaceholder("requisitos", "Separe os Requisitos por vírgula: Requisitos 1, Requisitos 2 ...");
        });
    </script>
    <script>
        // Defina uma variável JavaScript para armazenar o tema obtido do banco de dados
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
    <script src="radioButtons.js"></script>
    <script>
        $(document).ready(function () {
            let tituloValido = false;
            let clearAvisoTimeout = null; // Variável para armazenar o timeout

            function limparAviso() {
                $("#aviso").text(""); // Limpa a mensagem
                clearAvisoTimeout = null; // Limpa o timeout
            }

            function verificarTitulo(palavra, callback) {
                const letrasRegex = /[a-zA-Z]/g; // Expressão regular para contar letras
                const numLetras = (palavra.match(letrasRegex) || []).length; // Contar as letras

                if (numLetras >= 2) { // Verifica se há pelo menos duas letras
                    $.ajax({
                        url: "verificar-palavra.php",
                        type: "POST",
                        data: { palavra: palavra },
                        success: function (response) {
                            try {
                                const resultado = JSON.parse(response);
                                if (resultado.proibido) {
                                    $("#aviso").text("O título não pode ter '" + palavra + "' ela é uma palavra proibidas.").css("color", "red");
                                    tituloValido = false;
                                } else if (!resultado.existe) {
                                    $("#aviso").text("Palavra '" + palavra + "' não existe, por favor digite outra palavra.").css("color", "red");
                                    tituloValido = false;
                                } else {
                                    $("#aviso").text("Tudo Certo!").css("color", "#086507");
                                    tituloValido = true;
                                }
                            } catch (e) {
                                $("#aviso").text("Erro ao processar resposta do servidor.");
                                tituloValido = false;
                            }
                            callback(); // Notifica que a verificação terminou
                        },
                        error: function () {
                            $("#aviso").text("Erro ao verificar palavra. Tente novamente.");
                            tituloValido = false;
                            callback(); // Notifica que houve erro
                        },
                        timeout: 3000
                    });
                } else {
                    $("#aviso").text("O título deve conter pelo menos duas letras.");
                    tituloValido = false;
                    callback(); // Notifica que a verificação terminou
                }
            }

            $("#titulo").on("blur", function () {
                const palavra = $(this).val();
                if (palavra.trim() === "") { // Verifica se está vazio
                    if (clearAvisoTimeout) {
                        clearTimeout(clearAvisoTimeout); // Cancela o timeout anterior
                    }
                    clearAvisoTimeout = setTimeout(limparAviso, 3000); // Limpa aviso após 3 segundos
                } else {
                    verificarTitulo(palavra, function () {
                        console.log("Verificação do título concluída.");
                    });
                }
            });

            $("#formvaga").on("submit", function (e) {
                const palavra = $("#titulo").val();

                verificarTitulo(palavra, function () {
                    if (!tituloValido) {
                        e.preventDefault();
                        alert("O formulário não pode ser enviado, o título é inválido.");
                    }
                });

                if (palavra.trim() === "") {
                    e.preventDefault();
                    $("#aviso").text("O campo título não pode estar vazio.");
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            let tituloValido = false;
            let clearAvisoTimeout = null; // Variável para armazenar o timeout

            function limparAviso() {
                $("#aviso-Area").text(""); // Limpa a mensagem
                clearAvisoTimeout = null; // Limpa o timeout
            }

            function verificarTitulo(palavra, callback) {
                const letrasRegex = /[a-zA-Z]/g; 
                const numLetras = (palavra.match(letrasRegex) || []).length; // Contar as letras

                if (numLetras >= 2) { // Verifica se há pelo menos duas letras
                    $.ajax({
                        url: "verificar-palavra.php",
                        type: "POST",
                        data: { palavra: palavra },
                        success: function (response) {
                            try {
                                const resultado = JSON.parse(response);
                                if (resultado.proibido) {
                                    $("#aviso").text("A área não pode ter '" + palavra + "' ela é uma palavra proibidas.").css("color", "red");
                                    tituloValido = false;
                                } else if (!resultado.existe) {
                                    $("#aviso").text("Palavra '" + palavra + "' não existe, por favor digite outra palavra.").css("color", "red");
                                    tituloValido = false;
                                } else {
                                    $("#aviso").text("Tudo Certo!").css("color", "#086507");
                                    tituloValido = true;
                                }
                            } catch (e) {
                                $("#aviso").text("Erro ao processar resposta do servidor.");
                                tituloValido = false;
                            }
                            callback(); // Notifica que a verificação terminou
                        },
                        error: function () {
                            $("#aviso").text("Erro ao verificar palavra. Tente novamente.");
                            tituloValido = false;
                            callback(); // Notifica que houve erro
                        },
                        timeout: 3000
                    });
                } else {
                    $("#aviso").text("O Área deve conter pelo menos duas letras.");
                    tituloValido = false;
                    callback(); // Notifica que a verificação terminou
                }
            }

            $("#titulo").on("blur", function () {
                const palavra = $(this).val();
                if (palavra.trim() === "") { // Verifica se está vazio
                    if (clearAvisoTimeout) {
                        clearTimeout(clearAvisoTimeout); // Cancela o timeout anterior
                    }
                    clearAvisoTimeout = setTimeout(limparAviso, 3000); // Limpa aviso após 3 segundos
                } else {
                    verificarTitulo(palavra, function () {
                        console.log("Verificação do título concluída.");
                    });
                }
            });

            $("#formvaga").on("submit", function (e) {
                const palavra = $("#titulo").val();

                verificarTitulo(palavra, function () {
                    if (!tituloValido) {
                        e.preventDefault();
                        alert("O  formulário não pode ser enviado, o Área é inválido.");
                    }
                });

                if (palavra.trim() === "") {
                    e.preventDefault();
                    $("#aviso").text("O campo área não pode estar vazio.");
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Mapa para rastrear se os campos são válidos
            let camposValidos = {
                descricao: false,
                requisitos: false,
                beneficios: false,
            };

            // Função para verificar palavras localmente e no servidor
            function verificarPalavras(campo, palavras, callback) {
                const letrasRegex = /[a-zA-Z]/g;
                const palavrasInvalidas = [];

                // Verificar localmente se as palavras têm pelo menos 2 letras
                palavras.forEach((palavra) => {
                    const numLetras = (palavra.match(letrasRegex) || []).length;
                    if (numLetras < 2) {
                        palavrasInvalidas.push(palavra);
                    }
                });

                if (palavrasInvalidas.length > 0) {
                    $("#aviso-" + campo).text(`Palavras inválidas: ${palavrasInvalidas.join(", ")}`);
                    camposValidos[campo] = false;
                    callback(); // Notifica que a verificação terminou
                } else {
                    $("#aviso-" + campo).text("Verificando palavras...");

                    // Fazer chamada AJAX para verificar palavras no servidor
                    $.ajax({
                        url: "verificar-palavras.php",
                        type: "POST",
                        data: { palavras: palavras },
                        success: function (response) {
                            try {
                                const resultado = JSON.parse(response);

                                const palavrasInvalidasDoServidor = resultado.invalidas || [];
                                const palavrasNaoExistem = resultado.nao_existem || [];

                                let mensagemErro = "";

                                if (palavrasInvalidasDoServidor.length > 0) {
                                    mensagemErro += `Há palavras inválidas: <span style="color: red;">${palavrasInvalidasDoServidor.join(", ")}</span>. `;
                                }

                                if (palavrasNaoExistem.length > 0) {
                                    mensagemErro += `Há palavras que não existem: <span style="color: red;">${palavrasNaoExistem.join(", ")}</span>. `;
                                }


                                if (mensagemErro) {
                                    $("#aviso-" + campo).html(mensagemErro).css("color", "red");
                                    camposValidos[campo] = false;
                                } else {
                                    $("#aviso-" + campo).text("Tudo certo!").css("color", "#086507");
                                    camposValidos[campo] = true;
                                }

                            } catch (e) {
                                $("#aviso-" + campo).text("Erro ao processar resposta do servidor.");
                                camposValidos[campo] = false;
                            }
                            callback(); // Notifica que a verificação terminou
                        },
                        error: function () {
                            $("#aviso-" + campo).text("Erro ao verificar palavras. Tente novamente.");
                            camposValidos[campo] = false;
                            callback();
                        },
                        timeout: 3000
                    });
                }
            }

            function verificarCampo(campoId) {
                const campo = $("#" + campoId);
                const valor = campo.val().trim();
                const palavras = valor.split(/\s+/);

                verificarPalavras(campoId, palavras, function () {
                    console.log(`Verificação do campo ${campoId} concluída.`);
                });
            }

            $("#descricao, #requisitos, #beneficios").on("blur", function () {
                const campoId = $(this).attr("id");
                verificarCampo(campoId);
            });

            $("#formvaga").on("submit", function (e) {
                // Verificar todos os campos ao enviar o formulário
                ["descricao", "requisitos", "beneficios"].forEach((campoId) => {
                    verificarCampo(campoId);
                });

                // Verifique se todos os campos são válidos antes de permitir o envio
                const todosValidos = Object.values(camposValidos).every((valido) => valido);

                if (!todosValidos) {
                    e.preventDefault();
                    alert("O formulário não pode ser enviado. Por favor, corrija os erros.");
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#cep').on('change', function () {
                var cep = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos

                // Verifica se o CEP tem 8 dígitos
                if (cep.length == 8) {
                    $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function (data) {
                        if (!("erro" in data)) {
                            $('#bairro').val(data.bairro);
                            $('#estado').val(data.uf);
                            $('#cidade').val(data.localidade);
                            $('#endereco').val(data.logradouro);
                        } else {
                            alert('CEP não encontrado.');
                        }
                    });
                } else {
                    alert('CEP inválido. Por favor, insira um CEP válido com 8 dígitos.');
                }
            });
        });
    </script>
</body>

</html>