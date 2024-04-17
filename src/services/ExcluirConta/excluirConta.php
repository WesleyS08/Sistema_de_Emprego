<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verifique se o ID da pessoa a ser excluída foi enviado via POST
if(isset($_POST['id'])) {
    $idPessoa = $_POST['id'];

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

    // Se a exclusão for bem-sucedida, redirecione o usuário para alguma página de confirmação ou página inicial
    header("Location: algumapagina.php");
    exit();
} else {
    // Se o ID da pessoa não foi fornecido, redirecione para alguma página de erro
    header("Location: algumaoutrapagina.php");
    exit();
}
?>
