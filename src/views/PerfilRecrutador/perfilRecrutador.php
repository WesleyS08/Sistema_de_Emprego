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
    $autenticadoComoEmpresa = true;

} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    // Se estiver autenticado com o Google e for do tipo empresa
    $emailUsuario = $_SESSION['google_session'];
    $autenticadoComoEmpresa = true;
} else {
    $autenticadoComoEmpresa = false;
    exit;
}


// Recuperar informações da empresa do banco de dados com base no e-mail do usuário
$query = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario')";
$result = mysqli_query($_con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $dadosEmpresa = mysqli_fetch_assoc($result);
    // Preencher os campos HTML com as informações recuperadas do banco de dados
    $nomeEmpresa = $dadosEmpresa['Nome_da_Empresa'];
    $areaEmpresa = $dadosEmpresa['Area_da_Empresa'];
    $sobreEmpresa = $dadosEmpresa['Sobre_a_Empresa'];
    $telefoneEmpresa = $dadosEmpresa['Telefone'];
    $facebookEmpresa = $dadosEmpresa['Facebook'];
    $linkedinEmpresa = $dadosEmpresa['Linkedin'];
    $instagramEmpresa = $dadosEmpresa['Instagram'];
    $caminhoImagemPerfil = $dadosEmpresa['Img_Perfil'];
    $caminhoImagemBanner = $dadosEmpresa['Img_Banner'];
} else {
    // Se não houver informações da empresa, você pode definir valores padrão ou deixar em branco
    $nomeEmpresa = '';
    $areaEmpresa = '';
    $sobreEmpresa = '';
    $telefoneEmpresa = '';
    $facebookEmpresa = '';
    $linkedinEmpresa = '';
    $instagramEmpresa = '';
    $caminhoImagemPerfil = '';
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/editarStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/perfilStyle.css">
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
    <div class="divBackgroundImg" id="divBackgroundImgDefinida">
        <!-- Exibir o banner da empresa -->
        <img src="<?php echo $caminhoImagemBanner; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">

        <!-- Exibir a imagem de perfil da empresa -->
        <div class="divFotoDePerfil" id="divFotoDePerfilDefinida">
            <img src="<?php echo $caminhoImagemPerfil; ?>" alt=""
                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        </div>
        <?php if ($autenticadoComoEmpresa) { ?>
            <a class="acessarEditarPerfil" href="../EditarPerfilRecrutador/editarPerfilRecrutador.php">
                <div>
                    <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold" state="hover-line"
                        colors="primary:#ffffff,secondary:#ffffff" style="width:30px;height:30px">
                    </lord-icon>
                    <label>Editar</label>
                </div>
            </a>
        <?php } ?>
    </div>
    <div class="divCommon">
        <div class="containerPerfil">
            <div class="divTitulo">
                <h2 id="nomeUsuario"><?php echo $nomeEmpresa; ?></h2>

            </div>
            <div class="contentPerfil">
                <h3>Área da Empresa</h3>
                <p id="areaUsuario"><?php echo $areaEmpresa; ?></p>
            </div>
            <div class="contentPerfil" id="contentSobre">
                <fieldset>
                    <legend>
                        <h3>Sobre</h3>
                    </legend>
                    <p id="sobre"><?php echo $sobreEmpresa; ?>

                </fieldset>
            </div>
            <div class="contentPerfil">
                <h3>Contato</h3>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/nzixoeyk.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="email"><?php echo $emailUsuario; ?></a>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/rsvfayfn.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="telefone"><?php echo $telefoneEmpresa; ?></a>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="Instagram" href="https://www.instagram.com/<?php echo $instagramEmpresa; ?>">Instagram/<?php echo $instagramEmpresa; ?></a>


                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="linkedin" href="https://www.linkedin.com/<?php echo $linkedinEmpresa; ?>">linkedin/<?php echo $linkedinEmpresa; ?></a>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="facebook" href="https://web.facebook.com/<?php echo $facebookEmpresa; ?>"style="text-decoration: none;">facebook/<?php echo $facebookEmpresa; ?></a>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="acumuloDePontos.js"></script>
</body>

</html>