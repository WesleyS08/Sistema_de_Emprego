<?php
include "../../services/conexão_com_banco.php";
include "../../components/PHPMailer-master/src/PHPMailer.php";
include "../../components/PHPMailer-master/src/SMTP.php";
include "../../components/PHPMailer-master/src/Exception.php";

use PHPMAILER\PHPMAILER\PHPMAILER;
use PHPMAILER\PHPMAILER\SMTP;
use PHPMAILER\PHPMAILER\EXCEPTION;

$mail = new PHPMailer(true);

// Função para gerar um nome único para o arquivo de imagem
function gerarNomeUnico($nomeOriginal)
{
    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
    $nomeUnico = uniqid() . '.' . $extensao;
    return $nomeUnico;
}

// Recuperar informações do formulário
$nomeUsuario = $_POST['nome'] ?? '';
$areaUsuario = $_POST['area'] ?? '';
$autoDefinicaoUsuario = $_POST['breveDescricao'] ?? '';
$emailUsuario = $_POST['email_usuario'] ?? '';
$telefoneUsuario = $_POST['telefone'] ?? '';
$dataNascimentoUsuario = $_POST['data'] ?? '';
$generoUsuario = $_POST['genero'] ?? '';
$estadoUsuario = $_POST['estado'] ?? '';
$cidadeUsuario = $_POST['cidade'] ?? '';
$sobreUsuario = $_POST['sobre'] ?? '';
$cursoUsuario = $_POST['habilidades'] ?? '';
$escolaridadeUsuario = $_POST['cursos'] ?? '';
$experienciaUsuario = $_POST['experiencias'] ?? '';
$pcdUsuario = isset($_POST['pcd']) ? 1 : 0; // 1 se a opção foi marcada, 0 se não foi
$email = $_POST['email'] ?? '';

$idPessoa = isset($_GET['id']) ? $_GET['id'] : '';

/// Verifica se a data de nascimento foi fornecida
if (!empty($dataNascimentoUsuario)) {
    // Converte a data de nascimento em um objeto DateTime
    $dataNascimentoformat = DateTime::createFromFormat('d/m/Y', $dataNascimentoUsuario);

    // Verifica se a conversão foi bem-sucedida
    if ($dataNascimentoformat !== false) {
        $dataNascimentoMySQL = $dataNascimentoformat->format('Y-m-d');
        // Calcula a diferença entre a data de nascimento e a data atual
        $idade = $dataNascimentoformat->diff(new DateTime())->y;

    } else {
        echo "Formato de data inválido";
    }
} else {
    echo "A data de nascimento não foi fornecida";
}

// Verificar se todas as informações necessárias foram fornecidas
if (empty($nomeUsuario) || empty($areaUsuario) /*empty($telefoneUsuario) Colocar no banco futuramente*/ || empty($sobreUsuario) || empty($emailUsuario)) {
    echo "Erro: Preencha todos os campos obrigatórios.";
    exit;
}

// Obter o CPF do candidato
$query = "SELECT CPF FROM Tb_Candidato WHERE  Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?)";
$stmt = mysqli_prepare($_con, $query);
mysqli_stmt_bind_param($stmt, "s", $emailUsuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $userID = isset($row['CPF']) ? $row['CPF'] : null;
}

// Armazenar os URLs das imagens existentes
$queryImagens = "SELECT Img_Perfil, Banner FROM Tb_Candidato WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario')";
$resultImagens = mysqli_query($_con, $queryImagens);
$dadosImagens = mysqli_fetch_assoc($resultImagens);
$urlImagemPerfilAntiga = $dadosImagens['Img_Perfil'];
$urlBannerAntigo = $dadosImagens['Banner'];

