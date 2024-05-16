<?php
include "../../services/conexão_com_banco.php";


session_start();

// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// Armazenando o email do usuário no JSON
echo "<script>var emailUsuario = '" . $emailUsuario . "';</script>";

// Primeira consulta para obter o ID da pessoa logada
$sql = "SELECT Id_Pessoas, Verificado FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

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
    FROM Tb_Questionarios 
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
    <title>Criação de Teste</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/criacao.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/teste.css">

    <style>
        /* Oculta o texto "Nenhum item selecionado" */
        input[type="file"]::-webkit-file-upload-button,
        input[type="file"]::-webkit-file-upload-button::before {
            content: none;
            display: none;
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
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li>
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <a class="btnVoltar"  href="../HomeRecrutador/homeRecrutador.php"><img class="backImg" src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Criação de Teste</h2>
        </div>
        <form autocomplete="off"  id="formulario">
            <div class="containerForm">
                <div class="containerSuperior">
                    <div class="divFlexSuperior">
                        <div class="divImgTeste">
                            <!-- Input para selecionar a imagem -->
                            <input type="file" name="inputImagem" id="inputImagem" accept="image/jpeg, image/png"
                                onchange="carregarImagem(event)"
                                style="opacity: 0; position: absolute; width: 100%; height: 100%;">
                            <p id="textoAdicionarImagem">Adicionar Imagem</p>
                            <!-- Imagem carregada -->
                            <div id="imagemCarregada" style="cursor: pointer;">
                                <label for="inputImagem" class="custom-file-upload">
                                    <div class="divIconeEditar" id="divIconeEditar">
                                        <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover"
                                            stroke="bold" colors="primary:#f5f5f5,secondary:#f5f5f5"
                                            style="width:110px;height:110px"></lord-icon>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="divInputs">
                            <div class="divFlex">
                                <div class="containerInput">
                                    <div class="contentInput">
                                        <input class="inputAnimado" id="titulo" name="titulo" type="text" required>
                                        <div class="labelLine">Título</div>
                                    </div>
                                    <small name="aviso" id="aviso"></small>
                                </div>
                            </div>
                            <div class="divFlex">
                                <div class="containerInput">
                                    <select class="inputAnimado" id="nivel" name="nivel" type="text" required>
                                        <option label="Nível" style="display:none;"></option>
                                        <option>Iniciante</option>
                                        <option>Intermediário</option>
                                        <option>Avançado</option>
                                    </select>
                                    <small name="aviso"></small>
                                </div>
                            </div>
                            <div class="divFlex">
                                <div class="containerInput">
                                    <div class="contentInput">
                                        <input class="inputAnimado" id="duracao" name="duracao" type="text"
                                            pattern="[0-9]*" required>
                                        <div class="labelLine">Duração (min)</div>
                                    </div>
                                    <small name="aviso"></small>
                                </div>
                            </div>

                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="area" id="area" type="text" list="areaList"
                                        placeholder="Área do questionário" required>
                                    <div class="labelLine">Área</div>
                                    <datalist id="areaList">
                                        <?php
                                        foreach ($areas as $area) {
                                            echo "<option value='$area'>$area</option>";
                                        }
                                        ?>
                                    </datalist>
                                </div>
                                <small name="aviso" id="avisoArea"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="data" id="data" type="date"
                                        placeholder="Data do questionário" required>
                                    <div class="labelLine"></div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="competencias" id="competencias" type="text"
                                        placeholder="Ciência de Dados, Python, MySql, Power BI" required>
                                    <div class="labelLine">Competências</div>
                                </div>
                                <small name="aviso" id="avisocompetencias"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="divQuestoes" id="divQuestoesCriacao">
                    <div class="questoesAdicionadas">
                        <div class="articleQuestao">
                            <div class="divPergunta">
                                <p class="numQuestao">1</p>
                                <p class="ponto">.</p>
                                <input class="inputSimples" type="text" placeholder="Pergunta">
                            </div>
                            <div class="divAlternativas">
                                <div class="divRadio">
                                    <input type="radio" name="questao1">
                                    <input class="inputSimples" type="text" placeholder="Resposta">
                                </div>
                                <div class="divRadio">
                                    <input type="radio" name="questao1">
                                    <input class="inputSimples" type="text" placeholder="Resposta">
                                </div>
                                <div class="divRadio">
                                    <input type="radio" name="questao1">
                                    <input class="inputSimples" type="text" placeholder="Resposta">
                                </div>
                                <div class="divRadio">
                                    <input type="radio" name="questao1">
                                    <input class="inputSimples" type="text" placeholder="Resposta">
                                </div>
                                <div class="divRadio">
                                    <input type="radio" name="questao1">
                                    <input class="inputSimples" type="text" placeholder="Resposta">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="btnAdicionar">Adicionar</div>
                </div>
                <input type="hidden" name="idPessoa" id="idPessoa" value="<?php echo $idPessoa; ?>">
                <div class="divSalvar">
                    <input type="submit" value="Salvar" class="btnSalvar">
                </div>
            </div>
        </form>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a href="../NossoContato/nossoContato.html">Nosso contato</a>
        <a href="../AvalieNos/avalieNos.html">Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="adicionaQuestao.js"></script>
    <script src="mostraIcone.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="processarQuestoes.js"></script>
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
    <script src="imagemTeste.js"></script>
    <script>
        document.getElementById("area").addEventListener("change", function (event) {
            carregarImagem(event);
        });

        // Variáveis globais para armazenar temporariamente a imagem e o nome personalizado
        var imagemSelecionadaTemp = null;
        var novoNomeTemp = null;

        function carregarImagem(event) {
            var imagemSelecionada = event.target.files[0];
            var idUsuario = <?php echo $idPessoa; ?>; // Obtém o ID do usuário
            var urlImagem = URL.createObjectURL(imagemSelecionada);
            var imgElemento = document.createElement("img");
            imgElemento.style.width = "200px"; // Define a largura desejada da imagem
            imgElemento.style.height = "200px"; // Define a altura desejada da imagem
            imgElemento.onload = function () {
                var larguraMaxima = document.querySelector(".divImgTeste").offsetWidth;
                var alturaMaxima = document.querySelector(".divImgTeste").offsetHeight;
                var proporcao = Math.min(larguraMaxima / imgElemento.width, alturaMaxima / imgElemento.height);
                imgElemento.width = imgElemento.width * proporcao;
                imgElemento.height = imgElemento.height * proporcao;
            };
            imgElemento.src = urlImagem;

            // Limpa qualquer imagem anterior
            var divImgTeste = document.querySelector("#imagemCarregada");
            while (divImgTeste.firstChild) {
                divImgTeste.removeChild(divImgTeste.firstChild);
            }

            // Adiciona a nova imagem
            divImgTeste.appendChild(imgElemento);

            // Restaura o texto do parágrafo
            var pTextoAdicionarImagem = document.getElementById("textoAdicionarImagem");
            pTextoAdicionarImagem.style.display = "block";


        }

    </script>
    <script>
        $(document).ready(function () {
            let tituloValido = false;
            let clearAvisoTimeout = null; // Variável para armazenar o timeout

            function limparAviso(area) {
                $(area).text(""); // Limpa a mensagem
                clearAvisoTimeout = null; // Limpa o timeout
            }

            function verificarCampo(campo, area) {
                const palavra = $(campo).val();
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
                                    $(area).text("A " + area + " não pode ter '" + palavra + "' pois é uma palavra proibida.").css("color", "red");
                                    tituloValido = false;
                                } else if (!resultado.existe) {
                                    $(area).text("A palavra '" + palavra + "' não existe, por favor, digite outra palavra.").css("color", "red");
                                    tituloValido = false;
                                } else {
                                    $(area).text("Tudo certo!").css("color", "#086507");
                                    tituloValido = true;
                                }
                            } catch (e) {
                                $(area).text("Erro ao processar resposta do servidor.");
                                tituloValido = false;
                            }
                        },
                        error: function () {
                            $(area).text("Erro ao verificar palavra. Tente novamente.");
                            tituloValido = false;
                        },
                        timeout: 3000
                    });
                } else {
                    $(area).text("A " + area + " deve conter pelo menos duas letras.").css("color", "red");
                    tituloValido = false;
                }
            }

            function configurarVerificacao(campo, area) {
                $(campo).on("blur", function () {
                    const palavra = $(this).val();
                    if (palavra.trim() === "") { // Verifica se está vazio
                        if (clearAvisoTimeout) {
                            clearTimeout(clearAvisoTimeout); // Cancela o timeout anterior
                        }
                        clearAvisoTimeout = setTimeout(function () {
                            limparAviso(area);
                        }, 3000); // Limpa aviso após 3 segundos
                    } else {
                        verificarCampo(campo, area);
                    }
                });
            }

            configurarVerificacao("#titulo", "#aviso");
            configurarVerificacao("#area", "#avisoArea");

            $("form").on("submit", function (e) {
                const palavraTitulo = $("#titulo").val();
                const palavraArea = $("#area").val();

                verificarCampo("#titulo", "#aviso");
                verificarCampo("#area", "#avisoArea");

                if (palavraTitulo.trim() === "") {
                    e.preventDefault();
                    $("#aviso").text("O campo título não pode estar vazio.").css("color", "red");
                }

                if (palavraArea.trim() === "") {
                    e.preventDefault();
                    $("#avisoArea").text("O campo Área não pode estar vazio.").css("color", "red");
                }

                if (!tituloValido) {
                    e.preventDefault();
                    alert("O formulário não pode ser enviado, o título ou a Área são inválidos.");
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Mapa para rastrear se os campos são válidos
            let camposValidos = {
                competencias: false
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
                    $("#avisocompetencias").text(`Palavras inválidas: ${palavrasInvalidas.join(", ")}`).css("color", "red");
                    camposValidos[campo] = false;
                    callback(); // Notifica que a verificação terminou
                } else {
                    $("#avisocompetencias").text("Verificando palavras...");

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
                                    $("#avisocompetencias").html(mensagemErro).css("color", "red");
                                    camposValidos[campo] = false;
                                } else {
                                    $("#avisocompetencias").text("Tudo certo!").css("color", "#086507");
                                    camposValidos[campo] = true;
                                }

                            } catch (e) {
                                $("#avisocompetencias").text("Erro ao processar resposta do servidor.").css("color", "red");
                                camposValidos[campo] = false;
                            }
                            callback(); // Notifica que a verificação terminou
                        },
                        error: function () {
                            $("#avisocompetencias").text("Erro ao verificar palavras. Tente novamente.").css("color", "red");
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

            $("#competencias").on("blur", function () {
                const campoId = $(this).attr("id");
                verificarCampo(campoId);
            });

            $("form").on("submit", function (e) {
                // Verificar todos os campos ao enviar o formulário
                Object.keys(camposValidos).forEach((campoId) => {
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

</body>

</html>