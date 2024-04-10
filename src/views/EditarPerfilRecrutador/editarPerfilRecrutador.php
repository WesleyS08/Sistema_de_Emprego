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
            <li><a href="#">Anunciar</a></li>
            <li><a href="#">Minhas vagas</a></li>
            <li><a href="#">Meus testes</a></li>
            <li><a href="../PerfilRecrutador/perfilRecrutador.php">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <button class="btnVoltar" onclick="window.location.href='../PerfilRecrutador/perfilRecrutador.php'">
                <</button>
                    <h2>Editar Perfil</h2>
        </div>
        <div class="divEdicaoPerfil">
            <form method="post" action="../../../src/services/Perfil/PerfilEmpresa.php" autocomplete="off"
                enctype="multipart/form-data">


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
                                        <option>Tecnologia da Informação</option>
                                        <option>Medicia</option>
                                        <option>Engenharia</option>
                                        <option>Construção Civil</option>
                                        <option>Educação</option>
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
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="telefone" name="telefone" type="number" required
                                        value="<?php echo $telefoneEmpresa; ?>">
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
                        <input type="hidden" name="email_usuario" value="<?php echo $emailUsuario; ?>">

                </div>
            </form>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <script src="mostraIcone.js"></script>
        <script src="avisoInicial.js"></script>
        <script src="adicionaElementos.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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


</body>

</html>