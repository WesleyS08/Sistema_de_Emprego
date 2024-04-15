<?php
include "../../services/conexão_com_banco.php";

session_start();

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// Verificar se o usuário está autenticado como empresa
$autenticadoComoPublicador = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa';

// Definição de variáveis 
$emailUsuario = '';
$podeEditar = false; // Inicialize $podeEditar como false

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha e for do tipo candidato
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    // Se estiver autenticado com o Google e for do tipo candidato
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Verificação para a possível edição 
    $autenticadoComoEmpresa = false;
}

// Recuperar o nome da empresa da URL (supondo que seja passado como parâmetro "nomeEmpresa")
$nomeEmpresa = isset($_GET['empresa_nome']) ? $_GET['empresa_nome'] : '';

// Evite inserção direta de variáveis na consulta SQL para prevenir injeção de SQL
// Use declarações preparadas para evitar problemas de segurança
$query = "SELECT * FROM Tb_Empresa WHERE Nome_da_Empresa = ?";
$stmt = mysqli_prepare($_con, $query);

if ($stmt) {
    // Vincular parâmetros
    mysqli_stmt_bind_param($stmt, "s", $nomeEmpresa);

    // Executar a consulta
    mysqli_stmt_execute($stmt);

    // Obter o resultado
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $dadosEmpresa = mysqli_fetch_assoc($result);
        // Preencher os campos HTML com as informações recuperadas do banco de dados
        $CNPJ = $dadosEmpresa['CNPJ'];
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

    // Fechar declaração
    mysqli_stmt_close($stmt);
}

$query = "SELECT Email FROM Tb_Pessoas WHERE Id_Pessoas = (SELECT Tb_Pessoas_Id FROM Tb_Empresa WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario'))";
$result = mysqli_query($_con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $dadosUsuario = mysqli_fetch_assoc($result);
    $emailUsuarioBanco = $dadosUsuario['Email'];

    // Verificar se o usuário tem permissão para editar
    if ($emailUsuario == $emailUsuarioBanco) {
        $podeEditar = true;
    }
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
            <li><a href="../EditarPerfilRecrutador/editarPerfilRecrutador.php">Perfil</a></li>

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
        <?php if ($podeEditar) { ?>
            <a class="acessarEditarPerfil"
                href="../EditarPerfilRecrutador/editarPerfilRecrutador.php?cnpj=<?php echo urlencode($CNPJ); ?>">
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
                    <a id="Instagram"
                        href="https://www.instagram.com/<?php echo $instagramEmpresa; ?>">Instagram/<?php echo $instagramEmpresa; ?></a>


                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="linkedin"
                        href="https://www.linkedin.com/<?php echo $linkedinEmpresa; ?>">linkedin/<?php echo $linkedinEmpresa; ?></a>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" trigger="hover" colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="facebook" href="https://web.facebook.com/<?php echo $facebookEmpresa; ?>"
                        style="text-decoration: none;">facebook/<?php echo $facebookEmpresa; ?></a>
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