<?php
// Inclua a conexão com o banco de dados para usar na busca
include "../../services/conexão_com_banco.php"; // Atualize o caminho conforme necessário

// Receber o termo de busca e o ID da pessoa enviados pela solicitação AJAX
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : null; // Certifique-se de que é um valor válido

// Se não houver ID da pessoa, retorne um erro ou uma mensagem de falha
if (is_null($idPessoa)) {
    echo "<div class='sugestao-item'>ID da pessoa não fornecido</div>";
    exit;
}

// Prepare a consulta SQL com o filtro do ID da pessoa e o termo de busca
$sql = "SELECT Tb_Anuncios.Titulo 
        FROM Tb_Anuncios
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        WHERE Tb_Empresa.Tb_Pessoas_Id = ? 
          AND Tb_Anuncios.Titulo LIKE ?
        LIMIT 3";  // Limite para evitar muitas sugestões

$stmt = $_con->prepare($sql); // Prepara a consulta
$likeTerm = "%" . $termo . "%"; // Cria o padrão de pesquisa para SQL

// Vincular parâmetros para a consulta SQL
$stmt->bind_param("si", $likeTerm, $idPessoa); // "s" para string e "i" para inteiro

// Execute a consulta
$stmt->execute();

// Obtenha os resultados
$result = $stmt->get_result();

// Verifique se há resultados
if ($result->num_rows > 0) {
    // Se houver resultados, exiba cada um como uma sugestão
    while ($row = $result->fetch_assoc()) {
        echo "<div class='sugestao-item'>" . htmlspecialchars($row['Titulo'], ENT_QUOTES, 'UTF-8') . "</div>"; // Exibe a sugestão e escape para evitar ataques XSS
    }
} else {
    // Se não houver resultados, exiba uma mensagem padrão
    echo "<div class='sugestao-item'>Nenhuma sugestão</div>";
}

// Feche a declaração para liberar recursos
$stmt->close();
?>