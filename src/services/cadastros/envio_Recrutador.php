<?php
// Inclui arquivos necessários para PHPMailer
include "../../components/PHPMailer-master/src/PHPMailer.php";
include "../../components/PHPMailer-master/src/SMTP.php";
include "../../components/PHPMailer-master/src/Exception.php";
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
// Verificar se todos os parâmetros necessários foram passados
if (!isset($_GET['userId']) || !isset($_GET['emailRecrutador'])) {
    echo "Parâmetros insuficientes fornecidos.";
    exit();
}
 
// Obtenha o ID do usuário e o endereço de e-mail do recrutador do parâmetro GET
$userId = $_GET['userId'];
$emailRecrutador = $_GET['emailRecrutador'];
 
$mail = new PHPMailer(true);
 
try {
    // Configurações do PHPMailer
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sias99029@gmail.com';
    $mail->Password = 'xamb nshs dbkq phui';
    $mail->Port = 587;
 
    // Configurações do e-mail
    $mail->setFrom('sias99029@gmail.com');
    $mail->addAddress($emailRecrutador);
 
    // Restante do código para enviar o e-mail...
// Restante do código para enviar o e-mail...
    // Obter o host do servidor
    $host = $_SERVER['HTTP_HOST'];
 
    // Obter o diretório do arquivo atual (normalizando as barras)
    $currentPath = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__);
 
    // Obter o caminho para voltar duas pastas
    $twoDirsUp = dirname(dirname($currentPath));
 
    // Calcular o caminho relativo ao documento raiz
    $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $twoDirsUp);
 
    // Construir a URL base
    $baseURL = "http://$host$relativePath";
 
    // Função para gerar URLs a partir do baseURL
    function generateUrl($relativePathPart)
    {
        global $baseURL;
        return rtrim($baseURL, '/') . '/' . ltrim($relativePathPart, '/');
    }
 
    // Gerar a URL para o arquivo específico após sair duas pastas
    $link = generateUrl('views/EmailVerificado/emailVerificado.php?id=' . $userId);
 
    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'SIAS - Email de autenticação';
    $mail->CharSet = 'UTF-8';
 
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
            .content a {  color: #ffffff;}
        </style>
    ";
 
    $mail->Body = "
        <html>
        <head>
            $styles
        </head>
        <body>
            <div class='header'>
                <h1>Bem-vindo ao SIAS!</h1>
            </div>
            <div class='content'>
                <p'>Olá, recrutador!</p>
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
 
    $mail->AltBody = "Olá, recrutador!\n\nPara ativar seu cadastro, clique no seguinte link:\n$link";
 
    // Enviar e-mail
    if ($mail->send()) {
        header("Location: ../../views/AvisoVerificaEmail/avisoVerificaEmail.html");
        exit();
    } else {
        echo 'Erro ao enviar e-mail.';
    }
} catch (Exception $e) {
    echo "Erro grave ao enviar e-mail: {$mail->ErrorInfo}";
}
?>