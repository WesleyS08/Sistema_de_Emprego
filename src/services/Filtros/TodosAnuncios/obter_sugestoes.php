<?php

include '../../conexão_com_banco.php';


// Receber o termo de busca e a área enviados pela solicitação AJAX
$termo = isset($_POST['termo']) ? trim($_POST['termo']) : '';  // Limpar espaços em branco
$area = isset($_POST['area']) ? trim($_POST['area']) : '';

// Preparar a consulta SQL para buscar títulos com base no termo e na área
$sql = "SELECT Tb_Anuncios.Titulo 
        FROM Tb_Anuncios
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        WHERE Tb_Anuncios.Titulo LIKE ?
          AND (? = 'Todas' OR Tb_Anuncios.Area = ?)
        LIMIT 3";  // Limitar o número de resultados

$stmt = $_con->prepare($sql);
$likeTerm = "%" . $termo . "%";

// Vincular os parâmetros da consulta
$stmt->bind_param("sss", $likeTerm, $area, $area);  // Passar os parâmetros

// Tentar executar a consulta
try {
    $stmt->execute();

    // Obter os resultados
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='sugestao-item'>" . htmlspecialchars($row['Titulo'], ENT_QUOTES, 'UTF-8') . "</div>";
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
