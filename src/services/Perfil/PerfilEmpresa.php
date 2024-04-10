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
function gerarNomeUnico($nomeOriginal) {
    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
    $nomeUnico = uniqid() . '.' . $extensao;
    return $nomeUnico;
}

// Recuperar informações do formulário
$nomeEmpresa = $_POST['nome'] ?? '';
$areaEmpresa = $_POST['area'] ?? '';
$telefoneEmpresa = $_POST['telefone'] ?? '';
$sobreEmpresa = $_POST['sobre'] ?? '';
$facebookEmpresa = $_POST['facebook'] ?? '';
$githubEmpresa = $_POST['github'] ?? '';
$linkedinEmpresa = $_POST['linkedin'] ?? '';
$instagramEmpresa = $_POST['instagram'] ?? '';
$emailUsuario = $_POST['email_usuario'] ?? ''; // Verifique se o email do usuário está presente

// Verificar se todas as informações necessárias foram fornecidas
if (empty($nomeEmpresa) || empty($areaEmpresa) || empty($telefoneEmpresa) || empty($sobreEmpresa) || empty($emailUsuario)) {
    echo "Erro: Preencha todos os campos obrigatórios.";
    exit;
}
// Obter o CNPJ da empresa
$query = "SELECT CNPJ FROM Tb_Empresa WHERE  Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?)";
$stmt = mysqli_prepare($_con, $query);
mysqli_stmt_bind_param($stmt, "s", $emailUsuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $userID = $row['CNPJ'];
}

// Armazenar os URLs das imagens existentes
$urlImagemPerfilAntiga = $dadosEmpresa['Img_Perfil'];
$urlBannerAntigo = $dadosEmpresa['Banner'];

// Processar o upload da nova imagem de perfil
$imagemPerfil = $imagemPerfilExistente; // Definir a imagem de perfil como a existente por padrão
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
$banner = $bannerExistente; // Definir o banner como o existente por padrão
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



// Atualizar o email da pessoa na tabela Tb_Pessoas
$queryAtualizarEmail = "UPDATE Tb_Pessoas SET Email = '$emailUsuario' WHERE Email = '$emailUsuario'";

if (mysqli_query($_con, $queryAtualizarEmail)) {
    // Verificar se o campo "Verificado" está vazio
    $queryVerificarVerificado = "SELECT Verificado FROM Tb_Pessoas WHERE Email = '$emailUsuario'";
    $resultVerificado = mysqli_query($_con, $queryVerificarVerificado);
    $row = mysqli_fetch_assoc($resultVerificado);
    $verificado = $row['Verificado'];

    if (empty($verificado)) {
        // Envio do email de confirmação
        $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sias99029@gmail.com';
            $mail->Password = 'xamb nshs dbkq phui';
            $mail->Port = 587;
        
            $mail->setFrom('sias99029@gmail.com');
            $mail->addAddress($emailUsuario); // Jamais colocar o $emailRecrutador entre aspas
        
            $mail->isHTML(true);
            
        $mail->Subject = 'Email de confirmação';
        $mail->Body = 'Olá, seu e-mail foi atualizado com sucesso!'; // Corpo do e-mail

        if (!$mail->send()) {
            echo "Erro ao enviar o e-mail de confirmação: " . $mail->ErrorInfo;
            exit;
        }
    }

    // Continuar com a atualização dos dados da empresa
    // ...

} else {
    echo "Erro ao atualizar o email da pessoa: " . mysqli_error($_con);
    exit;
}

// Atualizar os dados da empresa no banco de dados
$query = "UPDATE Tb_Empresa SET Nome_da_Empresa = '$nomeEmpresa', Area_da_Empresa = '$areaEmpresa', Telefone = '$telefoneEmpresa', Sobre_a_Empresa = '$sobreEmpresa', Facebook = '$facebookEmpresa', Github = '$githubEmpresa', Linkedin = '$linkedinEmpresa', Instagram = '$instagramEmpresa', Img_Perfil = '$imagemPerfil', Img_Banner='$banner' WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario')";

if (mysqli_query($_con, $query)) {
    header("Location: ../../views/PerfilRecrutador/perfilRecrutador.php");
} else {
    echo "Erro ao salvar as alterações: " . mysqli_error($_con);
}

mysqli_close($_con);
?>