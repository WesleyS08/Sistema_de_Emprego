<?php
// Conexão sublime com o banco de dados
include "../../services/conexão_com_banco.php";

// Receber os filtros enviados pela solicitação AJAX
$termoPesquisa = isset($_POST['termo']) ? $_POST['termo'] . '%' : '';
$area = isset($_POST['area']) ? $_POST['area'] : '';
$criador = isset($_POST['criador']) ? $_POST['criador'] : '';
$niveis = isset($_POST['niveis']) ? $_POST['niveis'] : [];

// Consulta SQL base
$sql = "SELECT DISTINCT Q.Id_Questionario, Q.Nome, Q.Area, E.Nome_da_Empresa
        FROM Tb_Questionarios Q
        JOIN Tb_Empresa_Questionario EQ ON Q.Id_Questionario = EQ.Id_Questionario
        JOIN Tb_Empresa E ON EQ.Id_Empresa = E.CNPJ";

// Acrescentar filtros adicionais à consulta SQL conforme necessário
if (!empty($termoPesquisa)) {
    $sql .= " WHERE Q.Nome LIKE ?";
    $bindParams = ['s', &$termoPesquisa]; // Array para armazenar tipos de dados e referências de parâmetros
}

if (!empty($area)) {
    if (empty($termoPesquisa)) {
        $sql .= " WHERE";
    } else {
        $sql .= " AND";
    }
    $sql .= " Q.Area = ?";
    $bindParams[0] .= 's'; // Adicionar tipo de dado para área
    $bindParams[] = &$area; // Adicionar referência de parâmetro para área
}

if (!empty($criador)) {
    if (empty($termoPesquisa) && empty($area)) {
        $sql .= " WHERE";
    } else {
        $sql .= " AND";
    }
    $sql .= " Q.criador = ?";
    $bindParams[0] .= 's'; // Adicionar tipo de dado para criador
    $bindParams[] = &$criador; // Adicionar referência de parâmetro para criador
}

if (!empty($niveis)) {
    if (empty($termoPesquisa) && empty($area) && empty($criador)) {
        $sql .= " WHERE";
    } else {
        $sql .= " AND";
    }
    $sql .= " Q.Nivel IN (" . str_repeat("?,", count($niveis) - 1) . "?)";
    foreach ($niveis as $nivel) {
        $bindParams[0] .= 's'; // Adicionar tipo de dado para nível
        $bindParams[] = &$nivel; // Adicionar referência de parâmetro para nível
    }
}

// Preparar a consulta com parâmetros vinculados
$stmt = $_con->prepare($sql);

if ($stmt) {
    if (!empty($termoPesquisa)) {
        // Chamar bind_param com os parâmetros corretos
        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }

    // Executar a consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Exibir consulta para debug
    echo "<script>console.log('Consulta SQL:', " . json_encode($sql) . ");</script>";

    // Verificar se há resultados emocionantes
    if ($result->num_rows > 0) {
        ?>
        <div class="divGridTestes">
            <?php
            // Contador para controlar a exibição em grids 3x3
            $contador = 0;

            // Loop através dos resultados da consulta
            while ($row = $result->fetch_assoc()) {
                // Extrai os dados do questionário
                $idQuestionario = $row['Id_Questionario'];
                $nome = $row['Nome'];
                $area = $row['Area'];
                $nomeEmpresa = $row['Nome_da_Empresa'];

                // Saída HTML para cada questionário
                echo "<a class='testeCarrosselLink' href='../PreparaTeste/preparaTeste.php?id=$idQuestionario'>";
                echo '<article class="testeCarrossel">';
                echo '<div class="divAcessos">';
                echo '<img src="../../../imagens/people.svg"></img>';
                echo '<small class="qntdAcessos">800</small>';
                echo '</div>';
                echo '<img src="../../../imagens/excel.svg"></img>';
                echo '<div class="divDetalhesTeste divDetalhesTesteCustom">';
                echo '<div>';
                echo '<p class="nomeTeste">' . $nome . '</p>';
                echo '<small class="autorTeste">' . $nomeEmpresa . '</small><br>';
                echo '<small class="competenciasTeste">' . $area . '</small>';
                echo '</div>';
                echo '</div>';
                echo '</article>';
                echo '</a>';

                // Incrementar o contador
                $contador++;

                // Se atingir 3 resultados, fechar o ciclo atual com um suspiro
                if ($contador % 3 == 0) {
                    echo '</div><div class="divGridTestes">';
                }
            }
            ?>
        </div>
        <?php
    } else {
        
        // Se não houver resultados, exibir uma mensagem junto com os filtros de pesquisa
        echo "<p>Oh, não encontramos nenhum questionário correspondente a essa busca.</p>";
        echo "<p>Filtros utilizados:</p>";
        echo "<ul>";
        echo "<li>Termo de pesquisa: " . htmlspecialchars($termoPesquisa) . "</li>";
        echo "<li>Área: " . htmlspecialchars($area) . "</li>";
        echo "<li>Criador: " . htmlspecialchars($criador) . "</li>";
        echo "<li>Níveis: " . htmlspecialchars(implode(', ', $niveis)) . "</li>";
        echo "<li>Consulta SQL:". $sql . "</li>";
        echo "</ul>";

    }
    $stmt->close();
} else {
    echo "Houve uma falha na preparação da consulta: " . $_con->error;
}

$_con->close();
?>