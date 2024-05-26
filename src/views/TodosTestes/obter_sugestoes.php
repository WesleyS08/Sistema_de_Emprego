<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php"; // Ajuste o caminho conforme necessário

// Receber o termo de busca e a área enviados pela solicitação AJAX
$termo = isset($_POST['termo']) ? trim($_POST['termo']) : '';  // Limpar espaços em branco
$area = isset($_POST['area']) ? trim($_POST['area']) : '';

// Preparar a consulta SQL para buscar títulos com base no termo e, opcionalmente, na área
$sql = "SELECT Nome FROM Tb_Questionarios WHERE Nome COLLATE utf8_unicode_ci LIKE ?";

// Se a área for diferente de "todas", adicione a condição WHERE
if ($area !== 'Todas') {
    $sql .= " AND Area = ?";
}

$sql .= " LIMIT 4";

$stmt = $_con->prepare($sql);
$likeTerm = "%" . $termo . "%";

// Vincular os parâmetros da consulta
if ($area !== 'Todas') {
    $stmt->bind_param("ss", $likeTerm, $area);
} else {
    $stmt->bind_param("s", $likeTerm);
}

// Inicializa um array para armazenar as sugestões
$sugestoes = array();

// Tentar executar a consulta
try {
    $stmt->execute();

    // Obter os resultados
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Adicionar o título do anúncio como sugestão
            $sugestoes[] = htmlspecialchars($row['Nome'], ENT_QUOTES, 'UTF-8');
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