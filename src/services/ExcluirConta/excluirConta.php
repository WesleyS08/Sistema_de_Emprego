<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";

// Inicia a sessão
session_start();

// Definir variáveis para armazenar informações excluídas
$inscricoesExcluidas = [];
$vagasExcluidas = [];
$empresaExcluida = [];
$pessoaExcluida = [];
$anunciosExcluidos = [];

// Verifica se o ID da pessoa foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idUsuario = intval($_GET['id']);  // Converte para número inteiro

    // 1. Excluir todas as inscrições associadas às vagas de emprego da empresa
    $sql_delete_inscricoes = "DELETE FROM Tb_Inscricoes WHERE (Tb_Vagas_Tb_Anuncios_Id, Tb_Vagas_Tb_Empresa_CNPJ) IN (
            SELECT Id_Anuncios, Tb_Empresa_CNPJ
            FROM Tb_Vagas
            WHERE Tb_Empresa_CNPJ IN (
                SELECT CNPJ
                FROM Tb_Empresa
                WHERE Tb_Pessoas_Id = ?
            )
        )";

    $stmt_delete_inscricoes = mysqli_prepare($_con, $sql_delete_inscricoes);
    if (!$stmt_delete_inscricoes) {
        die("Erro ao preparar a consulta para excluir as inscrições: " . mysqli_error($_con));
    }

    mysqli_stmt_bind_param($stmt_delete_inscricoes, "i", $idUsuario);

    if (!mysqli_stmt_execute($stmt_delete_inscricoes)) {
        die("Erro ao executar a consulta para excluir as inscrições: " . mysqli_stmt_error($stmt_delete_inscricoes));
    }

    mysqli_stmt_close($stmt_delete_inscricoes);  // Limpa o recurso

    // Armazenar informações excluídas
    $inscricoesExcluidas[] = "Todas as inscrições associadas à empresa com ID $idUsuario";

    // 2. Excluir as vagas de emprego associadas à empresa
    $sql_delete_vagas = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (
            SELECT CNPJ
            FROM Tb_Empresa
            WHERE Tb_Pessoas_Id = ?
        )";

    $stmt_delete_vagas = mysqli_prepare($_con, $sql_delete_vagas);
    if (!$stmt_delete_vagas) {
        die("Erro ao preparar a consulta para excluir as vagas de emprego: " . mysqli_error($_con));
    }

    mysqli_stmt_bind_param($stmt_delete_vagas, "i", $idUsuario);

    if (!mysqli_stmt_execute($stmt_delete_vagas)) {
        die("Erro ao executar a consulta para excluir as vagas de emprego: " . mysqli_stmt_error($stmt_delete_vagas));
    }

    mysqli_stmt_close($stmt_delete_vagas);

    // Armazenar informações excluídas
    $vagasExcluidas[] = "Todas as vagas de emprego associadas à empresa com ID $idUsuario";

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
    if (!$stmt_delete_anuncios) {
        die("Erro ao preparar a consulta para excluir os anúncios: " . mysqli_error($_con));
    }

    mysqli_stmt_bind_param($stmt_delete_anuncios, "i", $idUsuario);

    if (!mysqli_stmt_execute($stmt_delete_anuncios)) {
        die("Erro ao executar a consulta para excluir os anúncios: " . mysqli_stmt_error($stmt_delete_anuncios));
    }

    mysqli_stmt_close($stmt_delete_anuncios);

    // Armazenar informações excluídas
    $anunciosExcluidos[] = "Todos os anúncios associados à empresa com ID $idUsuario";

    // 4. Excluir a empresa associada ao ID da pessoa
    $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
    $stmt_delete_empresa = mysqli_prepare($_con, $sql_delete_empresa);

    if (!$stmt_delete_empresa) {
        die("Erro ao preparar a consulta para excluir a empresa: " . mysqli_error($_con));
    }

    mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idUsuario);

    if (!mysqli_stmt_execute($stmt_delete_empresa)) {
        die("Erro ao executar a consulta para excluir a empresa: " . mysqli_stmt_error($stmt_delete_empresa));
    }

    mysqli_stmt_close($stmt_delete_empresa);  // Limpa o recurso

    // Armazenar informações excluídas
    $empresaExcluida[] = "Empresa associada ao ID da pessoa $idUsuario";

    // 5. Excluir a pessoa associada ao ID
    $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt_delete_pessoa = mysqli_prepare($_con, $sql_delete_pessoa);

    if (!$stmt_delete_pessoa) {
        die("Erro ao preparar a consulta para excluir a pessoa: " . mysqli_error($_con));
    }

    mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idUsuario);

    if (!mysqli_stmt_execute($stmt_delete_pessoa)) {
        die("Erro ao executar a consulta para excluir a pessoa: " . mysqli_stmt_error($stmt_delete_pessoa));
    }

    mysqli_stmt_close($stmt_delete_pessoa);  // Limpa o recurso

    // Armazenar informações excluídas
    $pessoaExcluida[] = "Pessoa com ID $idUsuario";

    // Destruir a sessão
    session_destroy();

    // Redirecionar para a página de confirmação

    // Mostrar informações excluídas
    echo "<h1>Informações Excluídas:</h1>";
    echo "<p>" . implode("<br>", $inscricoesExcluidas) . "</p>";
    echo "<p>" . implode("<br>", $vagasExcluidas) . "</p>";
    echo "<p>" . implode("<br>", $anunciosExcluidos) . "</p>";
    echo "<p>" . implode("<br>", $empresaExcluida) . "</p>";
    echo "<p>" . implode("<br>", $pessoaExcluida) . "</p>";

    exit();
} else {
    // Se o ID não foi encontrado, redireciona para uma página de erro

    exit();
}
?>
