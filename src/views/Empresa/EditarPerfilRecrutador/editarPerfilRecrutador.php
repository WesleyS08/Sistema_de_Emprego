<?php
include "../../services/conexão_com_banco.php";

session_start();


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
    header("Location: ../Login/login.html");
    exit;
}

$idPessoa = isset($_GET['id']) ? $_GET['id'] : '';


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

// Recuperar informações da empresa do banco de dados com base no e-mail do usuário
$query = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario')";
$result = mysqli_query($_con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $dadosEmpresa = mysqli_fetch_assoc($result);
    // Preencher o formulário com as informações recuperadas do banco de dados
    $nomeEmpresa = $dadosEmpresa['Nome_da_Empresa'];
    $areaEmpresa = $dadosEmpresa['Area_da_Empresa'];
    $telefoneEmpresa = $dadosEmpresa['Telefone'];
    $sobreEmpresa = $dadosEmpresa['Sobre_a_Empresa'];
    $facebookEmpresa = $dadosEmpresa['Facebook'];
    $githubEmpresa = $dadosEmpresa['Github'];
    $linkedinEmpresa = $dadosEmpresa['Linkedin'];
    $instagramEmpresa = $dadosEmpresa['Instagram'];
    $caminhoImagemPerfil = $dadosEmpresa['Img_Perfil'];
    $caminhoImagemBanner = $dadosEmpresa['Img_Banner'];
} else {
    // Se não houver informações da empresa, você pode definir valores padrão ou deixar em branco
    $nomeEmpresa = '';
    $areaEmpresa = '';
    $telefoneEmpresa = '';
    $sobreEmpresa = '';
    $facebookEmpresa = '';
    $githubEmpresa = '';
    $linkedinEmpresa = '';
    $instagramEmpresa = '';
    $caminhoBanner = '';
    $caminhoImagemPerfil = '';
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
    <title>Editar Perfil</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/editarStyles.css">
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
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>" class="btnVoltar"><img
                    src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Editar Perfil</h2>
        </div>
        <div class="divEdicaoPerfil">
            <form method="post" action="../../../src/services/Perfil/PerfilEmpresa.php?id=<?php echo $idPessoa; ?>"
                autocomplete="off" enctype="multipart/form-data">



                <div class="divBackgroundImg">
                    <div class="btnEditarFundo">
                        <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold"
                            colors="primary:#f5f5f5,secondary:#f5f5f5" style="width:34px;height:34px">
                        </lord-icon>
                    </div>
                    <input type="file" id="fundo_upload" name="fundo_upload" style="display: none;"
                        accept="image/jpeg, image/png">

                    <!-- Adicione um campo de entrada de arquivo -->
                    <input type="file" id="foto_perfil_upload" name="foto_perfil_upload" style="display: none;"
                        accept="image/jpeg, image/png">

                    <!-- Div de Foto de Perfil -->
                    <div class="divFotoDePerfil" id="divFotoDePerfil" style="position: relative;">
                        <!-- Div para exibir a imagem de perfil -->
                        <div id="preview_container"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;border-radius: 50%">
                        </div>
                        <!-- Div para o ícone de edição -->
                        <div class="divIconeEditar">
                            <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold"
                                colors="primary:#f5f5f5,secondary:#f5f5f5" style="width:110px;height:110px">
                            </lord-icon>
                        </div>
                    </div>

                </div>

                <div class="divContentPerfil">
                    <div class="divAvisoInicial">
                        <small>Há informações podem estar faltando!</small>
                    </div>
                    <form method="post" action="../../../src/services/Perfil/PerfilEmpresa.php" autocomplete="off">
                        <div class="inputsLadoALado">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="nome" name="nome" type="text" required
                                        value="<?php echo $nomeEmpresa; ?>">
                                    <div class="labelLine">Nome da Empresa</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="area" name="area" type="text" list="areaList"
                                        required value="<?php echo $areaEmpresa; ?>">
                                    <div class="labelLine">Área da Empresa</div>
                                    <datalist id="areaList">
                                        <?php
                                        foreach ($areas as $area) {
                                            echo "<option value='$area'>$area</option>";
                                        }
                                        ?>
                                    </datalist>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="inputsLadoALado">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="email" name="email" type="text" required
                                        value="<?php echo $emailUsuario; ?>">
                                    <div class="labelLine">Email</div>
                                </div>
                                <small id="aviso" name="aviso" style="display: none;">Caso altere o email é necessário
                                    realizar login novamente</small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" maxlength="15" id="telefone" name="telefone" type="text"
                                        required value="<?php echo $telefoneEmpresa; ?>">
                                    <div class="labelLine">Telefone</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="inputsLadoALado">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="facebook" name="facebook" type="text"
                                        value="<?php echo $facebookEmpresa; ?>">
                                    <div class="labelLine">Facebook</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="github" name="github" type="text"
                                        value="<?php echo $githubEmpresa; ?>">
                                    <div class="labelLine">GitHub</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="inputsLadoALado">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="linkedin" name="linkedin" type="text"
                                        value="<?php echo $linkedinEmpresa; ?>">
                                    <div class="labelLine">LinkedIn</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="instagram" name="instagram" type="text"
                                        value="<?php echo $instagramEmpresa; ?>">
                                    <div class="labelLine">Instagram</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divTextArea">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="sobre" id="sobre" type="text"
                                        required><?php echo $sobreEmpresa; ?></textarea>
                                    <div class="textArealabelLine">Sobre a Empresa</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divBtnAtualizar">
                            <input type="submit" value="Atualizar">
                        </div>

                        <form method="post" action="../../../src/services/ExcluirConta/excluirContaEmpresa.php">
                            <div style="    text-align: center;
    margin-top: 20px;
    cursor: pointer;
    border: 1px solid #c90000;
    font-size: 16pt;
    width: 200px;
    height: 50px;
    background-color: #ef0505;
    color: whitesmoke;
    border-radius: 14px;
    transition: 0.2s ease;
    box-shadow: 0px 0px 8px silver;
    align-content: center;
    margin-left: 35%;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        
                              <a class="acessarEditarPerfil" href="#" onclick="openModal('../../../src/services/ExcluirConta/excluirContaEmpresa.php?id=<?php echo $idPessoa; ?>')">Excluir Conta</a>

                                    <div style="padding: 6px 12px;
                    box-shadow: 0px 0px 6px silver;
                    display: flex;
                    align-items: center;
                    background-color: #830404; /* Corrigido o duplo # */
                    color: whitesmoke;
                    border-radius: 10px;
                    width: 9%;
                    margin-top: -3%;
                    margin-left: 89%;">
                <lord-icon src="https://cdn.lordicon.com/wpyrrmcq.json" trigger="hover" colors="primary:#ffffff"
                    style="width:30px;height:30px">
                </lord-icon>
                <label>Deletar</label>
            </div>
        </a>


    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirmação de Deleção</h2>
            <p>Você tem certeza de que deseja deletar esta vaga?</p>
            <button class="cancel-button" onclick="closeModal()">Cancelar</button>
            <a id="confirmDeleteButton" href="#" class="delete-button">Deletar</a>
        </div>
    </div>
                            </div>
                        </form>

                        <input type="hidden" name="email_usuario" value="<?php echo $emailUsuario; ?>">

                </div>

            </form>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <script src="mostraIcone.js"></script>
        <script src="avisoInicial.js"></script>
        <script src="adicionaElementos.js"></script>
        <script src="mascaras.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // Função para exibir o aviso quando o usuário alterar o campo de email
            function exibirAviso() {
                document.getElementById('aviso').style.display = 'block';
            }

            // Adicionar um evento de clique ao campo de email para chamar a função exibirAviso()
            document.getElementById('email').addEventListener('change', exibirAviso);
        </script>
        <?php
        // Obtém a URL da imagem de perfil do banco de dados
        $urlImagemPerfil = $dadosEmpresa['Img_Perfil'];
        ?>
        <script>
            $(document).ready(function () {
                // Adiciona um evento de clique ao botão de edição de foto de perfil
                $('#divFotoDePerfil').click(function () {
                    $('#foto_perfil_upload').click(); // Simula o clique no campo de upload de foto de perfil
                });

                // Evento disparado quando um arquivo é selecionado para a foto de perfil
                $('#foto_perfil_upload').change(function () {
                    var file = this.files[0];
                    var reader = new FileReader();

                    // Define o que fazer quando o arquivo é lido
                    reader.onload = function (e) {
                        $('#preview_container').css('background-image', 'url(' + e.target.result + ')'); // Define a imagem de fundo com a imagem carregada
                        $('#preview_container').show(); // Exibe o contêiner de pré-visualização
                    };

                    // Lê o arquivo como uma URL de dados
                    reader.readAsDataURL(file);
                });

                // Carrega a imagem de perfil ao carregar a página
                $('#preview_container').css('background-image', 'url(<?php echo $urlImagemPerfil; ?>)');
            });
        </script>
        <?php
        //
        $urlImagemFundo = $dadosEmpresa['Img_Banner'];
        ?>
        <script>
            $(document).ready(function () {
                // Função para carregar a imagem de fundo com base na URL fornecida
                function carregarImagemDeFundo(url) {
                    $('.divBackgroundImg').css('background-image', 'url(' + url + ')');
                }

                // Carrega a imagem de fundo ao carregar a página
                carregarImagemDeFundo('<?php echo $urlImagemFundo; ?>');

                // Adiciona um evento de clique ao botão de edição do fundo
                $('.btnEditarFundo').click(function () {
                    // Simula o clique no campo de upload de fundo
                    $('#fundo_upload').click();
                });

                // Evento disparado quando um arquivo é selecionado
                $('#fundo_upload').change(function () {
                    // Lê o arquivo selecionado
                    var file = this.files[0];
                    var reader = new FileReader();

                    // Define o que fazer quando o arquivo é lido
                    reader.onload = function (e) {
                        // Aplica a imagem de fundo à div
                        carregarImagemDeFundo(e.target.result);
                    };

                    // Lê o arquivo como uma URL de dados
                    reader.readAsDataURL(file);
                });
            });
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
                    Noturno(); // Adicione esta linha para atualizar imediatamente o tema na interface
                } else {
                    $("body").removeClass("noturno");
                    Claro(); // Adicione esta linha para atualizar imediatamente o tema na interface
                }

            });
        </script>

</body>

</html>