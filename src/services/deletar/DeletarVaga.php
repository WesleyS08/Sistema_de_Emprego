<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php";
session_start();



// Obter o ID do anúncio para exclusão
$idAnuncio = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obter o ID da pessoa do usuário
$idPessoaUsuario = isset($_GET['idPessoa']) ? (int)$_GET['idPessoa'] : 0;


// Verificar se o ID é válido
if ($idAnuncio <= 0) {
    http_response_code(400);
    echo 'ID inválido para exclusão.';
    exit;
}

// Excluir inscrições associadas ao anúncio
$sql_delete_inscricoes = "DELETE FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
$stmt_inscricoes = mysqli_prepare($_con, $sql_delete_inscricoes);

if ($stmt_inscricoes) {
    mysqli_stmt_bind_param($stmt_inscricoes, "i", $idAnuncio);
    mysqli_stmt_execute($stmt_inscricoes);
    mysqli_stmt_close($stmt_inscricoes);
} else {
    die("Erro ao preparar a consulta de exclusão de inscrições: " . mysqli_error($_con));
}

// Excluir vagas associadas ao anúncio
$sql_delete_vagas = "DELETE FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";
$stmt_vagas = mysqli_prepare($_con, $sql_delete_vagas);

if ($stmt_vagas) {
    mysqli_stmt_bind_param($stmt_vagas, "i", $idAnuncio);
    mysqli_stmt_execute($stmt_vagas);
    mysqli_stmt_close($stmt_vagas);
} else {
    die("Erro ao preparar a consulta de exclusão de vagas: " . mysqli_error($_con));
}

// Excluir o anúncio
$sql_delete_anuncio = "DELETE FROM Tb_Anuncios WHERE Id_Anuncios = ?";
$stmt_anuncio = mysqli_prepare($_con, $sql_delete_anuncio);

if ($stmt_anuncio) {
    mysqli_stmt_bind_param($stmt_anuncio, "i", $idAnuncio);
    mysqli_stmt_execute($stmt_anuncio);
    mysqli_stmt_close($stmt_anuncio);
} else {
    die("Erro ao preparar a consulta de exclusão do anúncio: " . mysqli_error($_con));
}

// Fechar a conexão com o banco de dados
mysqli_close($_con);

// Mensagem de sucesso ou redirecionamento
header("Location: ../../views/HomeRecrutador/homeRecrutador.php?id=" . $idPessoaUsuario);

?>
