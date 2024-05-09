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
        if ($emailExists > 0) {
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

            if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
                // Confirma a transação
                $_con->commit();
 
                // Redirecionar para o script de envio de e-mail
                header("Location: envio_Usuario.php?userId=$userId&emailUsuario=$emailUsuario");
                exit();
            } else {
                // Se a transação falhar, rollback
                $_con->rollback();
                echo "Erro ao inserir registro. Por favor, tente novamente.";
            }
 
            // Fechar as declarações preparadas
            $stmt1->close();
            $stmt2->close();
        }}
} else {
    $aviso = "Ocorreu um erro ao processar o formulário.";
}
?>
