<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php"; // Ajuste o caminho conforme necessário

// Receber o termo de busca e a área enviados pela solicitação AJAX
$termo = isset($_POST['termo']) ? trim($_POST['termo']) : ''; 
$area = isset($_POST['area']) ? trim($_POST['area']) : '';
$idPessoa = isset($_POST['idPessoa']) ? $_POST['idPessoa'] : '';

// Preparar a consulta SQL para buscar títulos com base no termo e, opcionalmente, na área, considerando o ID da pessoa
$sql = "SELECT q.Nome 
        FROM Tb_Questionarios q
        JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
        JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
        WHERE q.Nome COLLATE utf8_unicode_ci LIKE ?";

// Se a área for diferente de "todas", adicione a condição WHERE
if ($area !== 'Todas') {
    $sql .= " AND q.Area = ?";
}

// Adicione a condição para encontrar os questionários associados à pessoa especificada
$sql .= " AND e.Tb_Pessoas_Id = ?";

$sql .= " LIMIT 5";

$stmt = $_con->prepare($sql);
$likeTerm = "%" . $termo . "%";

// Vincular os parâmetros da consulta
if ($area !== 'Todas') {
    $stmt->bind_param("ssi", $likeTerm, $area, $idPessoa);
} else {
    $stmt->bind_param("si", $likeTerm, $idPessoa);
}

// Tentar executar a consulta
try {
    $stmt->execute();

    // Obter os resultados
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='sugestao-item'>" . htmlspecialchars($row['Nome'], ENT_QUOTES, 'UTF-8') . "</div>";
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