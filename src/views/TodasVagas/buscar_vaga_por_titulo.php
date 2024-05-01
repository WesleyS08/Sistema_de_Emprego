<?php
// Inclua a conexão com o banco de dados
include "../../services/conexão_com_banco.php";

// Receber o termo de pesquisa do cliente usando POST para evitar exposição na URL
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';

// Consulta SQL para buscar vagas que correspondem ao termo de pesquisa
$sql = "SELECT A.*, V.Status 
        FROM Tb_Anuncios A 
        INNER JOIN Tb_Vagas V ON A.Id_Anuncios = V.Tb_Anuncios_Id
        WHERE A.Titulo LIKE ?
        LIMIT 10"; // Limitar para evitar sobrecarga no sistema

$stmt = $_con->prepare($sql); // Prepara a consulta SQL
$likeTerm = "%" . $termo . "%"; // Criar o padrão de pesquisa para o SQL

// Vincular o parâmetro para a consulta SQL
$stmt->bind_param("s", $likeTerm);

// Execute a consulta
$stmt->execute();

// Obtenha os resultados da consulta
$result = $stmt->get_result();

// Inicialize uma variável para armazenar o HTML das vagas
$htmlVagas = '';

// Verifique se há resultados na consulta
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Contar o número de inscrições para cada vaga
        $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes 
                                  FROM Tb_Inscricoes 
                                  WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
        $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
        $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]); // "i" para inteiro
        $stmt_inscricoes->execute();
        $result_inscricoes = $stmt_inscricoes->get_result();
        $total_inscricoes = $result_inscricoes->fetch_assoc()['total_inscricoes'] ?? 0;

        // Crie o HTML para cada vaga
        $htmlVagas .= '<a class="postLink" href="../MinhaVaga/minhaVaga.php?id=' . $row["Id_Anuncios"] . '">';
        $htmlVagas .= '<article class="post">';
        $htmlVagas .= '<div class="divAcessos">';
        $htmlVagas .= '<img src="../../../imagens/people.svg"></img>';
        $htmlVagas .= '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
        $htmlVagas .= '</div>';

        // Mostrar a categoria da vaga
        $htmlVagas .= '<header>';
        switch ($row["Categoria"]) {
            case "CLT":
                $htmlVagas .= '<img src="../../../imagens/clt.svg">';
                $htmlVagas .= '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                break;
            case "Estágio":
            case "Jovem Aprendiz":
                $htmlVagas .= '<img src="../../../imagens/estagio.svg">';
                $htmlVagas .= '<label a classe "tipoVaga">' . $row["Categoria"] . '</label>';
                break;
            case "PJ":
                $htmlVagas .= '<img src "../../../imagens/pj.svg">';
                $htmlVagas .= '<label a classe "tipoVaga">' . $row["Categoria"] . '</label>';
                break;
            default:
                $htmlVagas .= '<label a classe "tipoVaga">Categoria não definida</label>';
                break;
        }
        $htmlVagas .= '</header>';

        // Mostrar detalhes da vaga e seu status
        $htmlVagas .= '<section>';
        $htmlVagas .= '<h3 class="nomeVaga">' . htmlspecialchars($row["Titulo"], ENT_QUOTES, 'UTF-8') . '</h3>';
        $htmlVagas .= '<p class="empresaVaga">' . htmlspecialchars($row["Descricao"], ENT_QUOTES, 'UTF-8') . '</p>';
        
        // Mostrar o status da vaga e as datas de criação e término
        $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
        $dataTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
        
        if (isset($row['Status'])) {
            if ($row['Status'] == 'Aberto') {
                $htmlVagas .= '<p style="color: green;">' . htmlspecialchars($row['Status'], ENT_QUOTES, 'UTF-8') . '</p>';
                $htmlVagas .= '<p class="dataVaga">' . htmlspecialchars($dataCriacao, ENT_QUOTES, 'UTF-8') . '</p>';
            } else {
                $htmlVagas .= '<p style="color: red;">' . htmlspecialchars($row['Status'], ENT_QUOTES, 'UTF-8') . '</p>';
                $htmlVagas .= '<p class="dataVaga">' . htmlspecialchars($dataTermino, ENT_QUOTES, 'UTF-8') . '</p>';
            }
        } else {
            $htmlVagas .= '<p>Status não definido</p>';
        }
        $htmlVagas .= '</section>';
        
        $htmlVagas .= '</article>';
        $htmlVagas .= '</a>';
    }
} else {
    // Se não houver resultados, mostrar uma mensagem
    $htmlVagas .= '<div class="sem-resultados">Nenhuma vaga encontrada</div>';
}

// Fechar a declaração para liberar recursos
$stmt->close();

// Saída do HTML das vagas para o JavaScript
echo $htmlVagas;

// Fechar a conexão para liberar recursos
$_con->close();
?>
