<?php
// Inclui arquivos necessários para conexão ao banco e PHPMailer
include "../conexão_com_banco.php";

include "../../components/PHPMailer-master/src/PHPMailer.php";
include "../../components/PHPMailer-master/src/SMTP.php";
include "../../components/PHPMailer-master/src/Exception.php";

// Usa os namespaces necessários para PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter dados do formulário
    $nomeRecrutador = $_POST['nome'];
    $emailRecrutador = $_POST['email'];
    $cnpjRecrutador = $_POST['cnpj'];
    $senhaRecrutador = $_POST['senha'];

    // Criptografia da senha
    $senhaCriptografada = sha1($senhaRecrutador);

    // Verificar se o CNPJ já existe
    $checkCNPJ = $_con->prepare("SELECT COUNT(*) AS total FROM tb_empresa WHERE CNPJ = ?");
    $checkCNPJ->bind_param("s", $cnpjRecrutador);
    $checkCNPJ->execute();
    $cnpjExists = $checkCNPJ->get_result()->fetch_assoc()['total'];

    // Verificar se Email existe na tabela

    $checkEmail = $_con->prepare("SELECT COUNT(*) AS total FROM tb_pessoas WHERE Email = ?");
    $checkEmail->bind_param("s", $emailRecrutador);
    $checkEmail->execute();
    $emailExists = $checkEmail->get_result()->fetch_assoc()['total'];

    if ($cnpjExists > 0) {
        echo "Erro ao inserir registro: CNPJ já existe.";
    } else {
        if ($emailExists > 0){
            echo "Erro ao inserir registro: Email já cadastrado.";
        } else {           
            // Iniciar transação para inserir dados no banco
            $_con->begin_transaction();

            // Inserção na tabela de pessoas
            $stmt1 = $_con->prepare("INSERT INTO tb_pessoas (Nome, Email, Senha) VALUES (?, ?, ?)");
            $stmt1->bind_param("sss", $nomeRecrutador, $emailRecrutador, $senhaCriptografada);
            $stmt1->execute();

            // Obter o ID do usuário recém inserido
            $userId = $_con->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];

            // Inserção na tabela de empresa
            $stmt2 = $_con->prepare("INSERT INTO tb_empresa (CNPJ, Tb_Pessoas_Id) VALUES (?, ?)");
            $stmt2->bind_param("si", $cnpjRecrutador, $userId);
            $stmt2->execute();

            // Verifica se ambas as operações foram bem-sucedidas
            if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
                // Confirma a transação
                $_con->commit();
                echo "Registro inserido com sucesso!";
                
                // Envio de e-mail de confirmação
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
                    $link = 'http://localhost/Sistema_de_Emprego/src/views/EmailVerificado/emailVerificado.php?id=' . $userId;

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
                                <p'>Olá, recrutador, $nomeRecrutador!</p>
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

            } else {
                // Se a transação falhar, rollback
                $_con->rollback();
                echo "Erro ao inserir registro. Por favor, tente novamente.";
            }

            // Fecha as instruções preparadas
            $stmt1->close();
            $stmt2->close();
        }
    }        
} else {
    echo "Ocorreu um erro ao processar o formulário.";
}
