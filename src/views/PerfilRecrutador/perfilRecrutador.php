<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';
$empresa = false;
$podeEditar = false;
$idPessoa = isset($_GET['id']) ? $_GET['id'] : '';
$autenticadoComoPublicador = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    // Se estiver autenticado com o Google
    $emailUsuario = $_SESSION['google_session'];
}
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $emailUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $idPessoaAtiva = $result->fetch_assoc()['Id_Pessoas'];
    } else {
        die("Erro: Usuário não encontrado.");
    }
    $stmt->close();
} else {
    die("Erro ao preparar a consulta para obter o ID da pessoa.");
}

// Consulta SQL para obter o ID do usuário como empresa
$sql = "SELECT e.Tb_Pessoas_Id AS idUsuario FROM Tb_Pessoas AS p
        INNER JOIN Tb_Candidato AS e ON p.Id_Pessoas = e.Tb_Pessoas_Id
        WHERE p.Email = ?";
$stmt = $_con->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $emailUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o usuário é uma empresa
    if ($result->num_rows > 0) {
        $idUsuarioEmpresa = $result->fetch_assoc()['idUsuario'];
        $candidato = true;
    } else {
        $candidato = false;
    }
    $stmt->close();
} else {
    die("Erro ao preparar a consulta para obter o ID do usuário como empresa.");
}

// Consulta para obter o tema da pessoa
$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $idPessoaAtiva);
    $stmt->execute();

    // Verificar resultado
    $result = $stmt->get_result();
    $tema = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['Tema'] : null;

    $stmt->close();
} else {
    die("Erro ao preparar a consulta para obter o tema.");
}

// Consulta para obter informações da empresa
$query = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
$stmt = $_con->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $idPessoa);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $empresa = $result->fetch_assoc();

        // Extrair valores da empresa
        $CNPJ = isset($empresa['CNPJ']) ? $empresa['CNPJ'] : '';
        $nomeEmpresa = isset($empresa['Nome_da_Empresa']) ? $empresa['Nome_da_Empresa'] : '';
        $areaEmpresa = isset($empresa['Area_da_Empresa']) ? $empresa['Area_da_Empresa'] : '';
        $sobreEmpresa = isset($empresa['Sobre_a_Empresa']) ? $empresa['Sobre_a_Empresa'] : '';
        $telefoneEmpresa = isset($empresa['Telefone']) ? $empresa['Telefone'] : '';
        $facebookEmpresa = isset($empresa['Facebook']) ? $empresa['Facebook'] : '';
        $linkedinEmpresa = isset($empresa['Linkedin']) ? $empresa['Linkedin'] : '';
        $instagramEmpresa = isset($empresa['Instagram']) ? $empresa['Instagram'] : '';
        $caminhoImagemPerfil = isset($empresa['Img_Perfil']) ? $empresa['Img_Perfil'] : '';
        $caminhoImagemBanner = isset($empresa['Img_Banner']) ? $empresa['Img_Banner'] : '';
    }
    $stmt->close();
} else {
    die("Erro ao preparar a consulta para obter informações da empresa.");
}

