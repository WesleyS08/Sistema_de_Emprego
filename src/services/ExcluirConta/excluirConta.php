<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../services/conexão_com_banco.php";

// Inicia a sessão
session_start();

// Verifica se o ID da pessoa foi fornecido na URL
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    // Consulta para obter os IDs dos anúncios associados às vagas de emprego da empresa usando o ID do usuário
    $sql_select_anuncios = "
        SELECT Tb_Anuncios_Id 
        FROM Tb_Vagas 
        WHERE Tb_Empresa_CNPJ IN (
            SELECT CNPJ 
            FROM Tb_Empresa 
            WHERE Tb_Pessoas_Id = ?
        )";

    // Prepara a consulta para selecionar os IDs dos anúncios
    $stmt_select_anuncios = mysqli_prepare($_con, $sql_select_anuncios);

    if (!$stmt_select_anuncios) {
        die("Erro ao preparar a consulta para selecionar os anúncios: " . mysqli_error($_con));
    }

    // Associa o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_select_anuncios, "i", $idUsuario);

    // Executa a consulta
    mysqli_stmt_execute($stmt_select_anuncios);

    // Associa o resultado da consulta a uma variável
    mysqli_stmt_bind_result($stmt_select_anuncios, $idAnuncio);

    // Inicializa um array para armazenar os IDs dos anúncios
    $anunciosParaExcluir = array();

    // Percorre os resultados da consulta e armazena os IDs dos anúncios no array
    while (mysqli_stmt_fetch($stmt_select_anuncios)) {
        $anunciosParaExcluir[] = $idAnuncio;
    }

    // Fecha o statement da consulta
    mysqli_stmt_close($stmt_select_anuncios);

    // Exclui os anúncios encontrados
    foreach ($anunciosParaExcluir as $anuncio) {
        // Consulta para excluir o anúncio
        $sql_delete_anuncio = "DELETE FROM Tb_Anuncios WHERE Tb_Anuncios_Id = ?";
        // Prepara a consulta para excluir o anúncio
        $stmt_delete_anuncio = mysqli_prepare($_con, $sql_delete_anuncio);

        if (!$stmt_delete_anuncio) {
            die("Erro ao preparar a consulta para excluir o anúncio: " . mysqli_error($_con));
        }

        // Associa o parâmetro para a consulta
        mysqli_stmt_bind_param($stmt_delete_anuncio, "i", $anuncio);

        // Executa a consulta para excluir o anúncio
        mysqli_stmt_execute($stmt_delete_anuncio);

        // Fecha o statement da consulta
        mysqli_stmt_close($stmt_delete_anuncio);
    }

    // Consulta para excluir as inscrições associadas às vagas de emprego da empresa usando o ID do usuário
    $sql_delete_inscricoes = "
        DELETE FROM Tb_Inscricoes 
        WHERE (Tb_Vagas_Tb_Anuncios_Id, Tb_Vagas_Tb_Empresa_CNPJ) IN (
            SELECT Tb_Anuncios_Id, Tb_Empresa_CNPJ 
            FROM Tb_Vagas 
            WHERE Tb_Empresa_CNPJ IN (
                SELECT CNPJ 
                FROM Tb_Empresa 
                WHERE Tb_Pessoas_Id = ?
            )
        )";

    // Prepara a consulta para excluir as inscrições
    $stmt_delete_inscricoes = mysqli_prepare($_con, $sql_delete_inscricoes);

    if (!$stmt_delete_inscricoes) {
        die("Erro ao preparar a consulta para excluir as inscrições: " . mysqli_error($_con));
    }

    // Associa o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_inscricoes, "i", $idUsuario);

    // Executa a consulta para excluir as inscrições
    mysqli_stmt_execute($stmt_delete_inscricoes);

    // Fecha o statement da consulta
    mysqli_stmt_close($stmt_delete_inscricoes);

    // Consulta para excluir as vagas de emprego associadas à empresa usando o ID da pessoa
    $sql_delete_vagas = "
        DELETE FROM Tb_Vagas 
        WHERE Tb_Empresa_CNPJ IN (
            SELECT CNPJ 
            FROM Tb_Empresa 
            WHERE Tb_Pessoas_Id = ?
        )";

    // Prepara a consulta para excluir as vagas de emprego
    $stmt_delete_vagas = mysqli_prepare($_con, $sql_delete_vagas);

    if (!$stmt_delete_vagas) {
        die("Erro ao preparar a consulta para excluir as vagas de emprego: " . mysqli_error($_con));
    }

    // Associa o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_vagas, "i", $idUsuario);

    // Executa a consulta para excluir as vagas de emprego
    mysqli_stmt_execute($stmt_delete_vagas);

    // Fecha o statement da consulta
    mysqli_stmt_close($stmt_delete_vagas);

    // Consulta para excluir o registro da empresa usando o ID da pessoa
    $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";

    // Prepara a consulta para excluir a empresa
    $stmt_delete_empresa = mysqli_prepare($_con, $sql_delete_empresa);

    if (!$stmt_delete_empresa) {
        die("Erro ao preparar a consulta para excluir a empresa: " . mysqli_error($_con));
    }

    // Associa o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idUsuario);

    // Executa a consulta para excluir a empresa
    mysqli_stmt_execute($stmt_delete_empresa);

    // Fecha o statement da consulta
    mysqli_stmt_close($stmt_delete_empresa);

    // Consulta para excluir o registro da pessoa usando o ID da pessoa
    $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";

    // Prepara a consulta para excluir a pessoa
    $stmt_delete_pessoa = mysqli_prepare($_con, $sql_delete_pessoa);

    if (!$stmt_delete_pessoa) {
        die("Erro ao preparar a consulta para excluir a pessoa: " . mysqli_error($_con));
    }

    // Associa o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idUsuario);

    // Executa a consulta para excluir a pessoa
    mysqli_stmt_execute($stmt_delete_pessoa);

    // Fecha o statement da consulta
    mysqli_stmt_close($stmt_delete_pessoa);

    // Destroi a sessão independentemente do sucesso da exclusão
    session_destroy();

    // Redireciona para uma página de confirmação
    header("Location: ../../../index.php");
    exit();
} else {
    // Se o ID da pessoa não foi fornecido, redireciona para alguma página de erro
    header("Location: ../../../index.php");
    exit();
}
?>
