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
    mysqli_stmt_execute($stmt_delete_inscricoes);
    mysqli_stmt_close($stmt_delete_inscricoes);

    // 2. Excluir as vagas de emprego associadas à empresa
    $sql_delete_vagas = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (
            SELECT CNPJ
            FROM Tb_Empresa
            WHERE Tb_Pessoas_Id = ?
        )";
    $stmt_delete_vagas = mysqli_prepare($_con, $sql_delete_vagas);
    mysqli_stmt_bind_param($stmt_delete_vagas, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_vagas);
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
    mysqli_stmt_execute($stmt_delete_anuncios);
    mysqli_stmt_close($stmt_delete_anuncios);

    // 4. Excluir os questionários associados ao usuário
    $sql_delete_questionarios = "DELETE FROM Tb_Questionarios WHERE Id_Questionario IN (
            SELECT Id_Questionario
            FROM Tb_Empresa_Questionario
            WHERE Id_Empresa IN (
                SELECT CNPJ
                FROM Tb_Empresa
                WHERE Tb_Pessoas_Id = ?
            )
        )";
    $stmt_delete_questionarios = mysqli_prepare($_con, $sql_delete_questionarios);
    mysqli_stmt_bind_param($stmt_delete_questionarios, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_questionarios);
    mysqli_stmt_close($stmt_delete_questionarios);

    // 5. Excluir as questões associadas aos questionários
    $sql_delete_questoes = "DELETE FROM Tb_Questoes WHERE Id_Questionario IN (
            SELECT Id_Questionario
            FROM Tb_Empresa_Questionario
            WHERE Id_Empresa IN (
                SELECT CNPJ
                FROM Tb_Empresa
                WHERE Tb_Pessoas_Id = ?
            )
        )";
    $stmt_delete_questoes = mysqli_prepare($_con, $sql_delete_questoes);
    mysqli_stmt_bind_param($stmt_delete_questoes, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_questoes);
    mysqli_stmt_close($stmt_delete_questoes);

    // 6. Excluir as alternativas associadas às questões
    $sql_delete_alternativas = "DELETE FROM Tb_Alternativas WHERE Tb_Questoes_Id_Questao IN (
            SELECT Id_Questao
            FROM Tb_Questoes
            WHERE Id_Questionario IN (
                SELECT Id_Questionario
                FROM Tb_Empresa_Questionario
                WHERE Id_Empresa IN (
                    SELECT CNPJ
                    FROM Tb_Empresa
                    WHERE Tb_Pessoas_Id = ?
                )
            )
        )";
    $stmt_delete_alternativas = mysqli_prepare($_con, $sql_delete_alternativas);
    mysqli_stmt_bind_param($stmt_delete_alternativas, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_alternativas);
    mysqli_stmt_close($stmt_delete_alternativas);

    // 7. Excluir as relações entre questionários e questões
    $sql_delete_relacao_questionario_questoes = "DELETE FROM Tb_Questionario_Questoes WHERE Id_Questionario IN (
            SELECT Id_Questionario
            FROM Tb_Empresa_Questionario
            WHERE Id_Empresa IN (
                SELECT CNPJ
                FROM Tb_Empresa
                WHERE Tb_Pessoas_Id = ?
            )
        )";
    $stmt_delete_relacao_questionario_questoes = mysqli_prepare($_con, $sql_delete_relacao_questionario_questoes);
    mysqli_stmt_bind_param($stmt_delete_relacao_questionario_questoes, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_relacao_questionario_questoes);
    mysqli_stmt_close($stmt_delete_relacao_questionario_questoes);

    // 8. Excluir a relação entre empresa e questionário
    $sql_delete_relacao_empresa_questionario = "DELETE FROM Tb_Empresa_Questionario WHERE Id_Empresa IN (
            SELECT CNPJ
            FROM Tb_Empresa
            WHERE Tb_Pessoas_Id = ?
        )";
    $stmt_delete_relacao_empresa_questionario = mysqli_prepare($_con, $sql_delete_relacao_empresa_questionario);
    mysqli_stmt_bind_param($stmt_delete_relacao_empresa_questionario, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_relacao_empresa_questionario);
    mysqli_stmt_close($stmt_delete_relacao_empresa_questionario);

  

    // Consulta para obter os dados da empresa com base no ID do usuário
    $sql_select_dados_empresa = "SELECT * FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
    $stmt_select_dados_empresa = mysqli_prepare($_con, $sql_select_dados_empresa);
    mysqli_stmt_bind_param($stmt_select_dados_empresa, "i", $idUsuario);
    mysqli_stmt_execute($stmt_select_dados_empresa);
    $result = mysqli_stmt_get_result($stmt_select_dados_empresa);

    // Verifica se os dados da empresa foram encontrados
    if ($result && mysqli_num_rows($result) > 0) {
        $dadosEmpresa = mysqli_fetch_assoc($result);
       
        $caminhoImagemPerfil = $dadosEmpresa['Img_Perfil'];
        $caminhoImagemBanner = $dadosEmpresa['Img_Banner'];

        // Excluir todas as informações da empresa e conta associadas ao ID do usuário
        // ...

        // 8. Excluir a imagem de perfil da empresa, se existir
        if (!empty($caminhoImagemPerfil)) {
            unlink($caminhoImagemPerfil); // Exclui o arquivo do servidor
        }

        // 9. Excluir a imagem do banner da empresa, se existir
        if (!empty($caminhoImagemBanner)) {
            unlink($caminhoImagemBanner); // Exclui o arquivo do servidor
        }

        // 10. Excluir a empresa associada ao ID da pessoa
        $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
        $stmt_delete_empresa = mysqli_prepare($_con, $sql_delete_empresa);
        mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idUsuario);
        mysqli_stmt_execute($stmt_delete_empresa);
        mysqli_stmt_close($stmt_delete_empresa);

        // 10. Excluir a pessoa associada ao ID
        $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
        $stmt_delete_pessoa = mysqli_prepare($_con, $sql_delete_pessoa);
        mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idUsuario);
        mysqli_stmt_execute($stmt_delete_pessoa);
        mysqli_stmt_close($stmt_delete_pessoa);

        // Destruir a sessão
        session_destroy();
        header("Location: ../../../index.php");
        exit();
} else {
    // Se o ID não foi encontrado, redireciona para uma página de erro
    header("Location: ../../views/PaginaErro/Erro.html");
    exit();
}
}
?>
