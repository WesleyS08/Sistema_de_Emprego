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
        if ($emailExists > 0) {
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
 
                // Redirecionar para o script de envio de e-mail
                header("Location: envio_email.php?userId=$userId&emailRecrutador=$emailRecrutador");
                exit();
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
?>