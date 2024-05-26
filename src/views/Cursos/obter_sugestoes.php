<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php";

// Receber o termo de busca
$termo = $_GET['query'];

// Preparar a consulta SQL para buscar cursos com base no termo
$sql = "SELECT Nome_do_Curso FROM Tb_Cursos WHERE Nome_do_Curso LIKE ? LIMIT 3";  // Limitar o número de resultados

$stmt = $_con->prepare($sql);
$likeTerm = "%" . $termo . "%";

// Vincular os parâmetros da consulta
$stmt->bind_param("s", $likeTerm);  // Passar os parâmetros

$sugestoes = array();
// Tentar executar a consulta
try {
    $stmt->execute();

    // Obter os resultados
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Adicionar o título do anúncio como sugestão
            $sugestoes[] = htmlspecialchars($row['Nome_do_Curso'], ENT_QUOTES, 'UTF-8');

        }
    } else {
        // Se não houver sugestões, adicionar uma mensagem de erro ao array
        $sugestoes[] = 'Nenhuma sugestão encontrada';
    }
} catch (Exception $e) {
    // Se ocorrer um erro, registrar o erro e adicionar uma mensagem de erro ao array
    error_log('Erro na busca de sugestões: ' . $e->getMessage());
    $sugestoes[] = 'Erro ao buscar sugestões';
}

// Converte o array para JSON e o retorna
echo json_encode($sugestoes);
?>