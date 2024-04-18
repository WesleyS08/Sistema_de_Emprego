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
$cursoUsuario = $_POST['habilidades']?? '';
$escolaridadeUsuario = $_POST['cursos']??'';
$experienciaUsuario = $_POST['experiencias']??'';
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


if ($email != $emailUsuario) {
    // Atualizar o email da pessoa na tabela Tb_Pessoas
    $queryAtualizarEmail = "UPDATE Tb_Pessoas SET Email = '$email', Token = NULL WHERE Email = '$emailUsuario'";

    if (mysqli_query($_con, $queryAtualizarEmail)) {
        // Envio do email de confirmação da alteração do email
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sias99029@gmail.com';
        $mail->Password = 'xamb nshs dbkq phui';
        $mail->Port = 587;

        $mail->setFrom('sias99029@gmail.com');
        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject = 'Confirmação de alteração de email';
        $mail->Body = 'Olá, seu email foi alterado com sucesso!';

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

            $mail->Body = 'Olá, Candidato! Obrigado por se inscrever em nosso sistema confirme o seu email!';
            $mail->Body .= '<br>Clique no link abaixo para ativar o seu cadastro.<br>';
            $link = 'http://localhost/Sistema_de_Emprego/src/views/EmailVerificado/emailVerificado.php?id=' . $idPessoa;
            $mail->Body .= '<a href="' . $link . '">Clique aqui para verificar seu cadastro</a>';
            
            $mail->AltBody = 'Olá, Candidato! Obrigado por se inscrever em nosso site e confiar em nosso sistema!';
            $mail->AltBody .= 'Clique no link abaixo para ativar o seu cadastro.';
            $mail->AltBody .= 'Link: ' . $link;

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
$query = "UPDATE Tb_Candidato SET Area_de_Interesse = '$areaUsuario', Idade = '$idade', Telefone = '$telefoneUsuario', Experiencia = '$experienciaUsuario', Escolaridade = '$escolaridadeUsuario', Cursos = '$cursoUsuario', Cidade = '$cidadeUsuario', Autodefinicao = '$autoDefinicaoUsuario', Genero = '$generoUsuario', Estado_Civil = '$estadoUsuario', Data_Nascimento = '$dataNascimentoMySQL', PCD = $pcdUsuario, Descricao = '$sobreUsuario', Img_Perfil = '$imagemPerfil', Banner = '$banner'  WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario')";

if (mysqli_query($_con, $query)) {
    header("Location: ../../views/Login/login.html?");
} else {
    echo "Erro ao salvar as alterações: " . mysqli_error($_con);
}
}


// Atualizar os dados do candidato no banco de dados
$query = "UPDATE Tb_Candidato SET Area_de_Interesse = '$areaUsuario', Idade = '$idade', Telefone = '$telefoneUsuario', Experiencia = '$experienciaUsuario', Escolaridade = '$escolaridadeUsuario', Cursos = '$cursoUsuario', Cidade = '$cidadeUsuario', Autodefinicao = '$autoDefinicaoUsuario', Genero = '$generoUsuario', Estado_Civil = '$estadoUsuario', Data_Nascimento = '$dataNascimentoMySQL', PCD = $pcdUsuario, Descricao = '$sobreUsuario', Img_Perfil = '$imagemPerfil', Banner = '$banner'  WHERE Tb_Pessoas_Id = (SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = '$emailUsuario')";

if (mysqli_query($_con, $query)) {
    header("Location: ../../views/PerfilCandidato/perfilCandidato.php?id=" . $idPessoa);
} else {
    echo "Erro ao salvar as alterações: " . mysqli_error($_con);
}

mysqli_close($_con);
?>