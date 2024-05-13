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
            <a class="btnVoltar"><img class="backImg" src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Criação de Teste</h2>
        </div>
        <form autocomplete="off">
            <div class="containerForm">
                <div class="containerSuperior">
                    <div class="divFlexSuperior">
                        <div class="divImgTeste">
                            <!-- Input para selecionar a imagem -->
                            <input type="file" id="inputImagem" accept="image/jpeg, image/png"
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
                                    <small name="aviso"></small>
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
                                        <input class="inputAnimado" id="duracao" name="duracao" type="number" required>
                                        <div class="labelLine">Duração (min)</div>
                                    </div>
                                    <small name="aviso"></small>
                                </div>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="area" id="area" type="text"
                                        placeholder="Area do questionário" required>
                                    <div class="labelLine">Area</div>
                                </div>
                                <small name="aviso"></small>
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
                                <small name="aviso"></small>
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
                                <div class="divRadio"><input type="radio" name="questao1"><input class="inputSimples"
                                        type="text" placeholder="Resposta"></div>
                                <div class="divRadio"><input type="radio" name="questao1"><input class="inputSimples"
                                        type="text" placeholder="Resposta"></div>
                                <div class="divRadio"><input type="radio" name="questao1"><input class="inputSimples"
                                        type="text" placeholder="Resposta"></div>
                                <div class="divRadio"><input type="radio" name="questao1"><input class="inputSimples"
                                        type="text" placeholder="Resposta"></div>
                                <div class="divRadio"><input type="radio" name="questao1"><input class="inputSimples"
                                        type="text" placeholder="Resposta"></div>
                            </div>
                        </div>
                    </div>
                    <div id="btnAdicionar">Adicionar</div>
                </div>
                <div class="divSalvar">
                    <input type="submit" value="Salvar" class="btnSalvar">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="adicionaQuestao.js"></script>
    <script src="mostraIcone.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="processarQuestoes.js"></script>
    <script src="../../../modoNoturno.js"></script>
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
            var area = document.getElementById("area").value; // Obtém o valor do campo de área
            novoNomeTemp = "imagem_" + idUsuario;
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

            // Altera o nome do arquivo
            imagemSelecionadaTemp = imagemSelecionada;
            imagemSelecionadaTemp.name = novoNomeTemp;

            // Exibe o nome do arquivo após ser renomeado
            console.log('Nome do arquivo antes  renomear:', imagemSelecionadaTemp.name);

            // Adicione um ponto de interrupção aqui para verificar o valor de novoNome
            console.log('Novo nome da imagem:', novoNomeTemp);
        }

    </script>

</body>

</html>