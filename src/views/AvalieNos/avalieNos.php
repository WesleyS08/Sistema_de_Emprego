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
                    <form method="post" action="../../services/Avaliação/Avaliaçao.php">
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
                                <small id="contador-caracteres" name="aviso"></small>
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
                                <small name="aviso"></small>
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
    <script>
    function atualizarContador(textarea) {
        var contador = document.getElementById('contador-caracteres');
        var limite = parseInt(textarea.getAttribute('maxlength'));
        var caracteresDigitados = textarea.value.length;
        var caracteresRestantes = limite - caracteresDigitados;
        contador.textContent = caracteresRestantes + ' caracteres restantes';
    }
</script>
</body>

</html>