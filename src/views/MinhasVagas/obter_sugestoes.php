<?php
// Inclua a conexão com o banco de dados para usar na busca
include "../../services/conexão_com_banco.php"; // Atualize o caminho conforme necessário

// Receba o termo de pesquisa do cliente (usando POST para evitar exposição do termo na URL)
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';


$sql = "SELECT Titulo FROM Tb_Anuncios WHERE Titulo LIKE ? LIMIT 3"; // Limite para evitar muitas sugestões
$stmt = $_con->prepare($sql); // Prepara a consulta
$likeTerm = "%" . $termo . "%"; // Crie o padrão de pesquisa para SQL

// Vincule o parâmetro para a consulta SQL
$stmt->bind_param("s", $likeTerm);

// Execute a consulta
$stmt->execute();

// Obtenha os resultados
$result = $stmt->get_result();

// Verifique se há resultados
if ($result->num_rows > 0) {
    // Se houver resultados, exiba cada um como uma sugestão
    while ($row = $result->fetch_assoc()) {
        echo "<div class='sugestao-item'>" . htmlspecialchars($row['Titulo'], ENT_QUOTES, 'UTF-8') . "</div>"; // Exiba a sugestão e escape para evitar ataques XSS
    }
} else {
    // Se não houver resultados, exiba uma mensagem padrão
    echo "<div class='sugestao-item'>Nenhuma sugestão</div>";
}

// Feche a declaração para liberar recursos
$stmt->close();
?>
