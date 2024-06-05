<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";

// Inicia a sessão
session_start();

// Verifica se o ID da pessoa foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idUsuario = intval($_GET['id']);  // Converte para número inteiro

    // Consulta para obter o CPF do candidato usando o Tb_Pessoas_Id
    $sql_select_cpf = "SELECT CPF FROM Tb_Candidato WHERE Tb_Pessoas_Id = ?";
    $stmt_select_cpf = mysqli_prepare($_con, $sql_select_cpf);
    mysqli_stmt_bind_param($stmt_select_cpf, "i", $idUsuario);
    mysqli_stmt_execute($stmt_select_cpf);
    mysqli_stmt_bind_result($stmt_select_cpf, $cpfCandidato);
    mysqli_stmt_fetch($stmt_select_cpf);
    mysqli_stmt_close($stmt_select_cpf);

    if ($cpfCandidato) {
        // 1. Excluir todas as recomendações associadas ao candidato
        $sql_delete_recomendacoes = "DELETE FROM Tb_Recomendacoes WHERE Tb_Candidato_CPF = ?";
        $stmt_delete_recomendacoes = mysqli_prepare($_con, $sql_delete_recomendacoes);
        mysqli_stmt_bind_param($stmt_delete_recomendacoes, "s", $cpfCandidato);
        mysqli_stmt_execute($stmt_delete_recomendacoes);
        mysqli_stmt_close($stmt_delete_recomendacoes);

        // 2. Excluir todas as inscrições do candidato em vagas de emprego
        $sql_delete_inscricoes = "DELETE FROM Tb_Inscricoes WHERE Tb_Candidato_CPF = ?";
        $stmt_delete_inscricoes = mysqli_prepare($_con, $sql_delete_inscricoes);
        mysqli_stmt_bind_param($stmt_delete_inscricoes, "s", $cpfCandidato);
        mysqli_stmt_execute($stmt_delete_inscricoes);
        mysqli_stmt_close($stmt_delete_inscricoes);

        // 3. Excluir todas as avaliações do candidato
        $sql_delete_avaliacoes = "DELETE FROM Tb_Avaliacoes WHERE Tb_Pessoas_Id = ?";
        $stmt_delete_avaliacoes = mysqli_prepare($_con, $sql_delete_avaliacoes);
        mysqli_stmt_bind_param($stmt_delete_avaliacoes, "i", $idUsuario);
        mysqli_stmt_execute($stmt_delete_avaliacoes);
        mysqli_stmt_close($stmt_delete_avaliacoes);

        // 4. Excluir todos os resultados do candidato em questionários
        $sql_delete_resultados = "DELETE FROM Tb_Resultados WHERE Tb_Candidato_CPF = ?";
        $stmt_delete_resultados = mysqli_prepare($_con, $sql_delete_resultados);
        mysqli_stmt_bind_param($stmt_delete_resultados, "s", $cpfCandidato);
        mysqli_stmt_execute($stmt_delete_resultados);
        mysqli_stmt_close($stmt_delete_resultados);

        // 5. Excluir o candidato
        $sql_delete_candidato = "DELETE FROM Tb_Candidato WHERE Tb_Pessoas_Id = ?";
        $stmt_delete_candidato = mysqli_prepare($_con, $sql_delete_candidato);
        mysqli_stmt_bind_param($stmt_delete_candidato, "i", $idUsuario);
        mysqli_stmt_execute($stmt_delete_candidato);
        mysqli_stmt_close($stmt_delete_candidato);

        // 6. Excluir a pessoa associada ao ID
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
        // Se o CPF não foi encontrado, redireciona para uma página de erro
        header("Location: ../../views/PaginaErro/Erro.html");
        exit();
    }
} else {
    // Se o ID não foi encontrado, redireciona para uma página de erro
    header("Location: ../../views/PaginaErro/Erro.html");
    exit();
}
?>
