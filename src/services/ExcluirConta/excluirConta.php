<?php
include "../../services/conexão_com_banco.php";
session_start();

// Verifica se o usuário está logado
if(isset($_SESSION['idPessoa'])) {
    $idPessoa = $_SESSION['idPessoa'];

    // Consulta para excluir as vagas de emprego associadas à empresa
    $sql_delete_vagas = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?)";
    $stmt_delete_vagas = mysqli_prepare($conexao, $sql_delete_vagas);
    mysqli_stmt_bind_param($stmt_delete_vagas, "i", $idPessoa);
    mysqli_stmt_execute($stmt_delete_vagas);

    // Consulta para excluir o registro da empresa
    $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
    $stmt_delete_empresa = mysqli_prepare($conexao, $sql_delete_empresa);
    mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idPessoa);
    mysqli_stmt_execute($stmt_delete_empresa);

    // Consulta para excluir o registro do usuário da tabela de pessoas
    $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt_delete_pessoa = mysqli_prepare($conexao, $sql_delete_pessoa);
    mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idPessoa);
    mysqli_stmt_execute($stmt_delete_pessoa);

    // Após a exclusão, destrói a sessão e redireciona para alguma página de confirmação ou para a página inicial
    session_destroy();
    header("Location: index.html");
    exit();
} else {
    // Se o usuário não estiver logado, redireciona para alguma página de erro
    header("Location: index.html");
    exit();
}
?>