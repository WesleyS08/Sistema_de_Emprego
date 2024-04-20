<?php
include "../../services/conexão_com_banco.php";
session_start();

if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    

    // Consulta para excluir as vagas de emprego associadas à empresa usando o ID do usuário
    $sql_delete_empre = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?)";
    // Consulta para excluir os anúncios relacionados às vagas que acabamos de excluir
    $sql_delete_anuncios = "DELETE FROM Tb_Anuncios WHERE Id_Anuncios IN (SELECT Tb_Anuncios_Id FROM Tb_Vagas WHERE Tb_Empresa_CNPJ IN (SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?))";
    // Preparar as consultas
    $stmt_delete_empre = mysqli_prepare($_con, $sql_delete_empre);
    $stmt_delete_anuncios = mysqli_prepare($_con, $sql_delete_anuncios);

    if (!$stmt_delete_empre || !$stmt_delete_anuncios) {
        die("Erro ao preparar a consulta de exclusão: " . mysqli_error($_con));
    }
    // Associar o parâmetro para as duas consultas
    mysqli_stmt_bind_param($stmt_delete_empre, "i", $idUsuario);
    mysqli_stmt_bind_param($stmt_delete_anuncios, "i", $idUsuario);
    // Iniciar a transação
    mysqli_autocommit($_con, false);
    // Executar as consultas
    mysqli_stmt_execute($stmt_delete_empre);
    mysqli_stmt_execute($stmt_delete_anuncios);

    // Restaurar o modo de autocommit padrão
    mysqli_autocommit($_con, true);



    $sql_delete_anuncio = "DELETE FROM Tb_Anuncios WHERE Id_Anuncios = ?";
    $stmt_delete_anuncio = mysqli_prepare($_con, $sql_delete_anuncio);
    if (!$stmt_delete_anuncio) {
        die("Erro ao preparar a consulta: " . mysqli_error($_con));
    }
    mysqli_stmt_bind_param($stmt_delete_anuncio, "i", $Id_Anuncios);
    mysqli_stmt_execute($stmt_delete_anuncio);




    // Consulta para excluir o registro da empresa usando o ID do usuário
    $sql_delete_empresa = "DELETE FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
    $stmt_delete_empresa = mysqli_prepare($_con, $sql_delete_empresa);
    if (!$stmt_delete_empresa) {
        die("Erro ao preparar a consulta: " . mysqli_error($_con));
    }
    mysqli_stmt_bind_param($stmt_delete_empresa, "i", $idUsuario);
    mysqli_stmt_execute($stmt_delete_empresa);

    // Consulta para excluir o registro do usuário da tabela de pessoas usando o ID do usuário
    $sql_delete_pessoa = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt_delete_pessoa = mysqli_prepare($_con, $sql_delete_pessoa);
    if (!$stmt_delete_pessoa) {
        die("Erro ao preparar a consulta: " . mysqli_error($_con));
    }
    mysqli_stmt_bind_param($stmt_delete_pessoa, "i", $idUsuario);
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

