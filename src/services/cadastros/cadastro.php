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

    if ($cpfExists > 0) {
        echo "Erro ao inserir registro: CPF já existe.";        
    } else {
        // Iniciar a transação
        $_con->begin_transaction();

        // Prevenir SQL Injection usando prepared statements
        $stmt1 = $_con->prepare("INSERT INTO tb_pessoas (Nome, Email, Senha) VALUES (?, ?, ?)");
        $stmt1->bind_param("sss", $nomeUsuario, $emailUsuario, $senhaCriptografada);
        
        $stmt2 = $_con->prepare("INSERT INTO tb_candidato (CPF) VALUES (?)");
        $stmt2->bind_param("s", $cpfUsuario);

        // Executar as instruções SQL dentro da transação
        $stmt1->execute();

        // Obtenção do Id do usuário inserido na última tabela (tb_pessoas)
        $userId = $_con->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];

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
        
            $mail->isHTML(true);
            $mail->Subject = 'SIAS - Email de autenticação';
            
            // Corpo do e-mail
            $mail->Body = 'Olá, candidato! Obrigado por se inscrever em nosso site';
            $mail->Body .= '<br>Clique no link abaixo para ativar o seu cadastro e acessar as vagas.<br>';
            $link = 'http://localhost/Sistema_de_Emprego/src/services/auth/verificarUsuario.php?id=' . $userId; // Link + protocolo HTTP
            $mail->Body .= '<a href="' . $link . '">Clique aqui para verificar seu cadastro</a>';
            
            $mail->AltBody = 'Olá, candidato! Obrigado por se inscrever em nosso site';
            $mail->AltBody .= 'Clique no link abaixo para ativar o seu cadastro e acessar as vagas.';
            $mail->AltBody .= 'Link: ' . $link;
        
            if($mail->send()) {
                // Redirecionar página
                echo 'Email enviado com sucesso';
            } else {
                echo 'Email não enviado';
            }
        } catch (Exception $e) {
            echo "Erro grave ao enviar e-mail: {$mail->ErrorInfo}";
        }

    }
} else {
    $aviso = "Ocorreu um erro ao processar o formulário.";
}
?>
