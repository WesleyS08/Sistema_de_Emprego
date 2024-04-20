<?php
include "../../services/conexão_com_banco.php";

// Receber os filtros enviados pela solicitação AJAX
$area = isset($_POST['area']) ? $_POST['area'] : 'Todas';
$tipos = isset($_POST['tipos']) ? $_POST['tipos'] : array();
$vagasAbertas = isset($_POST['vagasAbertas']) ? $_POST['vagasAbertas'] : 'false';
$termoPesquisa = isset($_POST['termo']) ? $_POST['termo'] : '';

// Construir a consulta SQL baseada nos filtros
$sql = "SELECT * FROM Tb_Anuncios 
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
        WHERE 1";

// Adicionar filtro de área, se selecionado
if ($area != 'Todas') {
    $sql .= " AND Tb_Anuncios.Area = '$area'";
}

// Adicionar filtro de tipos, se selecionado
if (!empty($tipos)) {
    $tiposString = implode("','", $tipos);
    $sql .= " AND Tb_Anuncios.Categoria IN ('$tiposString')";
}

// Adicionar filtro de vagas abertas, se selecionado
if ($vagasAbertas == 'true') {
    $sql .= " AND Tb_Vagas.Status = 'Aberto'";
}

// Adicionar filtro de termo de pesquisa por título
if (!empty($termoPesquisa)) {
    $sql .= " AND Tb_Anuncios.Titulo LIKE '%$termoPesquisa%'";
}

// Executar a consulta
$result = $_con->query($sql);

// Verificar se a consulta teve sucesso
if ($result) {
    // Construir o HTML das vagas com base nos resultados da consulta
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            // Consulta para contar o número de inscritos para esta vaga
            $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
            $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
            $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]); // "i" indica que o parâmetro é um inteiro
            $stmt_inscricoes->execute();
            $result_inscricoes = $stmt_inscricoes->get_result();

            // Verificar se a consulta teve sucesso
            if ($result_inscricoes === false) {
                // Tratar o erro, se necessário
                echo "Erro na consulta de contagem de inscrições: " . $_con->error;
                exit;
            }

            // Obter o resultado da contagem de inscrições
            $row_inscricoes = $result_inscricoes->fetch_assoc();
            $total_inscricoes = $row_inscricoes['total_inscricoes'];

            // HTML para cada vaga
            echo '<a class="postLink" href="../MinhaVaga/minhaVaga.php?id=' . $row["Id_Anuncios"] . '">';
            echo '<article class="post">';
            echo '<div class="divAcessos">';
            echo '<img src="../../../imagens/people.svg"></img>';
            echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
            echo '</div>';

            echo '<header>';
            switch ($row["Categoria"]) {
                case "CLT":
                    echo '<img src="../../../imagens/clt.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                case "Estágio":
                case "Jovem Aprendiz": // Caso tenham a mesma aparência visual
                    echo '<img src="../../../imagens/estagio.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                case "PJ":
                    echo '<img src="../../../imagens/pj.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                default:
                    echo '<label class="tipoVaga">Categoria não definida</label>';
                    break;
            }
            echo '</header>';
            
            echo '<section>';
            echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';
            echo '<p class="empresaVaga">' . (isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida") . '</p>';
            // Exibir o status da vaga e a data de criação
            $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
            $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
            if ($row['Status'] == 'Aberto') {
                echo '<p style="color: green;">' . $row['Status'] . '</p>';
                echo '<p class="dataVaga">' . $dataCriacao . '</p>';
            } else {
                echo '<p style="color: red;">' . $row['Status'] . '</p>';
                echo '<p class="dataVaga">' . $datadeTermino . '</p>';
            }
            echo '</section>';
            
            echo '</article>';
            echo '</a>';
        }
    } else {
        // Caso não haja resultados para os filtros selecionados
        echo "Nenhuma vaga encontrada com os filtros selecionados.";
    }
} else {
    // Caso ocorra algum erro na consulta
    echo "Erro na consulta: " . $_con->error;
}
?>
