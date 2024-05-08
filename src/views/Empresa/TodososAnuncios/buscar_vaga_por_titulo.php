<?php

include "../../services/conexão_com_banco.php";

// Receba o termo de pesquisa do cliente (usando POST para evitar exposição do termo na URL)
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : null;

$nomeEmpresa = ''; // Variável inicializada como string vazia

// Preparar a consulta SQL para buscar o nome da empresa com base no ID da pessoa
$sql = "SELECT Tb_Empresa.Nome_da_Empresa 
        FROM Tb_Empresa
        WHERE Tb_Empresa.Tb_Pessoas_Id = ?";

$stmt = $_con->prepare($sql);

if ($stmt) {
    // Vincular o parâmetro da consulta
    $stmt->bind_param("i", $idPessoa);
    // Tentar executar a consulta
    try {
        $stmt->execute();
        // Obter os resultados
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Obter o nome da empresa do primeiro resultado
            $row = $result->fetch_assoc();
            $nomeEmpresa = $row['Nome_da_Empresa']; // Salvar o nome na variável
        } else {
          // ! Arrumar questão de tratamento de Erros !! 
        }
    } catch (Exception $e) {
        error_log('Erro ao buscar o nome da empresa: ' . $e->getMessage());
         // ! Arrumar questão de tratamento de Erros !! 
        
    }
    // Fechar a declaração
    $stmt->close();
}

// Consulta SQL para buscar as vagas que correspondem ao termo de pesquisa
$sql = "SELECT A.*, V.Status FROM Tb_Anuncios A 
        INNER JOIN Tb_Vagas V ON A.Id_Anuncios = V.Tb_Anuncios_Id
        WHERE A.Titulo LIKE ?";
$stmt = $_con->prepare($sql); // Prepara a consulta
$likeTerm = "%" . $termo . "%"; // Crie o padrão de pesquisa para SQL

// Vincule o parâmetro para a consulta SQL
$stmt->bind_param("s", $likeTerm);

// Execute a consulta
$stmt->execute();

// Obtenha os resultados
$result = $stmt->get_result();

// Inicialize uma variável para armazenar o HTML das vagas
$htmlVagas = '';

// Verifique se há resultados
if ($result->num_rows > 0) {
    // Se houver resultados, crie o HTML para exibir as vagas
    while ($row = $result->fetch_assoc()) {

        $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
        $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
        $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]); // "i" indica que o parâmetro é um inteiro
        $stmt_inscricoes->execute();
        $result_inscricoes = $stmt_inscricoes->get_result();
        // Verificar se a consulta teve sucesso
        if ($result_inscricoes === false) {
            // ! Arrumar questão de tratamento de Erros !! 
            $htmlVagas .=  "Erro na consulta de contagem de inscrições: " . $_con->error;
            exit;
        }
        // Obter o resultado da contagem de inscrições
        $row_inscricoes = $result_inscricoes->fetch_assoc();
        $total_inscricoes = $row_inscricoes['total_inscricoes'];
        // Exibir a vaga e o número de inscritos
        $htmlVagas .=  '<a class="postLink" href="../MinhaVaga/minhaVaga.php?id=' . $row["Id_Anuncios"] . '">';
        $htmlVagas .=  '<article class="post">';
        $htmlVagas .=  '<div class="divAcessos">';
        $htmlVagas .=  '<img src="../../../imagens/people.svg"></img>';
        $htmlVagas .=  '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
        $htmlVagas .=  '</div>';
        $htmlVagas .=  '<header>';
        switch ($row["Categoria"]) {
            case "CLT":
                $htmlVagas .=  '<img src="../../../imagens/clt.svg">';
                $htmlVagas .=  '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                break;
            case "Estágio":
            case "Jovem Aprendiz": 
                $htmlVagas .=  '<img src="../../../imagens/estagio.svg">';
                $htmlVagas .=  '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                break;
            case "PJ":
                $htmlVagas .=  '<img src="../../../imagens/pj.svg">';
                $htmlVagas .=  '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                break;
            default:
                $htmlVagas .=  '<label class="tipoVaga">Categoria não definida</label>';
                break;
        }
        $htmlVagas .=  '</header>';
        $htmlVagas .=  '<section>';
        $htmlVagas .=  '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? (strlen($row["Titulo"]) > 14 ? substr($row["Titulo"], 0, 20) . '...' : $row["Titulo"]) : "Título não definido") . '</h3>';
        $htmlVagas .=  '<p class="empresaVaga"> Empresa:' . $nomeEmpresa . '</p>';
        // Exibir o status da vaga e a data de criação
        $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
        $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
        if ($row['Status'] == 'Aberto') {
            $htmlVagas .=  '<h4 class="statusVaga" style="color:green">Aberto</h4>';
            $htmlVagas .=  '<p class="dataVaga">' . $dataCriacao . '</p>';
        } else {
            $htmlVagas .=  '<h4 class="statusVaga" style="color:red">' . $row['Status'] . '</h4>';
            $htmlVagas .=  '<p class="dataVaga">' . $datadeTermino . '</p>';
        }
        $htmlVagas .=  '</section>';
        $htmlVagas .=  '</article>';
        $htmlVagas .=  '</a>';
    
    }
} else {
    // Se não houver resultados, exiba uma mensagem indicando que não foram encontradas vagas
    $htmlVagas .= '<div class="sem-resultados">Nenhuma vaga encontrada</div>';
}

// Feche a declaração para liberar recursos
$stmt->close();

// Saída do HTML das vagas para o JavaScript
echo $htmlVagas;
?>