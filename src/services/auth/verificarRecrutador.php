<?php
include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Atualizar o atributo na tabela tb_pessoas para permitir a verificação
    $stmtUpdate = $_con->prepare("UPDATE tb_pessoas SET Verificado = 1 WHERE Id_Pessoas = ?");
    $stmtUpdate->bind_param("i", $idPessoa);
    $idPessoa = $_GET['id']; // O ID do usuário é passado via GET
    $stmtUpdate->execute();

    if ($stmtUpdate->affected_rows > 0) {
        echo "Seu cadastro foi verificado com sucesso!";
    } else {
        echo "Falha ao verificar o cadastro.";
    }

    $stmtUpdate->close();
} else {
    echo "Método de requisição inválido.";
}
?>