// Verificar se o usuário pode editar
$query = "SELECT Email FROM Tb_Pessoas WHERE Id_Pessoas = (SELECT Tb_Pessoas_Id FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?)";
$stmt = $_con->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $idPessoa);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $emailUsuarioBanco = $result->fetch_assoc()['Email'];
        $podeEditar = ($emailUsuario == $emailUsuarioBanco);
    }
    $stmt->close();
} else {
    die("Erro ao preparar a consulta para verificar a permissão de edição.");
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
        <?php if ($candidato == true) { ?>
            <a href="../HomeRecrutador/homeRecrutador.php"><img id="logo"
                    src="../../assets/images/logos_empresa/logo_sias.png"></a>
            <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
            <ul>
                <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
                <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
                <li><a href="../Cursos/cursos.php">Cursos</a></li>
                <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoaAtiva; ?>">Perfil</a></li>
            </ul>
        <?php } else { ?>
            <a href="../homeCandidato/homeCandidato.php"><img id="logo"
                    src="../../assets/images/logos_empresa/logo_sias.png"></a>
            <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
            <ul>
                <li><a href="../CriarVaga/criarVaga.php">Anunciar</a></li>
                <li><a href="../MinhasVagas/minhasVagas.php">Minhas vagas</a></li>
                <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li><!-- Arrumar esse link -->
                <li><a href="../../../index.php">Deslogar</a></li>
                <li><a href="#">Perfil</a></li>
            </ul>
        <?php } ?>

    </nav>
    <div class="divBackgroundImg" id="divBackgroundImgDefinida">
        <?php
        if (!empty($caminhoImagemBanner)) { ?>
            <img src="<?php echo $caminhoImagemBanner; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
        <?php } else { ?>
            <img src="https://static.vecteezy.com/system/resources/previews/010/705/558/non_2x/orange-modern-abstract-background-for-banner-landing-page-poster-presentation-or-flyer-free-vector.jpg"
                alt="" style="width: 100%; height: 100%; object-fit: cover;">
        <?php } ?>

        <!-- Exibir a imagem de perfil da empresa -->
        <div class="divFotoDePerfil" id="divFotoDePerfilDefinida">
            <img src="<?php echo $caminhoImagemPerfil; ?>" alt=""
                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        </div>
        <?php if ($podeEditar) { ?>
            <a class="acessarEditarPerfil"
                href="../EditarPerfilRecrutador/editarPerfilRecrutador.php?id=<?php echo $idPessoa; ?>">
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
                    <lord-icon src="https://cdn.lordicon.com/nzixoeyk.json" class="iconeVaga" trigger="hover"
                        colors="primary:#000000" style="width:30px;height:30px">
                    </lord-icon>
                    <?php
                    if ($emailUsuario) {
                        echo '<a id="email" class="infos">' . $emailUsuario . '</a>'; // Não abra um bloco PHP dentro de um echo
                    } else {
                        echo '<span class="infos">Sem email salvo</span>'; // Feche o bloco PHP corretamente
                    }
                    ?>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/rsvfayfn.json" class="iconeVaga" trigger="hover"
                        colors="primary:#000000" style="width:30px;height:30px">
                    </lord-icon>

                    <?php
                    if ($telefoneEmpresa) {
                        echo '<a id="telefone" class="infos">' . $telefoneEmpresa . '</a>';
                    } else {
                        echo '<span class="infos">Sem número salvo</span>';
                    }
                    ?>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" class="iconeVaga" trigger="hover"
                        colors="primary:#000,secondary:#ec6809" style="width:30px;height:30px">
                    </lord-icon>

                    <?php
                    if ($instagramEmpresa) { // Certifique-se de que a condição está correta
                        echo '<a id="Instagram" class="infos" href="https://www.instagram.com/' . $instagramEmpresa . '" style="text-decoration: none;">Instagram/' . $instagramEmpresa . '</a>';
                    } else {
                        echo '<span class="infos">Sem conta no Instagram</span>';
                    }
                    ?>

                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" class="iconeVaga" trigger="hover"
                        colors="primary:#000,secondary:#ec6809" style="width:30px;height:30px">
                    </lord-icon>

                    <?php
                    if ($linkedinEmpresa) { // Certifique-se de que a condição está correta
                        echo '<a id="linkedin" class="infos" href="https://www.linkedin.com' . $linkedinEmpresa . '" style="text-decoration: none;">linkedin/' . $linkedinEmpresa . '</a>';
                    } else {
                        echo '<span class="infos">Sem conta no linkedin</span>';
                    }
                    ?>
                </div>
                <div class="divFlexContato">
                    <lord-icon src="https://cdn.lordicon.com/jdsvypqr.json" class="iconeVaga" trigger="hover"
                        colors="primary:#000,secondary:#ec6809" style="width:30px;height:30px">
                    </lord-icon>
                    <?php
                    if ($facebookEmpresa) { // Certifique-se de que a condição está correta
                        echo '<a id="facebook" class="infos" href="https://web.facebook.com/' . $facebookEmpresa . '" style="text-decoration: none;">Facebook/' . $facebookEmpresa . '</a>';
                    } else {
                        echo '<span class="infos">Sem conta no Facebook</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <footer>
    <a href="../PoliticadePrivacidade/PoliticadePrivacidade.html">Política de Privacidade</a>
        <a href="../NossoContato/nossoContato.html">Nosso contato</a>
        <a href="../AvalieNos/avalieNos.php">Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="acumuloDePontos.js"></script>
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