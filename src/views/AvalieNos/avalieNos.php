<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario'])) {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario'])) {
    $emailUsuario = $_SESSION['google_session'];
}

// Verificar se o usuário não está autenticado
if (empty($emailUsuario)) {
    $userAuthenticated = false;
} else {
    $userAuthenticated = true;

    // Primeira consulta para obter o ID da pessoa logada
    $sql = "SELECT Id_Pessoas, Verificado FROM Tb_Pessoas WHERE Email = ?";
    $stmt = $_con->prepare($sql);

    if ($stmt) {
        // Vincular o parâmetro ao placeholder na consulta
        $stmt->bind_param("s", $emailUsuario);
        // Executar a declaração
        $stmt->execute();
        // Obter o resultado da consulta
        $result = $stmt->get_result();
        // Verificar se a consulta retornou resultados
        if ($result->num_rows > 0) {
            // Obter o ID da pessoa e se ela está verificada
            $row = $result->fetch_assoc();
            $idPessoa = $row['Id_Pessoas'];
            $verificado = $row['Verificado'];
        } else {
            // Tratar o caso em que nenhum resultado é retornado
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avalie-Nos</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/verificacoes.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/perfilStyle.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/editarStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/rodapeStyle.css">
    <style>
        .alert {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .containerTextArea small {
            display: block;
            margin-top: 5px;
            color: #555;
        }

        #aviso-opiniao {
            color: #red;
            margin-top: -5px;
        }

        .alert-success {
            background-color: #0c750a;
            /* Verde claro */
            color: #fff;
            /* Verde escuro para o texto */
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <nav>
        <a href="../../../index.php">
            <img id="logo" src="../../assets/images/logos_empresa/logo_sias.png" alt="Logo da Empresa">
        </a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
    </nav>
    <div class="container">
        <div class="content">
            <?php if ($userAuthenticated): ?>
                <fieldset id="fieldsetAvalieNos">
                    <legend>
                        <h3>Avalie-nos</h3>
                    </legend>
                    <div id="sucesso" style="display: none;" class="alert alert-success">Avaliação enviada com sucesso!
                    </div>
                    <form id="avaliacaoForm" method="post">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($emailUsuario); ?>">
                        <input type="hidden" name="idPessoa" value="<?php echo htmlspecialchars($idPessoa); ?>">
                        <div class="divEstrelas">
                            <p class="pergunta">Quantas estrelas você daria para nossa aplicação?</p>
                            <div class="divRadios">
                                <input type="radio" id="uma" name="estrelas" value="1">
                                <input type="radio" id="duas" name="estrelas" value="2">
                                <input type="radio" id="tres" name="estrelas" value="3">
                                <input type="radio" id="quatro" name="estrelas" value="4">
                                <input type="radio" id="cinco" name="estrelas" value="5">
                            </div>
                            <div class="flexEstrelas">
                                <label for="uma"><img id="umaEstrela" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="duas"><img id="duasEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="tres"><img id="tresEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="quatro"><img id="quatroEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="cinco"><img id="cincoEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                            </div>
                        </div>
                        <div class="divTextArea" id="divTextAreaAvalieNos">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="opiniao" id="opiniao" type="text" required
                                        maxlength="155" oninput="atualizarContador(this)"></textarea>
                                    <div class="textArealabelLine">Minha opinião</div>
                                </div>
                                <small id="contador-caracteres"></small>
                                <small id="aviso-opiniao"></small>
                            </div>
                        </div>
                        <div class="divBtnAtualizar">
                            <input type="submit" value="Enviar" id="btnAvaliar">
                        </div>
                    </form>
                </fieldset>
            <?php else: ?>
                <fieldset id="fieldsetAvalieNos">
                    <legend>
                        <h3>Avalie-nos</h3>
                    </legend>
                    <form>
                        <div class="divEstrelas">
                            <p class="pergunta">Quantas estrelas você daria para nossa aplicação?</p>
                            <div class="divRadios">
                                <input type="radio" id="uma" name="estrelas" value="1">
                                <input type="radio" id="duas" name="estrelas" value="2">
                                <input type="radio" id="tres" name="estrelas" value="3">
                                <input type="radio" id="quatro" name="estrelas" value="4">
                                <input type="radio" id="cinco" name="estrelas" value="5">
                            </div>
                            <div class="flexEstrelas">
                                <label for="uma"><img id="umaEstrela" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="duas"><img id="duasEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="tres"><img id="tresEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="quatro"><img id="quatroEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                                <label for="cinco"><img id="cincoEstrelas" class="estrela"
                                        src="../../assets/images/icones_diversos/orangeStar.svg"></label>
                            </div>
                        </div>
                        <div class="divTextArea" id="divTextAreaAvalieNos">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="opiniao" id="opiniao" type="text"
                                        required></textarea>
                                    <div class="textArealabelLine">Minha opinião</div>
                                </div>
                                <small id="aviso-opiniao"></small>
                            </div>
                        </div>
                        <div class="alert">
                            <p>Por favor, inicie a sessão para avaliar.</p>
                        </div>
                    </form>
                </fieldset>
            <?php endif; ?>
        </div>
    </div>
    <script src="../../../modoNoturno.js"></script>
    <script src="adicionaEstrelas.js"></script>
    <script src="hoverEstrelas.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function atualizarContador(textarea) {
            var contadorCaracteres = document.getElementById('contador-caracteres');
            var contadorPalavras = document.getElementById('contador-palavras');
            var limite = parseInt(textarea.getAttribute('maxlength'));
            var caracteresDigitados = textarea.value.length;
            var caracteresRestantes = limite - caracteresDigitados;
            var palavras = textarea.value.trim().split(/\s+/).filter(function (palavra) {
                return palavra.length > 0;
            });
            var numeroPalavras = palavras.length;

            contadorCaracteres.textContent = caracteresRestantes + ' caracteres restantes';
            contadorPalavras.textContent = numeroPalavras + ' palavras digitadas';
        }

        $(document).ready(function () {
            // Mapa para rastrear se os campos são válidos
            let camposValidos = {
                opiniao: false,
            };

            // Função para verificar palavras localmente e no servidor
            function verificarPalavras(campo, palavras, callback) {
                const letrasRegex = /[a-zA-Z]/g;
                const palavrasInvalidas = [];

                // Verificar localmente se as palavras têm pelo menos 2 letras
                palavras.forEach((palavra) => {
                    const numLetras = (palavra.match(letrasRegex) || []).length;
                    if (numLetras < 0) {
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
                                    mensagemErro += `Há palavras inválidas: <span style="color: red; margin-top: -20px">${palavrasInvalidasDoServidor.join(", ")}</span>. `;
                                }

                                if (palavrasNaoExistem.length > 0) {
                                    mensagemErro += `Há palavras que não existem: <span style="color: red; margin-top: -20px">${palavrasNaoExistem.join(", ")}</span>. `;
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

            $("#opiniao").on("blur", function () {
                const campoId = $(this).attr("id");
                verificarCampo(campoId);
            });

            $("form").on("submit", function (e) {
                // Verificar todos os campos ao enviar o formulário
                ["opiniao"].forEach((campoId) => {
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
            $("#avaliacaoForm").on("submit", function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: "../../services/Avaliação/Avaliaçao.php",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        // Verificar a resposta do servidor
                        if (response.trim() === 'success') {
                            $("#sucesso").show();

                            // Esconder a mensagem após 5 segundos
                            setTimeout(function () {
                                $("#sucesso").hide();
                            }, 5000);
                        } else {
                            alert(response); // Exibir mensagem de erro
                        }
                    },
                    error: function () {
                        alert("Erro ao enviar a avaliação. Tente novamente.");
                    }
                });
            });
        });
    </script>
</body>

</html>