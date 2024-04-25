<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";

// Inicia a sessão
session_start();


// Verifica se o ID da pessoa foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idUsuario = intval($_GET['id']);  // Converte para número inteiro

    // 1. Excluir todas as inscrições associadas às vagas de emprego da empresa
    $sql_delete_inscricoes = "DELETE FROM Tb_Inscricoes WHERE (Tb_Vagas_Tb_Anuncios_Id, Tb_Vagas_Tb_Empresa_CNPJ) IN (
            SELECT Tb_Vagas_Tb_Anuncios_Id, Tb_Empresa_CNPJ
            FROM Tb_Vagas
            WHERE Tb_Empresa_CNPJ IN (
                SELECT CNPJ
                FROM Tb_Empresa
                WHERE Tb_Pessoas_Id = ?
            )
        )";

    $stmt_delete_inscricoes = mysqli_prepare($_con, $sql_delete_inscricoes);
    mysqli_stmt_bind_param($stmt_delete_inscricoes, "i", $idUsuario);
    mysqli_stmt_close($stmt_delete_inscricoes);  // Limpa o recurso

 
    // 2. Excluir as vagas de emprego associadas à empresa
    $sql_delete_vagas = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (
            SELECT CNPJ
            FROM Tb_Empresa
            WHERE Tb_Pessoas_Id = ?
        )";

    $stmt_delete_vagas = mysqli_prepare($_con, $sql_delete_vagas);
    mysqli_stmt_bind_param($stmt_delete_vagas, "i", $idUsuario);
    mysqli_stmt_close($stmt_delete_vagas);

   
    // 3. Excluir os anúncios associados às vagas de emprego
    $sql_delete_anuncios = "DELETE FROM Tb_Anuncios WHERE Id_Anuncios IN (
            SELECT Tb_Anuncios_Id
            FROM Tb_Vagas
            WHERE Tb_Empresa_CNPJ IN (
                SELECT CNPJ
                FROM Tb_Empresa
                WHERE Tb_Pessoas_Id = ?
            )
        )";

    
    $stmt_delete_anuncios = mysqli_prepare($_con, $sql_delete_anuncios);
    mysqli_stmt_bind_param($stmt_delete_anuncios, "i", $idUsuario);
    mysqli_stmt_close($stmt_delete_anuncios);



    // 4. Excluir a empresa associada ao ID da pessoa
    $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
    $stmt_delete_empresa = mysqli_prepare($_con, $sql_delete_empresa);


    mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idUsuario);
    mysqli_stmt_close($stmt_delete_empresa);  // Limpa o recurso

   

    // 5. Excluir a pessoa associada ao ID
    $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt_delete_pessoa = mysqli_prepare($_con, $sql_delete_pessoa);

    
    // preparada $stmt_delete_pessoa.
    mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idUsuario);
    mysqli_stmt_close($stmt_delete_pessoa);  // Limpa o recurso


    






    // Delete de Anuncios
    $sql_delete_anuncios = "DELETE FROM Tb_Anuncios";
    $stmt_delete_anuns = mysqli_prepare($_con, $sql_delete_anuncios);
    

    mysqli_stmt_close($stmt_delete_anuns); // Limpa o recurso


    // Destruir a sessão
    session_destroy();
    header("Location: ../../../index.php");
    exit();
} else {
    // Se o ID não foi encontrado, redireciona para uma página de erro
    header("Location: ../../views/PaginaErro/Erro.html");
    exit();
}
?>