// Processar o upload da nova imagem de perfil
$imagemPerfil = $urlImagemPerfilAntiga; // Definir a imagem de perfil como a existente por padrão
if ($_FILES['foto_perfil_upload']['error'] == UPLOAD_ERR_OK) {
    // Se o campo de upload de arquivo para a foto de perfil estiver preenchido
    $diretorioDestinoPerfil = '../../assets/imagensPerfil/';
    $nomeArquivoPerfil = 'perfil_' . $userID . '.' . pathinfo($_FILES['foto_perfil_upload']['name'], PATHINFO_EXTENSION); // Nome padrão para a imagem de perfil

    if (move_uploaded_file($_FILES['foto_perfil_upload']['tmp_name'], $diretorioDestinoPerfil . $nomeArquivoPerfil)) {
        $imagemPerfil = $diretorioDestinoPerfil . $nomeArquivoPerfil;

        // Verificar se a imagem de perfil foi atualizada
        if ($imagemPerfil != $urlImagemPerfilAntiga) {
            echo "A imagem de perfil foi atualizada com sucesso.";
        }
    } else {
        echo "Erro ao fazer upload do arquivo de imagem de perfil.";
        exit;
    }
}

// Processar o upload do banner
$banner = $urlBannerAntigo; // Definir o banner como o existente por padrão
if ($_FILES['fundo_upload']['error'] == UPLOAD_ERR_OK) {
    // Se o campo de upload de arquivo para o banner estiver preenchido
    $diretorioDestinoBanner = '../../assets/banners/';
    $nomeArquivoBanner = 'banner_' . $userID . '.' . pathinfo($_FILES['fundo_upload']['name'], PATHINFO_EXTENSION); // Nome padrão para o banner

    if (move_uploaded_file($_FILES['fundo_upload']['tmp_name'], $diretorioDestinoBanner . $nomeArquivoBanner)) {
        $banner = $diretorioDestinoBanner . $nomeArquivoBanner;

        // Verificar se o banner foi atualizado
        if ($banner != $urlBannerAntigo) {
            echo "O banner foi atualizado com sucesso.";
        }
    } else {
        echo "Erro ao fazer upload do arquivo de banner.";
        exit;
    }
}

