<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php";

// Receber o termo de busca e a área enviados pela solicitação AJAX
$termo = isset($_POST['termo']) ? trim($_POST['termo']) : '';  // Limpar espaços em branco
$area = isset($_POST['area']) ? trim($_POST['area']) : '';

// Preparar a consulta SQL para buscar títulos com base no termo e na área
$sql = "SELECT Tb_Anuncios.Titulo 
        FROM Tb_Anuncios
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        WHERE LOWER(REGEXP_REPLACE(Tb_Anuncios.Titulo, '\\b(de|em|para|com|por|sem)\\b', '')) LIKE LOWER(?)
        AND (? = 'Todas' OR LOWER(Tb_Anuncios.Area) = LOWER(?))
        LIMIT 3";

// Limitar o número de resultados
$stmt = $_con->prepare($sql);
$likeTerm = "%" . $termo . "%";

// Vincular os parâmetros da consulta
$stmt->bind_param("sss", $likeTerm, $area, $area);  // Passar os parâmetros

// Executar a consulta
$stmt->execute();

// Obter o resultado da consulta
$result = $stmt->get_result();

// Inicializa um array para armazenar as sugestões
$sugestoes = array();

try {
    // Verificar se há resultados
    if ($result->num_rows > 0) {
        // Iterar sobre os resultados e adicionar cada sugestão ao array
        while ($row = $result->fetch_assoc()) {
            // Adicionar o título do anúncio como sugestão
            $sugestoes[] = htmlspecialchars($row['Titulo'], ENT_QUOTES, 'UTF-8');
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