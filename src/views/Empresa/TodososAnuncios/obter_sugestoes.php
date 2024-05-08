<?php
// Inclua a conexão com o banco de dados para usar na busca
include "../../services/conexão_com_banco.php"; // Atualize o caminho conforme necessário

$termo = isset($_POST['termo']) ? trim($_POST['termo']) : '';
$area = isset($_POST['area']) ? trim($_POST['area']) : 'Todas'; // Considerando 'Todas' como valor padrão
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : 0; // Defina um padrão para o ID da pessoa

// Preparar a consulta SQL para buscar títulos com base no termo, na área e no ID da pessoa
$sql = "
    SELECT Tb_Anuncios.Titulo 
    FROM Tb_Anuncios
    JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
    JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
    WHERE Tb_Anuncios.Titulo LIKE ?
      AND (? = 'Todas' OR Tb_Anuncios.Area = ?)
      AND Tb_Empresa.Tb_Pessoas_Id = ?
    LIMIT 3"; // Limitar o número de resultados

$stmt = $_con->prepare($sql);

// Vincular os parâmetros da consulta
$likeTerm = "%" . $termo . "%";
$stmt->bind_param("sssi", $likeTerm, $area, $area, $idPessoa); // Adicionando o ID da pessoa como quarto parâmetro

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