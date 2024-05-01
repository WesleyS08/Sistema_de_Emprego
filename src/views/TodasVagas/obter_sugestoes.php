<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php"; // Ajuste o caminho conforme necessário

// Receber o termo de busca enviado pela solicitação AJAX
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';

// Preparar a consulta SQL para buscar títulos com base no termo de busca
$sql = "SELECT Tb_Anuncios.Titulo 
        FROM Tb_Anuncios
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        WHERE Tb_Anuncios.Titulo LIKE ?
        LIMIT 3";  // Limitar o número de resultados para evitar sobrecarga

$stmt = $_con->prepare($sql); // Prepara a consulta SQL
$likeTerm = "%" . $termo . "%"; // Cria o padrão para busca com wildcards

// Vincular parâmetros para a consulta
$stmt->bind_param("s", $likeTerm); // Apenas um parâmetro do tipo string

// Execute a consulta
$stmt->execute();

// Obtenha os resultados
$result = $stmt->get_result();

// Verifique se há resultados
if ($result->num_rows > 0) {
    // Se houver resultados, exiba cada um como uma sugestão
    while ($row = $result->fetch_assoc()) {
        echo "<div class='sugestao-item'>" . htmlspecialchars($row['Titulo'], ENT_QUOTES, 'UTF-8') . "</div>"; // Escapar para prevenir XSS
    }
} else {
    // Se não houver resultados, exiba uma mensagem padrão
    echo "<div class='sugestao-item'>Nenhuma sugestão encontrada</div>";
}

// Feche a declaração para liberar recursos
$stmt->close();

// Feche a conexão para liberar recursos
$_con->close();
?>
