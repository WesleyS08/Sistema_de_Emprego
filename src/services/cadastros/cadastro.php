<?php
include "../conexão_com_banco.php";

include "../../components/PHPMailer-master/src/PHPMailer.php";
include "../../components/PHPMailer-master/src/SMTP.php";
include "../../components/PHPMailer-master/src/Exception.php";

use PHPMAILER\PHPMAILER\PHPMAILER;
use PHPMAILER\PHPMAILER\SMTP;
use PHPMAILER\PHPMAILER\EXCEPTION;

$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Autenticação do usuário candidato

    $nomeUsuario = $_POST['nome'];
    $emailUsuario = $_POST['email'];
    $cpfUsuario = $_POST['cpf'];
    $senhaUsuario = $_POST['senha'];

    // Criptografia da senha

    $senhaCriptografada = sha1($senhaUsuario);

    // Verificar se o CPF já existe na tabela de candidatos

    $checkCPF = $_con->prepare("SELECT COUNT(*) AS total FROM tb_candidato WHERE CPF = ?");
    $checkCPF->bind_param("s", $cpfUsuario);
    $checkCPF->execute();
    $cpfExists = $checkCPF->get_result()->fetch_assoc()['total'];

    // Verificar se Email existe na tabela

    $checkEmail = $_con->prepare("SELECT COUNT(*) AS total FROM tb_pessoas WHERE Email = ?");
    $checkEmail->bind_param("s", $emailUsuario);
    $checkEmail->execute();
    $emailExists = $checkEmail->get_result()->fetch_assoc()['total'];

    if ($cpfExists > 0) {
        echo "Erro ao inserir registro: CPF já existe.";        
    } else {
        if($emailExists > 0) {
            echo "Erro ao inserir registro: Email já cadastrado.";
        } else {

            // Iniciar a transação
            $_con->begin_transaction();

            // Prevenir SQL Injection usando prepared statements
            $stmt1 = $_con->prepare("INSERT INTO tb_pessoas (Nome, Email, Senha) VALUES (?, ?, ?)");
            $stmt1->bind_param("sss", $nomeUsuario, $emailUsuario, $senhaCriptografada);
            
            // Executar as instruções SQL dentro da transação
            $stmt1->execute();

            // Obtenção do Id do usuário inserido na última tabela (tb_pessoas)
            $userId = $_con->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];
            
            $stmt2 = $_con->prepare("INSERT INTO tb_candidato (CPF, Tb_Pessoas_Id) VALUES (?, ?)");
            $stmt2->bind_param("si", $cpfUsuario, $userId);

            $stmt2->execute();

            //////////////////////

            // Verificar se ambas as inserções foram bem-sucedidas
            if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
                // Confirmar a transação
                $_con->commit();
                echo "Registro inserido com sucesso!";            
            } else {
                // Desfazer a transação em caso de erro
                $_con->rollback();
                echo "Erro ao inserir registro. Por favor, tente novamente.";
            }

            // Fechar as declarações preparadas
            $stmt1->close();
            $stmt2->close();

            // Envio do email de confirmação        

            try {
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER; Não é necessário, apenas para testes
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'sias99029@gmail.com';
                $mail->Password = 'xamb nshs dbkq phui';
                $mail->Port = 587;
            
                $mail->setFrom('sias99029@gmail.com');
                $mail->addAddress($emailUsuario); // Jamais colocar o $emailUsuario entre aspas
                $link = 'http://localhost/Sistema_de_Emprego/src/views/EmailVerificado/emailVerificado.php?id=' . $userId;


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

                // Enviar e-mail
                if ($mail->send()) {
                    header("Location: ../../views/AvisoVerificaEmail/avisoVerificaEmail.html");
                    exit();
                } else {
                    echo 'Erro ao enviar e-mail.';
                }

                /*// Enviar e-mail
                if ($mail->send()) {
                    header("Location: ../../views/Login/login.html?cadastro=sucesso");
                    exit();
                } else {
                    echo 'Erro ao enviar e-mail.';
                }*/

            } catch (Exception $e) {
                echo "Erro grave ao enviar e-mail: {$mail->ErrorInfo}";
            }
        }
    }
} else {
    $aviso = "Ocorreu um erro ao processar o formulário.";
}
?>
