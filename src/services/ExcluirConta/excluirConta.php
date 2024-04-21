<?php
include "../../services/conexão_com_banco.php";
session_start();

if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];
    $idAnuncio;

    // Consulta para obter os IDs dos anúncios associados às vagas de emprego da empresa usando o ID do usuário
    $sql_select_anuncios = "SELECT Tb_Anuncios_Id FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";

    // Preparar a consulta
    $stmt_select_anuncios = mysqli_prepare($_con, $sql_select_anuncios);

    if (!$stmt_select_anuncios) {
        die("Erro ao preparar a consulta para selecionar os anúncios: " . mysqli_error($_con));
    }

    // Associar o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_select_anuncios, "i", $idUsuario);

    // Executar a consulta
    mysqli_stmt_execute($stmt_select_anuncios);

    // Vincular o resultado da consulta
    mysqli_stmt_bind_result($stmt_select_anuncios, $idAnuncio);

    // Inicializar um array para armazenar os IDs dos anúncios
    $anunciosParaExcluir = array();

    // Buscar todos os IDs dos anúncios e armazená-los no array
    while (mysqli_stmt_fetch($stmt_select_anuncios)) {
        $anunciosParaExcluir[] = $idAnuncio;
    }

    // Fechar o statement
    mysqli_stmt_close($stmt_select_anuncios);

    // Excluir os anúncios encontrados
    foreach ($anunciosParaExcluir as $anuncio) {
        // Consulta para excluir o anúncio
        $sql_delete_anuncio = "DELETE FROM Tb_Anuncios WHERE Id_Anuncios = ?";
        // Preparar a consulta
        $stmt_delete_anuncio = mysqli_prepare($_con, $sql_delete_anuncio);

        if (!$stmt_delete_anuncio) {
            die("Erro ao preparar a consulta para excluir o anúncio: " . mysqli_error($_con));
        }

        // Associar o parâmetro para a consulta
        mysqli_stmt_bind_param($stmt_delete_anuncio, "i", $anuncio);

        // Executar a consulta
        mysqli_stmt_execute($stmt_delete_anuncio);

        // Fechar o statement
        mysqli_stmt_close($stmt_delete_anuncio);
    }

    // Consulta para excluir as vagas de emprego associadas à empresa usando o ID do usuário
    $sql_delete_empre = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?)";
    // Preparar a consulta
    $stmt_delete_empre = mysqli_prepare($_con, $sql_delete_empre);

    if (!$stmt_delete_empre) {
        die("Erro ao preparar a consulta de exclusão de vagas de emprego: " . mysqli_error($_con));
    }

    // Associar o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_empre, "i", $idUsuario);
    // Executar a consulta
    mysqli_stmt_execute($stmt_delete_empre);

    // Consulta para excluir o registro da empresa usando o ID do usuário
    $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
    // Preparar a consulta
    $stmt_delete_empresa = mysqli_prepare($_con, $sql_delete_empresa);

    if (!$stmt_delete_empresa) {
        die("Erro ao preparar a consulta de exclusão de empresa: " . mysqli_error($_con));
    }

    // Associar o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idUsuario);
    // Executar a consulta
    mysqli_stmt_execute($stmt_delete_empresa);

    // Consulta para excluir o registro do usuário da tabela de pessoas usando o ID do usuário
    $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    // Preparar a consulta
    $stmt_delete_pessoa = mysqli_prepare($_con, $sql_delete_pessoa);

    if (!$stmt_delete_pessoa) {
        die("Erro ao preparar a consulta de exclusão de pessoa: " . mysqli_error($_con));
    }

    // Associar o parâmetro para a consulta
    mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idUsuario);
    // Executar a consulta
    mysqli_stmt_execute($stmt_delete_pessoa);

    // Destruir a sessão independentemente do sucesso da exclusão
    session_destroy();

    // Redirecionar para uma página de confirmação
    header("Location: ../../../index.php");
    exit();
} else {
    // Se o ID do usuário não foi fornecido, redireciona para alguma página de erro
    header("Location: ../../../index.php");
    exit();
}
?>