// Atualizar o email da pessoa na tabela Tb_Pessoas, se houver alteração
if ($email != $emailUsuario) {
    $queryAtualizarEmail = "UPDATE Tb_Pessoas SET Email = '$email', Token = NULL WHERE Email = '$emailUsuario'";
    if (mysqli_query($_con, $queryAtualizarEmail)) {
        // Envio do email de confirmação da alteração do email
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sias99029@gmail.com';
        $mail->Password = 'xamb nshs dbkq phui'; // Considere usar uma variável de ambiente para proteger a senha
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls'; // Use 'tls' ou 'ssl' para conexão segura

        // Detalhes do e-mail
        $mail->setFrom('sias99029@gmail.com', 'SIAS');
        $mail->addAddress($email);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $link = 'http://localhost/Sistema_de_Emprego/src/views/EmailVerificado/emailVerificado.php?id=' . $idPessoa;
        $mail->Subject = 'Confirmação de alteração de email';

        // O corpo do e-mail corrigido
        $styles = "
        <style>
            body { font-family: Arial, sans-serif; }
            .header { background-color: #f2f2f2; padding: 10px; text-align: center; }
            .header h1 { margin: 0; }
            .content { padding: 20px; }
            .button { 
                background-color: #ec6809;
                color: #ffffff;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                font-size: 16px;
                border-radius: 5px;
                margin: 20px auto;
                display: block;
                width: 155px;
            }
            .footer { background-color: #f2f2f2; text-align: center; padding: 10px; }
            .content a {  color: #ffffff; }
        </style>
    ";

        $mail->Body = '
        <html>
        <head>
            ' . $styles . '
        </head>
        <body>
            <div class="header">
                <h1>SIAS! Confirmação de Alterações na conta</h1>
            </div>
            <div class="content">
                <p>Olá, Candidato, ' . htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') . '!</p>
                <p>Informamos que seu e-mail foi alterado com sucesso!</p>     
            </div>
            <div class="footer">
                <p>Se você não se inscreveu, ignore este e-mail.</p>
                <p>© 2024 SIAS - Todos os direitos reservados</p>
            </div>
        </body>
        </html>
    ';

        if (!$mail->send()) {
            echo "Erro ao enviar o email de confirmação: " . $mail->ErrorInfo;
            exit;
        }

        // Verificar se o campo "Verificado" está vazio
        $queryVerificarVerificado = "SELECT Verificado FROM Tb_Pessoas WHERE Email = '$email'";
        $resultVerificado = mysqli_query($_con, $queryVerificarVerificado);
        $row = mysqli_fetch_assoc($resultVerificado);
        $verificado = $row['Verificado'];

        if (empty($verificado)) {
            $mail->clearAddresses();
            $mail->addAddress($email);
            $mail->Body = $mail->Body = "
                <html>
                <head>
                    $styles
                </head>
                <body>
                    <div class='header'>
                        <h1>Bem-vindo ao SIAS!</h1>
                    </div>
                    <div class='content'>
                        <p'>Olá, Candidato, $nomeUsuario!</p>
                        <p>Obrigado por se inscrever em nosso sistema!</p>
                        <p>Para ativar seu cadastro, clique no botão abaixo:</p>
                        <a href='$link' class='button'>Ativar Cadastro</a>
                    </div>
                    <div class='footer'>
                        <p>Se você não se inscreveu, ignore este e-mail.</p>
                        <p>© 2024 SIAS - Todos os direitos reservados</p>
                    </div>
                </body>
                </html>
            ";

            $mail->AltBody = "Olá, Candidato!\n\nPara ativar seu cadastro, clique no seguinte link:\n$link";

            if (!$mail->send()) {
                echo "Erro ao enviar o email de verificação: " . $mail->ErrorInfo;
                exit;
            }
        }
    } else {
        echo "Erro ao atualizar o email da pessoa: " . mysqli_error($_con);
        exit;
    }
    // Atualizar os dados do candidato no banco de dados
    $query = "UPDATE Tb_Candidato AS c
    JOIN Tb_Pessoas AS p ON c.Tb_Pessoas_Id = p.Id_Pessoas
    SET 
        c.Area_de_Interesse = '$areaUsuario',
        c.Idade = '$idade',
        c.Telefone = '$telefoneUsuario',
        c.Experiencia = '$experienciaUsuario',
        c.Escolaridade = '$escolaridadeUsuario',
        c.Cursos = '$cursoUsuario',
        c.Cidade = '$cidadeUsuario',
        c.Autodefinicao = '$autoDefinicaoUsuario',
        c.Genero = '$generoUsuario',
        c.Estado_Civil = '$estadoUsuario',
        c.Data_Nascimento = '$dataNascimentoMySQL',
        c.PCD = $pcdUsuario,
        c.Descricao = '$sobreUsuario',
        c.Img_Perfil = '$imagemPerfil',
        c.Banner = '$banner',
        p.Nome = '$nomeUsuario'
    WHERE p.Email = '$email';";
    if (mysqli_query($_con, $query)) {
        header("Location: ../../views/Login/login.html?");
    } else {
        echo "Erro ao salvar as alterações: " . mysqli_error($_con);
    }

    mysqli_close($_con);
}

// Atualizar os dados do candidato no banco de dados
$query = "UPDATE Tb_Candidato AS c
    JOIN Tb_Pessoas AS p ON c.Tb_Pessoas_Id = p.Id_Pessoas
    SET 
        c.Area_de_Interesse = '$areaUsuario',
        c.Idade = '$idade',
        c.Telefone = '$telefoneUsuario',
        c.Experiencia = '$experienciaUsuario',
        c.Escolaridade = '$escolaridadeUsuario',
        c.Cursos = '$cursoUsuario',
        c.Cidade = '$cidadeUsuario',
        c.Autodefinicao = '$autoDefinicaoUsuario',
        c.Genero = '$generoUsuario',
        c.Estado_Civil = '$estadoUsuario',
        c.Data_Nascimento = '$dataNascimentoMySQL',
        c.PCD = $pcdUsuario,
        c.Descricao = '$sobreUsuario',
        c.Img_Perfil = '$imagemPerfil',
        c.Banner = '$banner',
        p.Nome = '$nomeUsuario'
    WHERE p.Email = '$email';";

if (mysqli_query($_con, $query)) {
    header("Location: ../../views/PerfilCandidato/perfilCandidato.php?id=" . $idPessoa);
} else {
    echo "Erro ao salvar as alterações: " . mysqli_error($_con);
}

mysqli_close($_con);
?>