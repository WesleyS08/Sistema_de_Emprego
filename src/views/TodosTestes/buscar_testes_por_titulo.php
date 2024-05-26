<?php
include "../../services/conexão_com_banco.php";

// Receber o termo de pesquisa e o ID da pessoa usando POST
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : 0;
$tema = 0;

if ($idPessoa > 0) {
    // Consulta para selecionar o tema salvo no banco de dados
    $query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt = $_con->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $idPessoa);
        $stmt->execute();
        $result = $stmt->get_result();
        $tema = $result ? ($result->fetch_assoc()['Tema'] ?? null) : null;
        $stmt->close();
    } else {
        die("Erro ao preparar a query: " . $_con->error);
    }
}



// Consulta SQL base
$sql = "SELECT DISTINCT q.*, e.Nome_da_Empresa
        FROM Tb_Questionarios q
        LEFT JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
        LEFT JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
        WHERE 1=1"; 

// Acrescentar filtros adicionais à consulta SQL conforme necessário
$bindParams = array();
if (!empty($termoPesquisa)) {
    $sql .= " AND q.Nome LIKE ?";
    $bindParams[] = '%' . $termoPesquisa . '%'; // Adicionar parâmetro de termo de pesquisa
}

// Preparar a consulta com parâmetros vinculados
$stmt = $pdo->prepare($sql);

if ($stmt) {
    // Chamar bind_param com os parâmetros corretos
    $stmt->execute($bindParams);

    // Exibir consulta para debug
    echo "<script>console.log('Consulta SQL:', " . json_encode($sql) . ");</script>";

    // Verificar se há resultados emocionantes
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        // Loop através dos resultados da consulta
        foreach ($result as $row) {
            $idQuestionario = $row['Id_Questionario'];
            $nome = $row['Nome'];
            $area = $row['Area'];
            $nomeEmpresa = $row['Nome_da_Empresa'];
            $ImagemQuestionario = $row['ImagemQuestionario']; // Nova variável para a imagem do questionário

            // Consulta para contar respostas
            $sql_contar_respostas = "SELECT COUNT(DISTINCT Tb_Candidato_CPF) AS total_pessoas FROM Tb_Resultados WHERE Tb_Questionarios_ID = ?";
            $stmt_contar_respostas = $pdo->prepare($sql_contar_respostas);
            $stmt_contar_respostas->execute([$idQuestionario]);
            $row_respostas = $stmt_contar_respostas->fetch(PDO::FETCH_ASSOC);
            $total_pessoas = $row_respostas['total_pessoas'];


             // Determinar a cor do nome da empresa com base no tema
    $cor_nome_empresa = ($tema == 'noturno') ? "#FFFFFF" : "#000000"; // Branco para tema noturno, preto para tema padrão
    
            // Saída HTML para cada questionário
            echo "<a class='testeCarrosselLink' href='../PreparaTeste/preparaTeste.php?id=$idQuestionario'>";
            echo '<article class="testeCarrossel">';
            echo '<div class="divAcessos">';
            if ($tema == 'noturno') {
                echo '<img src="../../assets/images/icones_diversos/peopleWhite.svg"></img>';
            } else {
                echo '<img src="../../assets/images/icones_diversos/people.svg"></img>';
            }
            echo '<small class="qntdAcessos">' . $total_pessoas . '</small>';
            echo '</div>';
            echo '<img class="imgTeste" src="' . $ImagemQuestionario . '"></img>';
            echo '<div class="divDetalhesTeste divDetalhesTesteCustom">';
            echo '<div>';
            echo '<p class="nomeTeste">' . $nome . '</p>';
            echo '<small class="autorTeste">' . $nomeEmpresa . '</small><br>';
            echo '<small class="competenciasTeste">' . $area . '</small>';
            echo '</div>';
            echo '</div>';
            echo '</article>';
            echo '</a>';
        }
    } else {
        echo "<p class='infos' style='text-align:center; margin:0 auto; position: absolute'>Nenhum Teste encontrado com os filtros selecionados.</p>";
    }
} else {
    echo "Houve uma falha na preparação da consulta: " . $pdo->errorInfo()[2];
}

$pdo = null; // Fechar conexão PDO
?>