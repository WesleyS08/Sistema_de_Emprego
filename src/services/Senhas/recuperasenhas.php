<?php
// Inclui arquivos para conexão ao banco e PHPMailer
include "../conexão_com_banco.php";

// Inclui arquivos do PHPMailer
include "../../components/PHPMailer-master/src/PHPMailer.php";
include "../../components/PHPMailer-master/src/SMTP.php";
include "../../components/PHPMailer-master/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Verificar se o email está no banco de dados
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Gerar um token seguro
    function generateToken()
    {
        return bin2hex(random_bytes(32));  // 64 caracteres hexadecimais
    }

    $token = generateToken();

    // Obter o ID do usuário
    $user = $result->fetch_assoc();
    $idPessoa = $user['Id_Pessoas'];


    $sql = "UPDATE Tb_Pessoas SET note = ?, Codigo_Created_At = NOW() WHERE Id_Pessoas = ?";
    $stmt = $_con->prepare($sql);
    $stmt->bind_param("si", $token, $idPessoa);
    $stmt->execute();

    // Criar o link de redefinição de senha
    $resetLink = 'http://localhost/Sistema_de_Emprego/src/views/RedefinirSenha/redefinirSenha.php?token=' . $token;

    // Configurar o PHPMailer para enviar o email
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sias99029@gmail.com';
    $mail->Password = 'xamb nshs dbkq phui';
    $mail->SMTPSecure = 'tls'; // Adicione a segurança TLS
    $mail->Port = 587;

    $mail->setFrom('seu-email@gmail.com', 'SIAS'); // Nome e-mail do remetente
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'SIAS - Redefinição de senha';

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
               display: inline-block;
           }
           .footer { background-color: #f2f2f2; text-align: center; padding: 10px; }
           .content a { color: #ffffff; text-decoration: none; }
       </style>
    ";

    $mail->Body = "
       <html>
       <head>
           $styles
       </head>
       <body>
           <div class='header'>
               <h1>Redefinir Senha do SIAS!</h1>
           </div>
           <div class='content'>
               <p>Atenção!!!</p>
               <p>Aqui está o link para redefinir a sua senha!</p>
               <p>Para alterar a senha, clique no botão abaixo:</p>
               <a href='$resetLink' class='button'>Redefinir senha</a>
           </div>
           <div class='footer'>
               <p>Se você não solicitou esta redefinição, por favor ignore este e-mail.</p>
               <p>© 2024 SIAS - Todos os direitos reservados.</p>
           </div>
       </body>
       </html>
    ";

    if ($mail->send()) {
        header("Location: ../../views/AvisoVerificaEmail/avisoVerificaEmail.html");
        exit(); // Certifique-se de sair após o redirecionamento
    } else {
        echo "Erro ao enviar e-mail: " . $mail->ErrorInfo;
    }

} else {
    echo "Email não encontrado.";
}
?>