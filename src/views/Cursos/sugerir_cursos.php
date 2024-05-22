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

// Tentar executar a consulta
try {
    $stmt->execute();

    // Obter os resultados
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='sugestao-item'>" . htmlspecialchars($row['Nome_do_Curso'], ENT_QUOTES, 'UTF-8') . "</div>";
        }
    } else {
        echo "<div class='sugestao-item'>Nenhuma sugestão encontrada</div>";
    }
} catch (Exception $e) {
    // Registrar o erro e retornar uma mensagem apropriada para o cliente
    error_log('Erro na busca de sugestões: ' . $e->getMessage());
    echo "<div class='sugestao-item'>Erro ao buscar sugestões</div>";
}

// Fechar a declaração e a conexão
$stmt->close();
$_con->close();
?>
